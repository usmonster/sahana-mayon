<?php

/**
 * provides event shift management functions
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

class agEventShiftHelper
{
  /**
   * Updates a single Event Shift record with absolute times
   *
   * @param integer $eventShiftId The event shift ID
   * @param timestamp $startTime The time for the shift to start operation
   * @param timestamp $startTime The time for the shift to close operation
   * @param timestamp $breakStart The time the shift break begins
   * @param timestamp $breakEnd The time the shift break ends
   * @return none
   */
  private function updateEventShiftTime($eventShiftId, $startTime, $endTime, $breakStart, $breakEnd)
  {    
    $query = Doctrine_Query::create()
      ->update('agEventShift')
      ->set('start_time', $startTime)
      ->set('end_time', $endTime)
      ->set('break_start', $breakStart)
      ->set('break_end', $breakEnd)
      ->where('id = ?', $eventShiftId);

    $query->execute() ;
    $query->free(true) ;
  }

  /**
   * Updates all shifts associated with $eventFacilityResourceID to use absolute timestamps per
   * activation protocol
   *
   * @param integer $eventFacilityResourceId The facility resource ID to update
   * @param timestamp $activationTime The initial activation time for shifts
   * @return none
   */  
  public static function activateEventFacilityResourceShifts($eventFacilityResourceId, $activationTime)
  {
    // capture the relative times and shifts associated with the event_facility_resource_id
    $query = Doctrine_Query::create()
      ->select('es.id')
        ->addSelect('es.minutes_start_to_facility_activation')
        ->addSelect('es.task_length_minutes')
        ->addSelect('es.break_length_minutes')
      ->from('agEventShift es')
      ->where('es.event_facility_resource_id = ?', $eventFacilityResourceId) ;

    // don't hydrate for quicker operation
    $results = $query->execute(array(), Doctrine_Core::HYDRATE_NONE);

    // loop through each shift and update times to absolute
    foreach ($results as $result)
    {
      $eventShiftId = $result[0] ;
      $relativeStart = $result[1] ;
      $taskLength = $result[2] ;
      $breakLength = $result[3] ;

      // need this bit to gracefully handle $relativeStart's +/- status
      $startModifier = ($relativeStart >= 0) ? '+{$relativeStart} minutes' : '{$relativeStart} minutes' ;

      // build our new timestamp values
      $startTime = strtotime($startModifier, $relativeStart) ;
      $endTime = strtotime('+{$taskLength} minutes', $startTime) ;
      $breakStart = $endTime ;
      $breakEnd = strtotime('+{$breakLength} minutes', $breakStart) ;

      // update the shift
      $this->updateEventShiftTime($eventShiftId, $startTime, $endTime, $breakStart, $breakEnd) ;
    }
  }

  /**
   * Updates all shifts associated with $eventFacilityGroupID to use absolute timestamps per
   * activation protocol.
   *
   * Note: This only activates those facilities that are committed, but are not standby
   *
   * @param integer $eventFacilityGroupId The facility group ID to update
   * @param timestamp $activationTime The initial activation time for shifts
   * @return none
   */
  public static function activateEventFacilityGroupShifts($eventFacilityGroupId, $activationTime)
  {
    // capture the facilities associated with the event_facility_group_id
    $query = Doctrine_Query::create()
      ->select('efr.id')
      ->from('agEventFacilityResource efr')
        ->innerJoin('efr.agEventFacilityResourceStatus efrs')
        ->innerJoin('efrs.agFacilityResourceAllocationStatus fras')
      ->where('EXISTS (
          SELECT s.id
            FROM agEventFacilityResourceStatus s
            WHERE s.event_facility_resource_id = efrs.id
              AND s.time_stamp <= CURRENT_TIMESTAMP
            HAVING MAX(s.time_stamp) = efrs.time_stamp)')
        ->andWhere('efr.event_facility_group_id = ?', $eventFacilityGroupId)
        ->andWhere('fras.committed = ?', true)
        ->andWhere('fras.standby = ?', false) ;

    // don't hydrate for quicker operation
    $results = $query->execute(array(), Doctrine_Core::HYDRATE_NONE);

    // loop through each facility and update
    foreach ($results as $result)
    {
      self::activateEventFacilityResourceShifts($result[0], $activationTime) ;
    }
  }

  /**
   * Updates all shifts associated with $event to use absolute timestamps per
   * activation protocol.
   *
   * Note: This only activates those facilities that are committed, but are not standby
   *
   * @param integer $eventId The event ID to update
   * @param timestamp $activationTime The initial activation time for shifts
   * @return none
   */
  public static function activateEventShifts($eventId, $activationTime)
  {
    // capture the facilities associated with the event_facility_group_id
    $query = Doctrine_Query::create()
      ->select('efg.id')
      ->from('agEventFacilityGroup efg')
        ->innerJoin('efg.agEventFacilityGroupStatus efgs')
        ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
      ->where('EXISTS (
          SELECT s.id
            FROM agEventFacilityGroupStatus s
            WHERE s.event_facility_group_id = efgs.id
              AND s.time_stamp <= CURRENT_TIMESTAMP
            HAVING MAX(s.time_stamp) = efgs.time_stamp)')
        ->andWhere('efg.event_id = ?', $eventId)
        ->andWhere('fgas.active = ?', true) ;

    // don't hydrate for quicker operation
    $results = $query->execute(array(), Doctrine_Core::HYDRATE_NONE);

    // loop through each facility and update
    foreach ($results as $result)
    {
      self::activateEventFacilityGroupShifts($result[0], $activationTime) ;
    }
  }
}