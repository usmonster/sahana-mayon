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
    $groupStatus = self::returnCurrentEventFacilityGroupStatus($eventId) ;

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
        ->andWhereIn('egs.id', array_keys($groupStatus)) ;
    
    $results = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    
    return $results ;
  }

  public static function returnFacilityResourceActivation ($eventId, $eventFacilityGroupId = NULL, $facilityStandbyStatus = NULL)
  {
    $groupStatus = self::returnCurrentEventFacilityGroupStatus($eventId) ;
    $resourceStatus = self::returnCurrentEventFacilityResourceStatus($eventId) ;

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
      ->whereIn('ers.id', array_keys($resourceStatus))
        ->andWhereIn('egs.id', array_keys($groupStatus))
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
        ->andWhere('efg.event_id = ?', $eventId) ;

    if (! is_null($eventFacilityGroupId)) { $query->andWhere('efg.id = ?', $eventFacilityGroupId) ; }

    if (! is_null($facilityStandbyStatus)) { $query->andWhere('ras.standby = ?', $facilityStandbyStatus) ; }
        
    $results = $query->execute() ;

    return $results ;
  }

  public static function activateZeroHourFacilityResources ($eventId, $activationTime)
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
        ->andWhere('ras.standby = ?', $false)
        ->andWhere('efg.id LIKE (?)', $eventFacilityGroupId) ;
    return $results;
  }

  /**
   * Static function to return the current status id's of all facility resources for a specific event
   *
   * <code>
   * // To get the current status of all facilities in a current event
   * $currentStatusIds = agEventFacilityHelper::returnCurrentFacilityResourceStatus($eventId) ;
   *
   * $q = Doctrine_Query::create()
   *   ->select('s.*')
   *   ->from('agEventFacilityResourceStatus s')
   *   ->whereIn('s.id', array_keys($currentStatusIds)) ;
   *
   * $results = $q->execute() ;
   * </code>
   *
   * @param integer(4) $eventId The event currently being queried
   * @return array A two-dimensional associative array, keyed by agEventFacilityResourceStatus.id with
   * a value array containing the surrogate key:
   * (agEventFacilityResourceStatus.event_facility_resource_id, agEventFacilityResourceStatus.time_stamp)
   */
  public static function returnCurrentEventFacilityResourceStatus($eventId)
  {
    $query = Doctrine_Query::create()
      ->select('efrs.id')
          ->addSelect('efrs.event_facility_resource_id')
          ->addSelect('efrs.time_stamp')
        ->from('agEventFacilityResourceStatus efrs')
          ->innerJoin('efrs.agEventFacilityResource efr')
          ->innerJoin('efr.agEventFacilityGroup efg')
        ->where('efg.event_id = ?', $eventId)
          ->andWhere('EXISTS (
            SELECT s.id
              FROM agEventFacilityResourceStatus s
              WHERE s.event_facility_resource_id = efrs.event_facility_resource_id
                AND s.time_stamp <= CURRENT_TIMESTAMP
              HAVING MAX(s.time_stamp) = efrs.time_stamp)') ;

    $results = $query->execute(array(), 'key_value_array') ;
    return $results ;
  }

  /**
   * Static function to return the current status id's of all facility groups for a specific event
   *
   * <code>
   * // To get the current status of all facilities in a current event
   * $currentStatusIds = agEventFacilityHelper::returnCurrentFacilityGroupStatus($eventId) ;
   *
   * $q = Doctrine_Query::create()
   *   ->select('s.*')
   *   ->from('agEventFacilityGroupStatus s')
   *   ->whereIn('s.id', array_keys($currentStatusIds)) ;
   *
   * $results = $q->execute() ;
   * </code>
   *
   * @param integer(4) $eventId The event currently being queried
   * @return array A two-dimensional associative array, keyed by agEventFacilityGroupStatus.id with
   * a value array containing the surrogate key:
   * (agEventFacilityGroupStatus.event_facility_group_id, agEventFacilityGroupStatus.time_stamp)
   */
  public static function returnCurrentEventFacilityGroupStatus($eventId)
  {
    $query = Doctrine_Query::create()
      ->select('efgs.id')
          ->addSelect('efgs.event_facility_group_id')
          ->addSelect('efgs.time_stamp')
        ->from('agEventFacilityGroupStatus efgs')
          ->innerJoin('efgs.agEventFacilityGroup efg')
        ->where('efg.event_id = ?', $eventId)
          ->andWhere('EXISTS (
            SELECT s.id
              FROM agEventFacilityGroupStatus s
              WHERE s.event_facility_group_id = efgs.event_facility_group_id
                AND s.time_stamp <= CURRENT_TIMESTAMP
              HAVING MAX(s.time_stamp) = efgs.time_stamp)') ;

    $results = $query->execute(array(), 'key_value_array') ;
    return $results ;
  }
