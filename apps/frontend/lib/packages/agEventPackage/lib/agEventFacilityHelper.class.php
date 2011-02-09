<?php

/**
 * provides event facility management functions
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

class agEventFacilityHelper
{
  public static function returnActiveFacilityGroups ($eventId)
  {
    $query = Doctrine_Query::create()
      ->select('efg.id')
        ->addSelect('efg.event_facility_group')
        ->addSelect('fgt.facility_group_type')
        ->addSelect('fgt.id')
      ->from('agEventFacilityGroup efg')
        ->innerJoin('efg.agFacilityGroupType fgt')
        ->innerJoin('efg.agEventFacilityGroupStatus egs')
        ->innerJoin('egs.agFacilityGroupAllocationStatus gas')
      ->where('efg.event_id = ?', $eventId)
        ->andWhere('gas.active = ?', true)
        ->andWhere('EXISTS (
          SELECT efgs.id
            FROM agEventFacilityGroupStatus efgs
            WHERE efgs.event_facility_group_id = egs.event_facility_group_id
              AND efgs.time_stamp <= CURRENT_TIMESTAMP
            HAVING MAX(efgs.time_stamp) = egs.time_stamp)') ;
    
    $results = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    
    return $results ;
  }

  public static function returnFacilityResourceActivation ($eventId, $eventFacilityGroupId = '%', $facilityStandbyStatus='%') //might break with wildcard defaults
  {
    $query = Doctrine_Query::create()
      ->select('efr.id')
        ->addSelect('f.facility_name')
        ->addSelect('f.facility_code')
        ->addSelect('frt.facility_resource_type')
        ->addSelect('ras.standby')
        ->addSelect('ras.facility_resource_allocation_status')
        ->addSelect('es.minutes_start_to_facility_activation')
        ->addSelect('f.id')
        ->addSelect('fr.id')
        ->addSelect('frt.id')
        ->addSelect('ras.id')
        ->addSelect('ers.id')
        ->addSelect('es.id')
      ->from('agEventFacilityResource efr')
        ->innerJoin('efr.agFacilityResource fr')
        ->innerJoin('fr.agFacilityResourceStatus frs')
        ->innerJoin('fr.agFacilityResourceType frt')
        ->innerJoin('fr.agFacility f')
        ->innerJoin('efr.agEventFacilityResourceStatus ers')
        ->innerJoin('ers.agFacilityResourceAllocationStatus ras')
        ->innerJoin('efr.agEventFacilityGroup efg')
        ->innerJoin('efg.agEventFacilityGroupStatus egs')
        ->innerJoin('egs.agFacilityGroupAllocationStatus gas')
        ->leftJoin('efr.agEventFacilityResourceActivationTime efat')
        ->innerJoin('efr.agEventShift es')
      ->where('EXISTS (
          SELECT efrs.id
            FROM agEventFacilityResourceStatus efrs
            WHERE efrs.event_facility_resource_id = ers.event_facility_resource_id
              AND efrs.time_stamp <= CURRENT_TIMESTAMP
            HAVING MAX(efrs.time_stamp) = ers.time_stamp)')
        ->andWhere('EXISTS (
          SELECT efgs.id
            FROM agEventFacilityGroupStatus efgs
            WHERE efgs.event_facility_group_id = egs.event_facility_group_id
              AND efgs.time_stamp <= CURRENT_TIMESTAMP
            HAVING MAX(efgs.time_stamp) = egs.time_stamp)')
        ->andWhere('EXISTS (
          SELECT s.id
            FROM agEventShift s
            WHERE s.event_facility_resource_id = es.event_facility_resource_id
            HAVING MIN(s.minutes_start_to_facility_activation) = es.minutes_start_to_facility_activation)')
        ->andWhere('efat.id IS NULL')
        ->andWhere('frs.is_available = ?', true)
        ->andWhere('gas.active = ?', true)
        ->andWhere('(ras.allocatable = ? OR ras.committed = ?)', array(true, true))
        ->andWhere('ras.staffed = ?', false)
        ->andWhere('efg.event_id = ?', $eventId)
        ->andWhere('ras.standby LIKE (?)', $facilityStandbyStatus)
        ->andWhere('efg.id LIKE (?)', $eventFacilityGroupId) ;

    $results = $query->execute() ;

    return $results ;
  }
}