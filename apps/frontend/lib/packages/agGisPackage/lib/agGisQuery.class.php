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
   * In the geo relationship table, geo_id1 captures person type geo and geo_id2 captures site type geo.  Geo_id1 < geo_id2.
   *
   * @param boolean $countRecords Optional. By default, it is set to false.  Thus, returns the set of undefined geo relation in an array.  If passed-in as true, method will return the total record count of undefined geo relation.
   * @param array $personType Optional. By default, it is set with array('staff').  Currently, the method is defined for staff and client entity type.  This specifies the query condition to search for staff and/or client with undefined geo relation.
   * @param array $siteType Optional.  By default, it is set with array('facility').  Currently, the method is defined only for facility site type.  This specifies the query condition to search for facility with undefined geo relation.
   * @return int|array Depending on the pass in param, this method can return either the total record count of undefined geo relation or returns an array of undefined geo relations as a form of array[geo id1] = geo id2.
   */
  public static function defineGeoRelation ($countRecords = FALSE, $personType = array('staff'), $siteType = array('facility'))
  {
    // In this condition (self-referencing table query), do not use symfony doctrine count.
    $query = Doctrine_Query::create();

    $query->select('g1.id, g2.id, c2.longitude, c2.latitude, c1.longitude, c1.latitude, f1.id, f2.id, c1.id, c2.id')
      ->from('agGeo g1')
      ->addFrom('agGeo g2')
      ->addFrom('agGeoFeature f1')
      ->addFrom('agGeoFeature f2')
      ->addFrom('agGeoCoordinate c1')
      ->addFrom('agGeoCoordinate c2')
      ->where('g1.id < g2.id')
      ->andWhere('g1.id=f1.geo_id')
      ->andWhere('f1.geo_coordinate_id=c1.id')
      ->andWhere('g2.id=f2.geo_id')
      ->andWhere('f2.geo_coordinate_id=c2.id')
      ->orderBy('g1.id, g2.id');

    // Search for person's geo id.
    $subPerson = $query->createSubquery()
      ->select('subG1.id')
      ->from('agGeo subG1')
      ->innerJoin('subG1.agAddressGeo subAG1')
      ->innerJoin('subAG1.agAddress subA1')
      ->innerJoin('subA1.agEntityAddressContact subEA1')
      ->innerJoin('subEA1.agEntity subE1')
      ->where('1')
      ->groupBy('subG1.id');
    
    $subPerson->innerJoin('subE1.agPerson p');
    foreach ($personType as $ptype)
    {
      switch ($ptype)
      {
        case 'staff':
          $subPerson->orWhere('p.id IN (SELECT stf.person_id FROM agStaff stf)');
          break;
        case 'client':
          $subPerson->orWhere('p.id IN (SELECT c.person_id FROM agClient c)');
          break;
        default:
          throw new sfException('An error occurred. Please pass in an accepted parameter.');
      }
    }
    $query->andWhere('g1.id IN (' . $subPerson->getDql() . ')');

    // Search for site type.
    $subSite = $query->createSubquery()
      ->select('subG2.id')
      ->from('agGeo subG2')
      ->innerJoin('subG2.agAddressGeo subAG2')
      ->innerJoin('subAG2.agAddress subA2')
      ->innerJoin('subA2.agEntityAddressContact subEA2')
      ->innerJoin('subEA2.agEntity subE2')
      ->where('1')
      ->groupBy('subG2.id');
    $subSite->innerJoin('subE2.agSite si');
    foreach ($siteType as $stype)
    {
      switch ($stype)
      {
        case 'facility':
          $subSite->orWhere('si.id IN (SELECT f.site_id FROM agFacility f)');
          break;
        default:
          throw new sfException('An error occurred. Please pass in an accepted parameter.');
      }
    }
    $query->andWhere('g2.id IN (' . $subSite->getDql() . ')');

    /*
     * @todo Remove this subquery block and replace it with a centroid calculation function.
     * This is, currently, a hack to prevent geo features with more than one coordinate from returning.
     */
    $subQuery1 = $query->createSubquery()
      ->select('f3.geo_id')
      ->from('agGeoFeature f3')
      ->groupBy('f3.geo_id')
      ->having('count(f3.geo_id) = 1');
    $query->andWhere('g1.id in (' . $subQuery1->getDql() . ')');
    $subQuery2 = $query->createSubquery()
      ->select('f4.geo_id')
      ->from('agGeoFeature f4')
      ->groupBy('f4.geo_id')
      ->having('count(f4.geo_id) = 1');
    $query->andWhere('g2.id in (' . $subQuery2->getDql() . ')');

    $test = $query->getSqlQuery();


    /*************
     * NOTE: The combo_set hydration does not return all of the query rows.  It
     * only returns the last sets since geo_id1 is used as the array key and has
     * to be unique.  The last appearance of geo_id1 over-writes the previous
     * sets of geo_id1, and geo_id2 where geo_id1 repeats.
     *
     */
    // Collect all geo relations in an array.
    $allGeoSet = $query->execute(array(), 'combo_set');
//    $allGeoSet = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    echo '<br>allGeoSet: ';
    print_r($allGeoSet);
    // Collect defined geo relations in an array.
    $existingGeoQuery = self::returnExistingGeoRelation();
    $existingGeoSet = $existingGeoQuery->execute(array(), 'combo_set');
//    $existingGeoSet = $existingGeoQuery->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    echo '<br><br>existingGeoSet: ';
    print_r($existingGeoSet);
    // Diff all geo relations against the defined geo relation to find the undefined geo relations.
    $newGeoSet = array_diff_assoc($allGeoSet, $existingGeoSet);
    echo '<br><br>newGeoSet: ';
    print_r($newGeoSet);

    // if countRecord is true, return only the total record count for undefined geo relations.  Otherwise, return the undefined geo relation set.
    return ($countRecords ? count($newGeoSet) : $newGeoSet);
  }
}