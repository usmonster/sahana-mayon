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
      ->select('geo_id1, geo_id2')
      ->from('agGeoRelationship');

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

    $query->select('g1.id, g2.id')
      ->from('agGeo g1')
      ->addFrom('agGeo g2')
      ->where('g1.id < g2.id');

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
    $query->where('g1.id IN (' . $subPerson->getDql() . ')');

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

    // Collect all geo relations in an array.
    $allGeoSet = $query->execute(array(), 'key_value_pair');
    // Collect defined geo relations in an array.
    $existingGeoQuery = self::returnExistingGeoRelation();
    $existingGeoSet = $existingGeoQuery->execute(array(), 'key_value_pair');
    // Diff all geo relations against the defined geo relation to find the undefined geo relations.
    $newGeoSet = array_diff_assoc($allGeoSet, $existingGeoSet);

    // if countRecord is true, return only the total record count for undefined geo relations.  Otherwise, return the undefined geo relation set.
    return ($countRecords ? count($newGeoSet) : $newGeoSet);
  }
}