public static function returnCurrentEventStatus($eventId)
  {
    $query = Doctrine_Query::create()
      ->select('es.id')
        //->addSelect('es.time_stamp')
        ->addSelect('es.event_status_type_id')
        ->from('agEventStatus es')
        ->where('es.event_id = ?', $eventId)
          ->andWhere('EXISTS (
            SELECT s.id
              FROM agEventStatus s
              WHERE s.event_id = es.event_id
                AND s.time_stamp <= CURRENT_TIMESTAMP
              HAVING MAX(s.time_stamp) = es.time_stamp)') ;

    $results = $query->execute(array(), 'key_value_array') ;
    return $results ;
  }

  /**
   * Function to return the event_status_id and event_status_type_id of the passed event_id
   * 
   * @param integer(4) $eventId The event id being queried
   * @return array An associative array with event_status_id as the key and event_status_type_id as the value 
   */
  public static function returnCurrentEventStatus($eventId)
  {
    $query = Doctrine_Query::create()
      ->select('es.event_status_type_id')
        ->from('agEventStatus es')
        ->where('es.event_id = ?', $eventId)
          ->andWhere('EXISTS (
            SELECT s.id
              FROM agEventStatus s
              WHERE s.event_id = es.event_id
                AND s.time_stamp <= CURRENT_TIMESTAMP
              HAVING MAX(s.time_stamp) = es.time_stamp)') ;

    $results = $query->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR) ;
    return $results ;
  }

  /**
   * Function to return the first shifts of each facility resource for a given event.
   * 
   * @param integer(4) $eventId The current event being queried
   * @return array A three-dimensional associative array keyed by facility_resource_id with value array
   * members as all shifts that fulfill the criterion of being a 'first shift' (eg, all start at the
   * same time).
   */
  public static function returnFirstFacilityResourceShifts($eventId)
  {
    $query = Doctrine_Query::create()
      ->select('es.facility_resource_id')
          ->addSelect('es.id')
        ->from('agEventShift es')
          ->innerJoin('es.agEventFacilityResource efr')
          ->innerJoin('efr.agEventFacilityGroup efg')
        ->where('efg.event_id = ?', $eventId)
          ->andWhere('EXISTS (
          SELECT s.id
            FROM agEventShift s
            WHERE s.event_facility_resource_id = es.event_facility_resource_id
            HAVING MIN(s.minutes_start_to_facility_activation) = es.minutes_start_to_facility_activation)') ;

    $results = $query->execute(array(), 'key_value_group_by') ;
    return $results ;
  }

  public static function returnFirstFacilityStaffedShifts($eventId)
  {
    $query = Doctrine_Query::create()
      ->select('es.facility_resource_id')
          ->addSelect('es.id')
        ->from('agEventShift es')
          ->innerJoin('es.agEventFacilityResource efr')
          ->innerJoin('efr.agEventFacilityGroup efg')
          ->innerJoin('es.agEventStaffShift ess')
        ->where('efg.event_id = ?', $eventId)
          ->andWhere('EXISTS (
          SELECT s.id
            FROM agEventShift s
            WHERE s.event_facility_resource_id = es.event_facility_resource_id
            HAVING MIN(s.minutes_start_to_facility_activation) = es.minutes_start_to_facility_activation)');

    $results = $query->execute(array(), 'key_value_array');
    return $results;
  }

  public static function returnCurrentFacilityResourceShifts($eventId, $time = NULL)
  {
    // convert our start time to unix timestamp or set default if null
    $timestamp = agDateTimeHelper::defaultTimestampFormat($time) ;
    $mysqlTime = agDateTimeHelper::timestampToMySql($timestamp) ;

    $query = new Doctrine_RawSql() ;
    $query->addComponent('es', 'agEventShift es')
      ->select('{es.facility_resource_id}')
        ->addSelect('{es.id}')
      ->from('ag_event_shift es')
        ->innerJoin('ag_event_facility_resource efr ON es.event_facility_resource_id = efr.id')
        ->innerJoin('ag_event_facility_group efg ON efr.event_facility_group_id = efg.id')
        ->leftJoin('ag_event_facility_resource_activation_time efrat ON efr.id = efrat.event_facility_resource_id')
      ->where('efg.event_id = ?', $eventId)
        ->andWhere('DATE_ADD(efrat.activation_time, INTERVAL es.minutes_start_to_facility_activation MINUTE) <= TIMESTAMP(?)', $mysqlTime)
        ->andWhere('DATE_ADD(efrat.activation_time, INTERVAL (es.minutes_start_to_facility_activation + es.task_length_minutes + es.break_length_minutes) MINUTE) >= TIMESTAMP(?)', $mysqlTime) ;

    $results = $query->execute(array(), 'key_value_array');
    return $results;
  }
}