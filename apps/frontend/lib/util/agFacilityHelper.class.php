<?php

/**
 * Provides basic facility information.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agFacilityHelper
{

  protected static $affectScenariosGlobal = 'facility_resource_status_affects_scenarios',
                   $affectEventsGlobal = 'facility_resource_status_affects_events',
                   $resourceEnabledGlobal = 'facility_resource_enabled_status',
                   $resourceDisabledGlobal = 'facility_resource_disabled_status';

  /**
   * @method facilityGeneralInfo()
   * Returns a flat associate array of facility's general information.
   *
   * @param string $packageType Optional.  Currently can pass in scenario,
   *                            event, or NULL.
   * @param integer $packageId Optional.  The id of either scenario or event based on the $packageType
   * passed in.
   */
  public static function facilityGeneralInfo($packageType = NULL, $packageId = NULL)
  {
    try {
      $facilityQuery = agDoctrineQuery::create()
                      ->select('f.id, f.facility_name, frt.facility_resource_type_abbr, frs.facility_resource_status, fr.capacity, f.facility_code')
                      ->addSelect('e.id, s.id')
                      ->from('agFacility f')
                      ->innerJoin('f.agSite s')
                      ->innerJoin('s.agEntity e')
                      ->leftJoin('f.agFacilityResource fr')
                      ->leftJoin('fr.agFacilityResourceType frt')
                      ->leftJoin('fr.agFacilityResourceStatus frs')
                      ->orderBy('f.id');

      if (isset($packageType)) {
        switch (strtolower($packageType)) {
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
            if (!is_null($packageId))
            {
              $facilityQuery->andWhere('sfg.scenario_id = ?', $packageId);
            }
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
            if (!is_null($packageId))
            {
              $facilityQuery->andWhere('efg.scenario_id = ?', $packageId);
            }
            break;
          default:
            // Do nothing.
            break;
        }
      }

//      $facilityQueryString = $facilityQuery->getSqlQuery();
      $facilityInfo = $facilityQuery->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
      return $facilityInfo;
    } catch (Exception $e) {
      echo 'Caught exception: ', $e->getMessage(), "\n";
      return NULL;
    }
  }

  public static function facilityAddress($addressStandard, $primaryOnly=FALSE, $type=NULL)
  {
    try {
      $facilityQuery = agDoctrineQuery::create()
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
                      ->where('as.address_standard=?', $addressStandard);

      if (isset($type)) {
        $facilityQuery->addWhere('act.address_contact_type=?', $type);
      }

      if ($primaryOnly) {
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
      $facilityInfo = $facilityQuery->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

      $cleanFacilityInfo = array();
      foreach ($facilityInfo as $fac) {
        if (array_key_exists($fac['f_id'], $cleanFacilityInfo)) {
          if (array_key_exists($fac['act_address_contact_type'], $cleanFacilityInfo[$fac['f_id']])) {
            if (array_key_exists($fac['eac_priority'], $cleanFacilityInfo[$fac['f_id']][$fac['act_address_contact_type']])) {
              $tempArray = $cleanFacilityInfo[$fac['f_id']][$fac['act_address_contact_type']][$fac['eac_priority']];
              $newArray = $tempArray + array($fac['ae_address_element'] => $fac['av_value']);
              $cleanFacilityInfo[$fac['f_id']][$fac['act_address_contact_type']][$fac['eac_priority']] = $newArray;
            } else {
              $cleanFacilityInfo[$fac['f_id']][$fac['act_address_contact_type']][$fac['eac_priority']][$fac['ae_address_element']] = $fac['av_value'];
              $cleanFacilityInfo[$fac['f_id']][$fac['act_address_contact_type']][$fac['eac_priority']]['address_id'] = $fac['eac_address_id'];
            }
          } else {
            $tempArray = $cleanFacilityInfo[$fac['f_id']];
            $newArray = $tempArray + array($fac['act_address_contact_type'] => array($fac['eac_priority'] => array($fac['ae_address_element'] => $fac['av_value'])));
            $cleanFacilityInfo[$fac['f_id']] = $newArray;
            $cleanFacilityInfo[$fac['f_id']][$fac['act_address_contact_type']][$fac['eac_priority']]['address_id'] = $fac['eac_address_id'];
          }
        } else {
          $cleanFacilityInfo[$fac['f_id']] = array($fac['act_address_contact_type'] => array($fac['eac_priority'] => array($fac['ae_address_element'] => $fac['av_value'])));
          $cleanFacilityInfo[$fac['f_id']][$fac['act_address_contact_type']][$fac['eac_priority']]['address_id'] = $fac['eac_address_id'];
        }
      }

      return $cleanFacilityInfo;
    } catch (Exception $e) {
      echo 'Caught exception: ', $e->getMessage(), "\n";
      return NULL;
    }
  }

  public static function facilityGeo($primaryOnly=FALSE, $type=NULL)
  {
    try {
      $facilityQuery = agDoctrineQuery::create()
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
                      ->innerJoin('gf.agGeoCoordinate gc')
                      ->groupBy('f.id, act.address_contact_type, eac.address_id')
                      ->orderBy('f.id, eac.address_id, eac.address_contact_type_id, eac.priority');

      if (isset($type)) {
        $facilityQuery->where('act.address_contact_type=?', $type);
        $initialWhereClause = FALSE;
      }

      $facilityQueryString = $facilityQuery->getSqlQuery();
      $facilityInfo = $facilityQuery->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

      $cleanFacilityInfo = array();
      foreach ($facilityInfo as $fac) {
        if (array_key_exists($fac['f_id'], $cleanFacilityInfo)) {
          $tempArray = $cleanFacilityInfo[$fac['f_id']];
          $newArray = $tempArray + array($fac['eac_address_id'] => array('latitude' => $fac['gc_latitude'], 'longitude' => $fac['gc_longitude']));
          $cleanFacilityInfo[$fac['f_id']] = $newArray;
        } else {
          $cleanFacilityInfo[$fac['f_id']] = array($fac['eac_address_id'] => array('latitude' => $fac['gc_latitude'], 'longitude' => $fac['gc_longitude']));
        }
      }

      return $cleanFacilityInfo;
    } catch (Exception $e) {
      echo 'Caught exception: ', $e->getMessage(), "\n";
      return NULL;
    }
  }

  public static function facilityEmail($primaryOnly=FALSE, $type=NULL)
  {
    try {
      $initialWhereClause = TRUE;
      $facilityQuery = agDoctrineQuery::create()
                      ->select('f.id, ect.email_contact_type, ec.email_contact, eec.priority')
                      ->addSelect('s.id, e.id, eec.id')
                      ->from('agFacility f')
                      ->innerJoin('f.agSite s')
                      ->innerJoin('s.agEntity e')
                      ->innerJoin('e.agEntityEmailContact eec')
                      ->innerJoin('eec.agEmailContact ec')
                      ->innerJoin('eec.agEmailContactType ect');

      if (isset($type)) {
        if ($initialWhereClause) {
          $facilityQuery->where('ect.email_contact_type=?', $type);
          $initialWhereClause = FALSE;
        } else {
          $facilityQuery->addWhere('ect.email_contact_type=?', $type);
        }
      }

      if ($primaryOnly) {
        $subQuery = 'EXISTS (SELECT eec2.id
                             FROM agEntityEmailContact eec2
                             WHERE eec2.entity_id = eec.entity_id
                               AND eec2.email_contact_type_id = eec.email_contact_type_id
                             HAVING MIN(eec2.priority) = eec.priority)';

        if ($initialWhereClause) {
          $facilityQuery->where($subQuery);
        } else {
          $facilityQuery->addWhere($subQuery);
        }
      } else {
        $facilityQuery->orderBy('f.id, eec.email_contact_type_id, eec.priority');
      }

      $facilityQueryString = $facilityQuery->getSqlQuery();
      $facilityInfo = $facilityQuery->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

      $cleanFacilityInfo = array();
      foreach ($facilityInfo as $fac) {
        if (array_key_exists($fac['f_id'], $cleanFacilityInfo)) {
          if (array_key_exists($fac['ect_email_contact_type'], $cleanFacilityInfo[$fac['f_id']])) {
            $tempArray = $cleanFacilityInfo[$fac['f_id']][$fac['ect_email_contact_type']];
            $newArray = $tempArray + array($fac['eec_priority'] => $fac['ec_email_contact']);
            $cleanFacilityInfo[$fac['f_id']][$fac['ect_email_contact_type']] = $newArray;
          } else {
            $cleanFacilityInfo[$fac['f_id']][$fac['ect_email_contact_type']][$fac['eec_priority']] = $fac['ec_email_contact'];
          }
        } else {
          $cleanFacilityInfo[$fac['f_id']] = array($fac['ect_email_contact_type'] => array($fac['eec_priority'] => $fac['ec_email_contact']));
        }
      }

      return $cleanFacilityInfo;
    } catch (Exception $e) {
      echo 'Caught exception: ', $e->getMessage(), "\n";
      return NULL;
    }
  }

  public static function facilityPhone($primaryOnly=FALSE, $type=NULL)
  {
    try {
      $initialWhereClause = TRUE;
      $facilityQuery = agDoctrineQuery::create()
                      ->select('f.id, pct.phone_contact_type, pc.phone_contact, epc.priority')
                      ->addSelect('s.id, e.id, epc.id')
                      ->from('agFacility f')
                      ->innerJoin('f.agSite s')
                      ->innerJoin('s.agEntity e')
                      ->innerJoin('e.agEntityPhoneContact epc')
                      ->innerJoin('epc.agPhoneContact pc')
                      ->innerJoin('epc.agPhoneContactType pct');

      if (isset($type)) {
        if ($initialWhereClause) {
          $facilityQuery->where('pct.phone_contact_type=?', $type);
          $initialWhereClause = FALSE;
        } else {
          $facilityQuery->addWhere('pct.phone_contact_type=?', $type);
        }
      }

      if ($primaryOnly) {
        $subQuery = 'EXISTS (SELECT epc2.id
                             FROM agEntityPhoneContact epc2
                             WHERE epc2.entity_id = epc.entity_id
                               AND epc2.phone_contact_type_id = epc.phone_contact_type_id
                             HAVING MIN(epc2.priority) = epc.priority)';

        if ($initialWhereClause) {
          $facilityQuery->where($subQuery);
        } else {
          $facilityQuery->addWhere($subQuery);
        }
      } else {
        $facilityQuery->orderBy('f.id, epc.phone_contact_type_id, epc.priority');
      }

      $facilityQueryString = $facilityQuery->getSqlQuery();
      $facilityInfo = $facilityQuery->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

      $cleanFacilityInfo = array();
      foreach ($facilityInfo as $fac) {
        if (array_key_exists($fac['f_id'], $cleanFacilityInfo)) {
          if (array_key_exists($fac['pct_phone_contact_type'], $cleanFacilityInfo[$fac['f_id']])) {
            $tempArray = $cleanFacilityInfo[$fac['f_id']][$fac['pct_phone_contact_type']];
            $newArray = $tempArray + array($fac['epc_priority'] => $fac['pc_phone_contact']);
            $cleanFacilityInfo[$fac['f_id']][$fac['pct_phone_contact_type']] = $newArray;
          } else {
            $cleanFacilityInfo[$fac['f_id']][$fac['pct_phone_contact_type']][$fac['epc_priority']] = $fac['pc_phone_contact'];
          }
        } else {
          $cleanFacilityInfo[$fac['f_id']] = array($fac['pct_phone_contact_type'] => array($fac['epc_priority'] => $fac['pc_phone_contact']));
        }
      }

      return $cleanFacilityInfo;
    } catch (Exception $e) {
      echo 'Caught exception: ', $e->getMessage(), "\n";
      return NULL;
    }
  }

  public static function facilityStaffResource()
  {
    try {
      $facilityQuery = agDoctrineQuery::create()
                      ->select('fstf.scenario_facility_resource_id, srt.staff_resource_type, fstf.minimum_staff, fstf.maximum_staff')
                      ->from('agFacilityStaffResource fstf')
                      ->innerJoin('fstf.agStaffResourceType srt')
                      ->orderBy('fstf.scenario_facility_resource_id, srt.staff_resource_type');

      $facilityQueryString = $facilityQuery->getSqlQuery();
      $facilityInfo = $facilityQuery->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

      $cleanFacilityInfo = array();
      foreach ($facilityInfo as $fac) {
        if (array_key_exists($fac['fstf_scenario_facility_resource_id'], $cleanFacilityInfo)) {
          $tempArray = $cleanFacilityInfo[$fac['fstf_scenario_facility_resource_id']];
          $newArray = $tempArray + array($fac['srt_staff_resource_type'] => array('minimum staff' => $fac['fstf_minimum_staff'], 'maximum staff' => $fac['fstf_maximum_staff']));
          $cleanFacilityInfo[$fac['fstf_scenario_facility_resource_id']] = $newArray;
        } else {
          $cleanFacilityInfo[$fac['fstf_scenario_facility_resource_id']] = array($fac['srt_staff_resource_type'] => array('minimum staff' => $fac['fstf_minimum_staff'], 'maximum staff' => $fac['fstf_maximum_staff']));
        }
      }

      return $cleanFacilityInfo;
    } catch (Exception $e) {
      echo 'Caught exception: ', $e->getMessage(), "\n";
      return NULL;
    }
  }

  /**
   * Method to return the id value of a facility allocation status string
   * @param string $allocationStatusString The string value of the facility resource allocation
   * status.
   * @return integer The facility resource allocation status id.
   */
  public static function returnFacilityResourceAllocationStatusId($allocationStatusString)
  {
    $statusId = agDoctrineQuery::create()
                    ->select('fras.id')
                    ->from('agFacilityResourceAllocationStatus fras')
                    ->where('fras.facility_resource_allocation_status = ?', $allocationStatusString)
                    ->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    return $statusId;
  }

  /**
   * Method to return the available boolean value for the available status of a
   * $facilityResourceStatusId
   * @param integer(2) $facilityResourceStatusId The facility_resource_status_id being queried.
   * @param boolean $inverse Whether or not to apply an inverse match. Defaults to FALSE.
   * @return boolean The boolean value for available status.
   */
  public static function getFacilityResourceAvailableStatus($facilityResourceStatusId, $inverse = FALSE)
  {
    // pick up the available / unavailable boolean being passed
    $result = agDoctrineQuery::create()
                    ->select('frs.is_available')
                    ->from('agFacilityResourceStatus frs')
                    ->where('frs.id = ?', $facilityResourceStatusId)
                    ->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    // return an inverse match if preferred
    if ($inverse) {
      $result = ($result) ? FALSE : TRUE;
    }

    return $result;
  }

  /**
   * Method to return actionable facility resources based on the facility status being set
   * @param array $facilityResourceIds An array of facility_resource_ids
   * @param integer(2) $facilityResourceStatusId The facility status being set
   * @return array An array of facility resource id's for facilities that are undergoing an
   * activation change.
   */
  public static function returnActionableResources($facilityResourceIds, $facilityResourceStatusId)
  {
    $useInverse = TRUE;

    // get the current facility resource statuses
    $currentStatusQuery = agDoctrineQuery::create()
                    ->select('fr.id')
                    ->addSelect('frs.is_available')
                    ->from('agFacilityResource fr')
                    ->innerJoin('fr.agFacilityResourceStatus frs')
                    ->whereIn('fr.id', $facilityResourceIds);
    $facilityResourceStatuses = $currentStatusQuery->execute(array(), 'key_value_pair');

    // get the inverse of the passed available status
    $inverseMatch = self::getFacilityResourceAvailableStatus($facilityResourceStatusId, $useInverse);

    // find only those resources that are negative matches
    $actionableResources = array_keys($facilityResourceStatuses, $inverseMatch);

    $results = $actionableResources;
    return $results;
  }

  /**
   * Returns the id of the abbreviated facility resource type either in an associative array or an
   * integer.
   * @param string $facilityResourceAbbrType Optional.  Pass in a specific abbreviated facility
   * resource type to retrieve only the id of that instant.  If none pass in, retrieve the ids for
   * all facility resource type.
   * @return array|integer Returns either an associative array,
   * array(id => facility resource abbr type), for all types if no param is passed.
   * Otherwise, just the single-value id of the pass-in param.
   */
  public static function getFacilityResourceAbbrTypes($facilityResourceAbbrType = NULL)
  {
    $facilityResourceTypes = agDoctrineQuery::create()
                    ->from('agFacilityResourceType frt');

    if (empty($facilityResourceAbbrType)) {
      $facilityResourceTypes = $facilityResourceTypes->select('frt.id, frt.facility_resource_type_abbr')
                      ->execute(array(), 'key_value_pair');
    } else {
      $facilityResourceTypes = $facilityResourceTypes->select('frt.id')
                      ->where('facility_resource_type = ?', $facilityResourceAbbrType)
                      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    }

    return $facilityResourceTypes;
  }

  /**
   * Returns the id of the facility resource status either in an associative array or an integer.
   * @param string $facilityResourceStatus Optional.  Pass in a specific facility resource status
   * to retrieve only the id of that instant.  If none pass in, retrieve the ids for
   * all facility resource status.
   * @return array|integer Returns either an associative array,
   * array(id => facility resource allocation status), for all types if no param is passed.
   * Otherwise, just the single-value id of the pass-in param.
   */
  public static function getFacilityResourceStatuses($facilityResourceStatus = NULL)
  {
    $facilityResourceStatuses = agDoctrineQuery::create()
                    ->from('agFacilityResourceStatus');

    if (empty($facilityResourceStatus)) {
      $facilityResourceStatuses = $facilityResourceStatuses->select('id, facility_resource_status')
              ->execute(array(), 'key_value_pair');
    } else {
      $facilityResourceStatuses = $facilityResourceStatuses->select('id')
              ->where('facility_resource_status = ?', $facilityResourceStatus)
              ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    }

    return $facilityResourceStatuses;
  }

  /**
   * Returns the id of the geo type either in an associate array or an integer.
   * @param string $geoType Optional.  Pass in a specific geo type to retrieve only the id of that
   * instant.  If none pass in, retrieve the ids for all geo type.
   * @return array|integer Returns either an associate array, array(id => geo type), for all types or
   * returns only the id for the specified pass-in geo type param.
   */
  public static function getGeoTypes($geoType = NULL)
  {
    $geoTypes = agDoctrineQuery::create()
                    ->from('agGeoType');

    if (empty($geoType)) {
      $geoTypes = $geoTypes->select('id, geo_type')
                      ->execute(array(), 'key_value_pair');
    } else {
      $geoTypes = $geoTypes->select('id')
                      ->where('geo_type = ?', $geoType)
                      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    }
    return $geoTypes;
  }

  /**
   * Returns the id of the facility resource allocation status either in an associate array or an
   * integer.
   * @param string $allocationStatus Optional.  Pass in a specific facility resource allocation
   * status to retrieve only the id of that instant.  If none pass in, retrieve the ids for all
   * facility resource allocation statuses.
   * @return array|integer Returns either an associate array,
   * array(id => facility resource allocation status), for all types or returns only the id for the
   * specified pass-in facility resource allocation status param.
   */
  public static function getFacilityResourceAllocationStatuses($allocationStatus = null)
  {
    $facilityResourceAllocationStatuses = agDoctrineQuery::create()
                    ->from('agFacilityResourceAllocationStatus');

    if (empty($facilityResourceStatus)) {
      $facilityResourceAllocationStatuses = $facilityResourceAllocationStatuses->select('id, facility_resource_allocation_status')
              ->execute(array(), 'key_value_pair');
    } else {
      $facilityResourceAllocationStatuses = $facilityResourceAllocationStatuses->select('id')
              ->where('facility_resource_allocation_status = ?', $allocationStatus)
              ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    }

    return $facilityResourceAllocationStatuses;
  }

  /**
   * @param string $groupType Optional.  None or a specific facility group type.
   * @return array|integer Returns either an associate array, array(id => facility group type), for
   * all types or returns only the id for the specified pass-in facility resource allocation
   * status param.
   */
  public static function getFacilityGroupTypes($groupType = null)
  {
    $facilityGroupTypes = agDoctrineQuery::create()
                    ->from('agFacilityGroupType');

    if (empty($groupType)) {
      $facilityGroupTypes = $facilityGroupTypes->select('id, facility_group_type')
                    ->execute(array(), 'key_value_pair');
    } else {
      $facilityGroupTypes = $facilityGroupTypes->select('id')
              -where('facility_group_type = ?', $groupType)
              ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    }

    return $facilityGroupTypes;
  }

  /**
   * @param string $allocationStatus Optional.  None or a specific facility group allocation status.
   * @return array|integer Returns either an associate array,
   * array(id => facility group allocation status), for all types or returns only the id for the
   * specified pass-in facility group allocation status param.
   */
  public static function getFacilityGroupAllocationStatuses ($allocationStatus = null) {
    $facilityGroupAllocationStatuses = agDoctrineQuery::create()
              ->from('agFacilitygroupAllocationStatus');

    if (empty($allocationStatus)) {
      $facilityGroupAllocationStatuses = $facilityGroupAllocationStatuses->select('id, facility_group_allocation_status')
              ->execute(array(), 'key_value_pair');
    } else {
      $facilityGroupAllocationStatuses = $facilitygroupAllocationStatuses->select('id')
              ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    }

    return $facilityGroupAllocationStatuses;
  }

  private function getAttributeIds ($tableName, $attributeName) {
    $returnValue = agDoctrineQuery::create()
      ->select('id, '. $attributeName)
      ->from($tableName)
      ->execute(array(), 'key_value_pair');
    return $returnValue;
  }

}