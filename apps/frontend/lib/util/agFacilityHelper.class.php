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
              ->select('f.id, f.facility_name, f.facility_code, frt.facility_resource_type_abbr, frs.facility_resource_status, fr.capacity')
//              ->addSelect('ec.email_contact')
              ->addSelect('e.id, s.id')
//              ->addSelect('eac.id, s.id, e.id, eec.id')
              ->from('agFacility f')
              ->innerJoin('f.agSite s')
              ->innerJoin('s.agEntity e')
              ->leftJoin('f.agFacilityResource fr')
              ->leftJoin('fr.agFacilityResourceType frt')
              ->leftJoin('fr.agFacilityResourceStatus frs')
              ->orderBy('f.id');
      
      if (isset($packageType))
      {
        switch( strtolower($packageType) )
        {
          case "scenario":
            $facilityQuery->addSelect('sfr.id, sfr.activation_sequence, fras.facility_resource_allocation_status')
              ->addSelect('sfg.scenario_facility_group, fgt.facility_group_type, fgas.facility_group_allocation_status, sfg.activation_sequence')
              ->leftJoin('fr.agScenarioFacilityResource sfr')
              ->leftJoin('sfr.agFacilityResourceAllocationStatus fras')
              ->leftJoin('sfr.agScenarioFacilityGroup sfg')
              ->leftJoin('sfg.agFacilityGroupType fgt')
              ->leftJoin('sfg.agFacilityGroupAllocationStatus fgas')
              ->where('sfr.id IS NOT NULL')
              ->addOrderBy('sfg.id, sfg.activation_sequence, sfr.activation_sequence');
            break;
          case "event":
            $facilityQuery->addSelect('efr.id, efr.activation_sequence, fras.facility_resource_allocation_status')
              ->addSelect('efg.event_facility_group, fgt.facility_group_type, fgas.facility_group_allocation_status, efg.activation_sequence')
              ->addSelect('efrs.id, efgs.id')
              ->leftJoin('fr.agEventFacilityResource efr')
              ->leftJoin('efr.agEventFacilityResourceStatus efrs')
              ->leftJoin('efrs.agFacilityResourceAllocationStatus fras')
              ->leftJoin('efr.agEventFacilityGroup efg')
              ->leftJoin('efg.agFacilityGroupType fgt')
              ->leftJoin('efg.agEventFacilityGroupStatus efgs')
              ->leftJoin('efgs.agFacilityGroupAllocationStatus fgas')
              ->where('efr.id IS NOT NULL')
              ->addOrderBy('efg.id, efg.activation_sequence, efr.activation_sequence');
            break;
          default:
            // Do nothing.
            break;
        }
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

  public static function facilityAddress($primaryOnly=FALSE, $type=NULL, $countryFormat = 'United States')
  {
    try {
      $facilityQuery = Doctrine_Query::create()
              ->select('f.id, eac.entity_id, act.address_contact_type, eac.address_id, eac.priority')
              ->addSelect('ae.address_element, av.value')
              ->addSelect('s.id, e.id, eac.id, a.id, aav.id')
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
              ->innerJoin('af.agAddressStandard as')
              ->innerJoin('as.agCountry c')
              ->where('c.country=?', $countryFormat);
//              ->orderBy('f.id, a.id, act.id, af.line_sequence, af.inline_sequence');
//              ->orderBy('f.id, eac.address_id, eac.address_contact_type_id, af.line_sequence, af.inline_sequence');

      if (isset($type))
      {
          $facilityQuery->addWhere('act.address_contact_type=?', $type);
      }

      if ($primaryOnly)
      {
        $subQuery = 'EXISTS (SELECT eac2.id
                             FROM agEntityAddressContact eac2
                             WHERE eac2.entity_id = eac.entity_id
                               AND eac2.address_contact_type_id = eac.address_contact_type_id
                             HAVING MIN(eac2.priority) = eac.priority)';

        $facilityQuery->addWhere($subQuery);
      } else {
        $facilityQuery->orderBy('f.id, act.id, eac.priority, a.id, af.line_sequence, af.inline_sequence');
      }

      $facilityQueryString = $facilityQuery->getSqlQuery();
      echo "$facilityQueryString<BR />";
      $facilityInfo = $facilityQuery->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
//      print_r($facilityInfo);
//      return $facilityInfo;

      $cleanFacilityInfo = array();
      foreach($facilityInfo as $fac)
      {
        if (array_key_exists($fac['f_id'], $cleanFacilityInfo))
        {
          if (array_key_exists($fac['act_address_contact_type'], $cleanFacilityInfo[ $fac['f_id'] ]))
          {
            if (array_key_exists($fac['eac_priority'], $cleanFacilityInfo[ $fac['f_id'] ][ $fac['act_address_contact_type'] ]))
            {
              $tempArray = $cleanFacilityInfo[ $fac['f_id'] ][ $fac['act_address_contact_type'] ][ $fac['eac_priority'] ][ $fac['ae_address_element'] ] = $fac['av_value'];
            } else {
              $tempArray = $cleanFacilityInfo[ $fac['f_id'] ][ $fac['act_address_contact_type'] ];
              $newArray = $tempArray + array($fac['eac_priority'] => array($fac['ae_address_element'] => $fac['av_value']));
              $cleanFacilityInfo[ $fac['f_id'] ][ $fac['act_address_contact_type'] ] = $newArray;
              $cleanFacilityInfo[$fac['f_id']][$fac['act_address_contact_type']][$fac['eac_priority']]['address_id'] = $fac['eac_address_id'];
            }
          } else {
            $tempArray = $cleanFacilityInfo[ $fac['f_id'] ];
            $newArray = $tempArray + array($fac['act_address_contact_type'] => array($fac['eac_priority'] => array($fac['ae_address_element'] => $fac['av_value'])));
            $cleanFacilityInfo[ $fac['f_id'] ] = $newArray;
            $cleanFacilityInfo[$fac['f_id']][$fac['act_address_contact_type']][$fac['eac_priority']]['address_id'] = $fac['eac_address_id'];
          }
        } else {
          $cleanFacilityInfo[$fac['f_id']] = array($fac['act_address_contact_type'] => array($fac['eac_priority'] => array($fac['ae_address_element'] => $fac['av_value'])));
          $cleanFacilityInfo[$fac['f_id']][$fac['act_address_contact_type']][$fac['eac_priority']]['address_id'] = $fac['eac_address_id'];
        }
      }

      print_r($cleanFacilityInfo);

    } catch (Exception $e) {
      echo 'Caught exception: ', $e->getMessage(), "\n";
//      return NULL;
    }
  }

  public static function facilityGeo($primaryOnly=FALSE, $type=NULL)
  {
    try {
      $initialWhereClause = TRUE;
      $facilityQuery = Doctrine_Query::create()
              ->select('f.id, act.address_contact_type, eac.address_id, eac.priority')
              ->addSelect('gc.latitude, gc.longitude')
              ->addSelect('s.id, e.id, eac.id, a.id, ag.id, g.id, gf.id')
              ->from('agFacility f')
              ->innerJoin('f.agSite s')
              ->innerJoin('s.agEntity e')
              ->innerJoin('e.agEntityAddressContact eac')
              ->innerJoin('eac.agAddressContactType act')
              ->innerJoin('eac.agAddress a')
              ->innerJoin('a.agAddressGeo ag')
              ->innerJoin('ag.agGeo g')
              ->innerJoin('g.agGeoSource gs')
              ->innerJoin('g.agGeoFeature gf')
              ->innerJoin('gf.agGeoCoordinate gc');

      if (isset($type))
      {
        if ($initialWhereClause)
        {
          $facilityQuery->where('act.address_contact_type=?', $type);
          $initialWhereClause = FALSE;
        }
      }

      if ($primaryOnly)
      {
        $subQuery = 'Exists (SELECT eac2.id
                             FROM agEntityAddressContact eac2
                             WHERE eac2.entity_id = eac.entity_id
                               AND eac2.address_contact_type_id = eac.address_contact_type_id
                             HAVING MIN(eac2.priority) = eac.priority';

        if ($initialWhereClause)
        {
          $facilityQuery->where($subQuery);
          $initialWhereClause = FALSE;
        } else {
          $facilityQuery->addWhere($subQuery);
        }
      } else {
        $facilityQuery->orderBy('f.id, eac.address_id, eac.address_contact_type_id, eac.priority');
      }

      $facilityQueryString = $facilityQuery->getSqlQuery();
      echo "$facilityQueryString<BR />";
      $facilityInfo = $facilityQuery->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
//      print_r($facilityInfo);
//      return $facilityInfo;

      $cleanFacilityInfo = array();
      foreach ($facilityInfo as $fac)
      {
        if (array_key_exists($fac['f_id'], $cleanFacilityInfo))
        {
          $tempArray = $cleanFacilityInfo[ $fac['f_id'] ];
          $newArray = $tempArray + array( $fac['eac_address_id'] => array( 'latitude' => $fac['gc_latitude'], 'longitude' => $fac['gc_longitude']));
          $cleanFacilityInfo[ $fac['f_id'] ] = $newArray;
        } else {
          $cleanFacilityInfo[ $fac['f_id'] ] = array($fac['eac_address_id'] => array( 'latitude' => $fac['gc_latitude'], 'longitude' => $fac['gc_longitude']));
        }
      }

      print_r($cleanFacilityInfo);
//      return $cleanFacilityInfo;




    } catch (Exception $e) {
      echo 'Caught exception: ', $e->getMessage(), "\n";
//      return NULL;
    }
  }

  public static function facilityEmail($primaryOnly=FALSE, $type=NULL)
  {
    try {
      $initialWhereClause = TRUE;
      $facilityQuery = Doctrine_Query::create()
              ->select('f.id, ect.email_contact_type, ec.email_contact, eec.priority')
              ->addSelect('s.id, e.id, eec.id')
              ->from('agFacility f')
              ->innerJoin('f.agSite s')
              ->innerJoin('s.agEntity e')
              ->innerJoin('e.agEntityEmailContact eec')
              ->innerJoin('eec.agEmailContact ec')
              ->innerJoin('eec.agEmailContactType ect');
//              ->orderBy('f.id, eec.email_contact_type_id, eec.priority');

      if (isset($type))
      {
        if ($initialWhereClause) {
          $facilityQuery->where('ect.email_contact_type=?', $type);
          $initialWhereCaluse = FALSE;
        } else {
          $facilityQuery->addWhere('ect.email_contact_type=?', $type);
        }
      }

      if ($primaryOnly)
      {
        $subQuery = 'EXISTS (SELECT eec2.id
                             FROM agEntityEmailContact eec2
                             WHERE eec2.entity_id = eec.entity_id
                               AND eec2.email_contact_type_id = eec.email_contact_type_id
                             HAVING MIN(eec2.priority) = eec.priority)';

        if ($initialWhereClause)
        {
          $facilityQuery->where($subQuery);
        } else {
          $faciltiyQuery->addWhere($subQuery);
        }
      } else {
        $facilityQuery->orderBy('f.id, eec.email_contact_type_id, eec.priority');
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

  public static function facilityPhone($primaryOnly=FALSE, $type=NULL)
  {
    try {
      $initialWhereClause = TRUE;
      $facilityQuery = Doctrine_Query::create()
              ->select('f.id, pct.phone_contact_type, pc.phone_contact, epc.priority')
              ->addSelect('s.id, e.id, epc.id')
              ->from('agFacility f')
              ->innerJoin('f.agSite s')
              ->innerJoin('s.agEntity e')
              ->innerJoin('e.agEntityPhoneContact epc')
              ->innerJoin('epc.agPhoneContact pc')
              ->innerJoin('epc.agPhoneContactType pct');

      if (isset($type))
      {
        if ($initialWhereClause)
        {
          $facilityQuery->where('pct.phone_contact_type=?', $type);
          $initialWhereClause = FALSE;
        } else {
          $facilityQuery->addWhere('pct.phone_contact_type=?', $type);
        }
      }

      if($primaryOnly)
      {
        $subQuery = 'EXISTS (SELECT epc2.id
                             FROM agEntityPhoneContact epc2
                             WHERE epc2.entity_id = epc.entity_id
                               AND epc2.phone_contact_type_id = epc.phone_contact_type_id
                             HAVING MIN(epc2.priority) = epc.priority)';

        if ($initialWhereClause)
        {
          $facilityQuery->where($subQuery);
        } else {
          $facilityQuery->addWhere($subQuery);
        }
      } else {
        $facilityQuery->orderBy('f.id, epc.phone_contact_type_id, epc.priority');
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

  public static function facilityStaffResource()
  {
    try {
      $facilityQuery = Doctrine_Query::create()
              ->select('fstf.scenario_facility_resource_id, srt.staff_resource_type, fstf.minimum_staff, fstf.maximum_staff')
              ->from('agFacilityStaffResource fstf')
              ->innerJoin('fstf.agStaffResourceType srt')
              ->orderBy('fstf.scenario_facility_resource_id, srt.staff_resource_type');

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

}