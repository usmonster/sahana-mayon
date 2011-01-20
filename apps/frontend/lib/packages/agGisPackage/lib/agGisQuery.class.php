<?php

/**
 * agGisQuery this class defines gis related queries.
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agGisQuery
{
  /**
   * Builds a query to find addresses with missing geo info.
   * 
   * @param string $entityType - Specifies the type of entity for address id search.  Currenlty, this method has entity type staff and facility defined.
   * @return string A query string.
   */
  public static function missingGis($entityType)
  {
    $addressArray = array();

    $query = Doctrine_Query::create()
      ->select('a.id, ea.id, ag.id, g.id, gf.id')
      ->from('agAddress a')
      ->innerJoin('a.agEntityAddressContact ea')
      ->leftJoin('a.agAddressGeo ag')
      ->leftJoin('ag.agGeo g')
      ->leftJoin('g.agGeoFeature gf')
      ->innerJoin('ea.agEntity e')
      ->where('gf.geo_coordinate_id is null');

    switch ($entityType)
    {
      case 'staff':
        $query->innerJoin('e.agPerson p')
          ->innerJoin('p.agStaff stf');
        break;

      case 'facility':
        $query->innerJoin('e.agSite si')
          ->innerJoin('p.agFacility fac');
        break;

      default:
        throw new sfException('An error occurred. Please pass in an accepted parameter.');
    }

    return $query;
  }

  /**
   * Builds a query to return geo_id1 and geo_id2 in the geo relationship table.
   *
   * @param none
   * @return string A query string.
   */
  public static function returnExistingGeoRelation()
  {
    $query = Doctrine_Query::create()
      ->select('geo_id1, geo_id2, c2.longitude, c2.latitude, c1.longitude, c1.latitude, f1.id, f2.id, c1.id, c2.id')
      ->from('agGeoRelationship r')
      ->addFrom('agGeoFeature f1')
      ->addFrom('agGeoFeature f2')
      ->addFrom('agGeoCoordinate c1')
      ->addFrom('agGeoCoordinate c2')
      ->where('r.geo_id1=f1.geo_id')
      ->andWhere('f1.geo_coordinate_id=c1.id')
      ->andWhere('r.geo_id2=f2.geo_id')
      ->andWhere('f2.geo_coordinate_id=c2.id')
      ->orderBy('geo_id1, geo_id2');

    return $query;
  }

  /**
   * In the geo relationship table, geo_id1 captures person type geo and geo_id2 captures site type geo.
   *
   * @param boolean $countRecords Optional. By default, it is set to false.  Thus, returns the set of undefined geo relation in an array.  If passed-in as true, method will return the total record count of undefined geo relation.
   * @param string $leftRelation Optional. By default, it is set to 'staff'.  This specifies the query condition to search for one of the component objects with a geo relation as defined in $queryStaticClauses.
   * @param string $rightRelation Optional.  By default, it is set to 'facility'.   This specifies the query condition to search for one of the component objects with a geo relation as defined in $queryStaticClauses.
   * @return int|array Depending on the pass in param, this method can return either the total record count of undefined geo relation or returns an array of undefined geo relations as a form of array[geo id1] = geo id2.
   * @todo It would be nice if this could be made flexible enough to relate elements beyond just persons and sites. Functionally, it shouldn't be too hard to objectify but the larger issue is how to pass other necessary variables like event and scenario id's.
   */
  public static function findUnrelatedGeo($countRecords = TRUE, $leftRelation = 'staff', $rightRelation = 'facility')
  {

    /**
     * This little magic array handles all of our joins for the right and left parameters
     * @todo this should really be a pre-defined class so we don't have to keep writing nested array stuff
     */
    $queryStaticClauses = array() ;
    // tier 0 (eg, entity)
    $queryStaticClauses['entity']['from']= 'INNER JOIN ag_address_geo AS ag ON g.id = ag.geo_id
      INNER JOIN ag_address AS a ON a.id = ag.address_id
      INNER JOIN ag_entity_address_contact AS eac ON eac.address_id = a.id
      INNER JOIN ag_entity AS e ON eac.entity_id = e.id' ;
    // tier 1
    $queryStaticClauses['person']['from']= $queryStaticClauses['entity']['from'] . ' ' . 'INNER JOIN ag_person AS p ON e.id = p.entity_id' ;
    $queryStaticClauses['site']['from']= $queryStaticClauses['entity']['from'] . ' ' . 'INNER JOIN ag_site AS s ON e.id = s.entity_id' ;
    // tier 2
    $queryStaticClauses['staff']['from'] = $queryStaticClauses['person']['from'] . ' ' . 'INNER JOIN ag_staff AS stf ON p.id = stf.person_id' ;
    $queryStaticClauses['facility']['from']= $queryStaticClauses['site']['from'] . ' ' . 'INNER JOIN ag_facility AS fac ON s.id = fac.site_id' ;

    $baseFilterSubquery = 'SELECT DISTINCT g.id FROM ag_geo AS g' ;

    // build the left relation filtering subquery
    $leftFilterSubquery = $baseFilterSubquery . ' ' . $queryStaticClauses[$leftRelation]['from'] ;

    // build the right relation filtering subquery
    $rightFilterSubquery = $baseFilterSubquery . ' ' . $queryStaticClauses[$rightRelation]['from'] ;

    /**
     * This content block establishes a core query against which the other components of the geo relation may be joined.
     * @todo Remove the geo-coordinate count blocks and replace them with a centroid calculation aggregate function. This is, currently, a hack to prevent geo features with more than one coordinate from returning.
     */
    $queryBaseInnerSelect = 'SELECT g.id
          FROM ag_geo AS g
            INNER JOIN ag_geo_feature AS f
              ON g.id = f.geo_id
            INNER JOIN ag_geo_coordinate AS c
              ON f.geo_coordinate_id = c.id' ;

    $queryBaseInnerGroupBy = 'GROUP BY g.id HAVING COUNT(c.id) = 1' ;

    // using the base inner query, we combine our right and left queries with their filtering subqueries
    // @todo this could totally be a tiny function
    // @todo check whether or not the caching of derived tables FROM (SELECT...) would be more efficient than the ref join below
    $leftQuery = $queryBaseInnerSelect . ' ' . 'INNER JOIN (' . $leftFilterSubquery . ') AS lf ON g.id = lf.id' . ' ' . $queryBaseInnerGroupBy ;
    $rightQuery = $queryBaseInnerSelect . ' ' . 'INNER JOIN (' . $rightFilterSubquery . ') AS rf ON g.id = rf.id' . ' ' . $queryBaseInnerGroupBy ;

    // build our base cartesian query
    $queryCartesianSelect = 'SELECT
      geo1.id AS geo1_id,
      geo2.id AS geo2_id' ;

    $queryCartesianWhere = 'WHERE geo1.id != geo2.id' ;
    
    $queryCartesianFrom = 'FROM (' . $leftQuery . ') AS geo1' ;
    $queryCartesianFrom = $queryCartesianFrom . ', (' . $rightQuery . ') AS geo2' ;

    $queryCartesian = $queryCartesianSelect . ' ' . $queryCartesianFrom . ' ' . $queryCartesianWhere ;

    $queryOuter = 'SELECT
        qc.geo1_id,
        qc.geo2_id,
        gc1.longitude AS geo1_longitude,
        gc1.latitude AS geo1_latitude,
        gc2.longitude AS geo2_longitude,
        gc2.latitude AS geo2_latitude
      FROM (' . $queryCartesian . ') AS qc
        INNER JOIN ag_geo AS g1
          ON qc.geo1_id = g1.id
        INNER JOIN ag_geo AS g2
          ON qc.geo2_id = g2.id
        INNER JOIN ag_geo_feature AS gf1
          ON g1.id = gf1.geo_id
        INNER JOIN ag_geo_feature AS gf2
          ON g2.id = gf2.geo_id
        INNER JOIN ag_geo_coordinate AS gc1
          ON gf1.geo_coordinate_id = gc1.id
        INNER JOIN ag_geo_coordinate AS gc2
          ON gf2.geo_coordinate_id = gc2.id
        LEFT JOIN ag_geo_relationship AS agr
          ON qc.geo1_id = agr.geo_id1 AND qc.geo2_id = agr.geo_id2
      WHERE agr.id IS NULL' ;
    
    $query = $queryOuter ;

    $conn = Doctrine_Manager::connection();
    $pdo = $conn->execute($query);
    $pdo->setFetchMode(Doctrine_Core::FETCH_ASSOC);
    $result = $pdo->fetchAll();

    // -- TEST BLOCK / REMOVE LATER --
    echo $query . '<br><br>';
    print_r($result);



    // if countRecord is true, return only the total record count for undefined geo relations.  Otherwise, return the undefined geo relation set.
    return ($countRecords ? count($result) : $result);
  }

  /**
   * This is a MySQL-optimized version of findUnrelatedGeo that takes advantage of MySQL's fast in-memory temporary tables and STRAIGHT_JOIN condition
   *
   * @param boolean $countRecords Optional. By default, it is set to false.  Thus, returns the set of undefined geo relation in an array.  If passed-in as true, method will return the total record count of undefined geo relation.
   * @param string $leftRelation Optional. By default, it is set to 'staff'.  This specifies the query condition to search for one of the component objects with a geo relation as defined in $queryStaticClauses.
   * @param string $rightRelation Optional.  By default, it is set to 'facility'.   This specifies the query condition to search for one of the component objects with a geo relation as defined in $queryStaticClauses.
   * @return int|array Depending on the pass in param, this method can return either the total record count of undefined geo relation or returns an array of undefined geo relations as a form of array[geo id1] = geo id2.
   * @todo It would be nice if this could be made flexible enough to relate elements beyond just persons and sites. Functionally, it shouldn't be too hard to objectify but the larger issue is how to pass other necessary variables like event and scenario id's.
   * @todo For this to work properly and without danger to the system, this needs to be encapsulated with exception handlers that do garbage cleanup
   */
  public static function findUnrelatedGeoMySQL($countRecords = FALSE, $leftRelation = 'staff', $rightRelation = 'facility')
  {

    /*
     * This little magic array handles all of our joins for the right and left parameters
     * @todo this should really be a pre-defined class so we don't have to keep writing nested array stuff
     */
    $queryStaticClauses = array() ;
    // tier 0 (eg, entity)
    $queryStaticClauses['entity']['from']= 'INNER JOIN ag_address_geo AS ag ON g.id = ag.geo_id
      INNER JOIN ag_address AS a ON a.id = ag.address_id
      INNER JOIN ag_entity_address_contact AS eac ON eac.address_id = a.id
      INNER JOIN ag_entity AS e ON eac.entity_id = e.id' ;
    // tier 1
    $queryStaticClauses['person']['from']= $queryStaticClauses['entity']['from'] . ' ' . 'INNER JOIN ag_person AS p ON e.id = p.entity_id' ;
    $queryStaticClauses['site']['from']= $queryStaticClauses['entity']['from'] . ' ' . 'INNER JOIN ag_site AS s ON e.id = s.entity_id' ;
    // tier 2
    $queryStaticClauses['staff']['from'] = $queryStaticClauses['person']['from'] . ' ' . 'INNER JOIN ag_staff AS stf ON p.id = stf.person_id' ;
    $queryStaticClauses['facility']['from']= $queryStaticClauses['site']['from'] . ' ' . 'INNER JOIN ag_facility AS fac ON s.id = fac.site_id' ;

    $baseFilterSubquery = 'SELECT DISTINCT g.id FROM ag_geo AS g' ;

    // build the left relation filtering subquery
    $leftFilterSubquery = $baseFilterSubquery . ' ' . $queryStaticClauses[$leftRelation]['from'] ;

    // build the right relation filtering subquery
    $rightFilterSubquery = $baseFilterSubquery . ' ' . $queryStaticClauses[$rightRelation]['from'] ;

    /*
     * This content block establishes a core query against which the other components of the geo relation may be joined.
     * @todo Remove the geo-coordinate count blocks and replace them with a centroid calculation aggregate function. This is, currently, a hack to prevent geo features with more than one coordinate from returning.
     */
    $queryBaseInnerSelect = 'SELECT g.id
          FROM ag_geo AS g
            INNER JOIN ag_geo_feature AS f
              ON g.id = f.geo_id
            INNER JOIN ag_geo_coordinate AS c
              ON f.geo_coordinate_id = c.id' ;

    $queryBaseInnerGroupBy = 'GROUP BY g.id HAVING COUNT(c.id) = 1' ;

    // using the base inner query, we combine our right and left queries with their filtering subqueries
    // @todo this could totally be a tiny function
    // @todo check whether or not the caching of derived tables FROM (SELECT...) would be more efficient than the ref join below
    $leftQuery = $queryBaseInnerSelect . ' ' . 'STRAIGHT_JOIN (' . $leftFilterSubquery . ') AS lf ON lf.id = g.id' . ' ' . $queryBaseInnerGroupBy ;
    $rightQuery = $queryBaseInnerSelect . ' ' . 'STRAIGHT_JOIN (' . $rightFilterSubquery . ') AS rf ON rf.id = g.id' . ' ' . $queryBaseInnerGroupBy ;

    // build our base cartesian query
    $queryCartesianSelect = 'SELECT
      geo1.id AS geo1_id,
      geo2.id AS geo2_id' ;

    $queryCartesianWhere = 'WHERE geo1.id != geo2.id' ;

    $queryCartesianFrom = 'FROM (' . $leftQuery . ') AS geo1' ;
    $queryCartesianFrom = $queryCartesianFrom . ', (' . $rightQuery . ') AS geo2' ;

    $queryCartesian = $queryCartesianSelect . ' ' . $queryCartesianFrom . ' ' . $queryCartesianWhere ;

    $queryOuter = 'SELECT
        qc.geo1_id,
        qc.geo2_id,
        gc1.longitude AS geo1_longitude,
        gc1.latitude AS geo1_latitude,
        gc2.longitude AS geo2_longitude,
        gc2.latitude AS geo2_latitude
      FROM (' . $queryCartesian . ') AS qc
        STRAIGHT_JOIN ag_geo AS g1
          ON qc.geo1_id = g1.id
        STRAIGHT_JOIN ag_geo AS g2
          ON qc.geo2_id = g2.id
        INNER JOIN ag_geo_feature AS gf1
          ON g1.id = gf1.geo_id
        INNER JOIN ag_geo_feature AS gf2
          ON g2.id = gf2.geo_id
        INNER JOIN ag_geo_coordinate AS gc1
          ON gf1.geo_coordinate_id = gc1.id
        INNER JOIN ag_geo_coordinate AS gc2
          ON gf2.geo_coordinate_id = gc2.id
        LEFT JOIN ag_geo_relationship AS agr
          ON qc.geo1_id = agr.geo_id1 AND qc.geo2_id = agr.geo_id2
      WHERE agr.id IS NULL' ;

    $query = $queryOuter ;
    $conn = Doctrine_Manager::connection();
    $pdo = $conn->execute($query);
    $pdo->setFetchMode(Doctrine_Core::FETCH_ASSOC);
    $result = $pdo->fetchAll();

    // -- TEST BLOCK / REMOVE LATER --
    echo $query . '<br><br>';
    print_r($result);



    // if countRecord is true, return only the total record count for undefined geo relations.  Otherwise, return the undefined geo relation set.
    return ($countRecords ? count($result) : $result);
  }
}


