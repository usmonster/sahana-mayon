<?php

/**
 * This class is used to provide facility's basic information.
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agFacilityHelper {

  /**
   * @method facilityGeneralInfo()
   * Returns a flat associate array of facility's general information.
   *
   * @param string $packageType Optional.  Currently can pass in scenario,
   *                            event, or NULL.
   */
//  public static function facilityGeneralInfo($facilityIds = array(), $scenarioId = null)
  public static function facilityGeneralInfo($packageType = NULL)
  {
    try {
      $facilityQuery = Doctrine_Query::create()
              ->select('f.id AS facility_id, f.facility_name, f.facility_code, frt.facility_resource_type_abbr, frs.facility_resource_status, fr.capacity')
//              ->addSelect('ec.email_contact')
              ->addSelect('e.id, s.id')
//              ->addSelect('eac.id, s.id, e.id, eec.id')
              ->from('agFacility f')
              ->innerJoin('f.agSite s')
              ->innerJoin('s.agEntity e')
              ->leftJoin('f.agFacilityResource fr')
              ->leftJoin('fr.agFacilityResourceType frt')
              ->leftJoin('fr.agFacilityResourceStatus frs');
      
      if (isset($packageType))
      {
        switch( strtolower($packageType) )
        {
          case "scenario":
            $facilityQuery->addSelect('sfr.activation_sequence, fras.facility_resource_allocation_status')
              ->addSelect('sfg.scenario_facility_group, fgt.facility_group_type, fgas.facility_group_allocation_status, sfg.activation_sequence')
              ->leftJoin('fr.agScenarioFacilityResource sfr')
              ->leftJoin('sfr.agFacilityResourceAllocationStatus fras')
              ->leftJoin('sfr.agScenarioFacilityGroup sfg')
              ->leftJoin('sfg.agFacilityGroupType fgt')
              ->leftJoin('sfg.agFacilityGroupAllocationStatus fgas')
              ->where('sfr.id IS NOT NULL');
            break;
          case "event":
            $facilityQuery->addSelect('efr.activation_sequence, fras.facility_resource_allocation_status')
              ->addSelect('efg.event_facility_group, fgt.facility_group_type, fgas.facility_group_allocation_status, efg.activation_sequence')
              ->addSelect('efrs.id, efgs.id')
              ->leftJoin('fr.agEventFacilityResource efr')
              ->leftJoin('efr.agEventFacilityResourceStatus efrs')
              ->leftJoin('efrs.agFacilityResourceAllocationStatus fras')
              ->leftJoin('efr.agEventFacilityGroup efg')
              ->leftJoin('efg.agFacilityGroupType fgt')
              ->leftJoin('efg.agEventFacilityGroupStatus efgs')
              ->leftJoin('efgs.agFacilityGroupAllocationStatus fgas')
              ->where('efr.id IS NOT NULL');
            break;
          default:
            // Do nothing.
            break;
        }
      }

//              ->leftJoin('e.agEntityEmailContact eec')
//              ->leftJoin('eec.agEmailContact ec')
//              ->leftJoin('eec.agEmailContactType ect')
//              ->addWhere('ect.email_contact_type=?', 'work');
//              ->orWHere('EXISTS (SELECT eec2.id
//                                  FROM agEntityEmailContact eec2
//                                  WHERE eec2.entity_id=eec.entity_id
//                                    AND eec2.email_contact_type_id=eec.email_contact_type_id
//                                  HAVING MIN(eec2.priority) = eec.priority)');

      $facilityQueryString = $facilityQuery->getSqlQuery();
      echo "$facilityQueryString<BR />";
      $facilityInfo = $facilityQuery->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
      print_r($facilityInfo);
//      return $facilityInfo;
    } catch (Exception $e) {
      echo 'Caught exception: ', $e->getMessage(), "\n";
//      return NULL;
    }
  }

  public static function facilityAddress($primaryOnly=FALSE, $type=NULL)
  {
    try {
      $facilityQuery = Doctrine_Query::create()
              ->select('f.id, eac.entity_id, eac.address_contact_type_id, eac.address_id')
              ->addSelect('ae.address_element, av.value')
              ->addSelect('gc.longitude, gc.latitude')
              ->addSelect('s.id, e.id, eac.id, a.id, aav.id')
              ->addSelect('ag.id, g.id, gf.id')
              ->from('agFacility f')
              ->innerJoin('f.agSite s')
              ->innerJoin('s.agEntity e')
              ->leftJoin('e.agEntityAddressContact eac')
              ->innerJoin('eac.agAddressContactType act')
              ->innerJoin('eac.agAddress a')
              ->leftJoin('a.agAddressMjAgAddressValue aav')
              ->leftJoin('aav.agAddressValue av')
              ->leftJoin('av.agAddressElement ae')
              ->innerJoin('ae.agAddressFormat af')
              ->leftJoin('a.agAddressGeo ag')
                ->leftJoin('ag.agGeo g')
                ->leftJoin('g.agGeoSource gs')
                ->leftJoin('g.agGeoFeature gf')
                ->leftJoin('gf.agGeoCoordinate gc')
              ->groupBy('eac.entity_id, eac.address_contact_type_id')
              ->orderBy('f.id, af.line_sequence, af.inline_sequence');

      if (isset($type))
      {
          $facilityQuery->where('act.address_contact_type=?', $type);
      }

      if ($primaryOnly)
      {
          $facilityQuery->addSelect('MIN(eac.priority) AS priority');
      } else {
        $facilityQuery->addSelect('eac.priority')
          ->addGroupBy('eac.address_id');
      }

      $facilityQueryString = $facilityQuery->getSqlQuery();
      echo "$facilityQueryString<BR />";
      $facilityInfo = $facilityQuery->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
      print_r($facilityInfo);
//      return $facilityInfo;
    } catch (Exception $e) {
      echo 'Caught exception: ', $e->getMessage(), "\n";
//      return NULL;
    }
  }

  public static function facilityGeo()
  {

  }

//  public static function facilityEmail($primaryOnly=FALSE, $type=NULL)
//  {
//    try {
//      $facilityAddress = Doctrine_Query::create()
//              ->from('agFacility f')
//              ->innerJoin('f.agSite s')
//              ->innerJoin('s.agEntity e')
//              ->
//
//    } catch (Exception $e) {
//      echo 'Caught exception: ', $e->getMessage(), "\n";
////      return NULL;
//    }
//  }
//
//  public static function facilityPhone($primaryOnly=FALSE, $type=NULL)
//  {
//    try {
//      $facilityAddress = Doctrine_Query::create()
//              ->from('agFacility f')
//              ->innerJoin('f.agSite s')
//              ->innerJoin('s.agEntity e')
//              ->
//
//    } catch (Exception $e) {
//      echo 'Caught exception: ', $e->getMessage(), "\n";
////      return NULL;
//    }
//  }

}