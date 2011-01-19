<?php

class agGisQuery
{
  /**
   * @method missingGis
   * Query generator function.
   * @param string $entityType - Specifies the type of entity for address id search.
   * @returns a query string.
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
   *
   */
  public static function flattenAddress()
  {
    
  }

  /**
   * 
   */
  public static function defineGeoRelation ($personType = array('staff'), $siteType = array('facility'))
//  public static function defineGeoRelation()
  {
    // In this condition (self-referencing table query), do not use symfony doctrine count.
    $query = Doctrine_Query::create()
      ->select('g1.id, g2.id')
      ->from('agGeo g1')
      ->addFrom('agGeo g2')
      ->where('g1.id < g2.id');

//    $subQuery = $query->createSubquery()
//      ->select('geo.id')
//      ->from('agGeo geo')
//      ->innerJoin('geo.agAddressGeo ag')
//      ->innerJoin('ag.agAddress a')
//      ->innerJoin('a.agEntityAddressContact ea')
//      ->innerJoin('ea.agEntity e')
//      ->where('1')
//      ->groupBy('geo.id');

    // Search for person's geo id.
//    $subPerson = $subQuery;
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
//    $subSite = $subQuery;
    $subSite = $query->createSubquery()
      ->select('subG2.id')
      ->from('agGeo subG2')
      ->innerJoin('subG2.agAddressGeo subAG2')
      ->innerJoin('subAG2.agAddress subA2')
      ->innerJoin('subA2.agEntityAddressContact subEA')
      ->innerJoin('subEA.agEntity subE2')
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

    return $query;
  }

  /**
   *
   */
  public static function caculateGis()
  {

  }
}