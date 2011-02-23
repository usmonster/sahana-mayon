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
    $query = agDoctrineQuery::create()
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
    $query = agDoctrineQuery::create()
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
    $query = agDoctrineQuery::create()
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
    $query = agDoctrineQuery::create()
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

  /**
   * Changes the status of a specified event shift and optionally calls a function to release staff
   * of disabled shifts.
   *
   * @param array $eventShiftIds An array of event shift id's.
   * @param integer(2) $shiftStatusId The shift status id that will be set via this action
   * @param boolean $releaseStaff An optional boolean that determines whether or not existing staff
   * will be released from these shifts.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array An array representing the number of shifts affected and number of staff released.
   */
  public static function setEventShiftStatus ($eventShiftIds, $shiftStatusId, $releaseStaff = FALSE, Doctrine_Connection $conn = NULL)
  {
    $eventStaffUpdates = 0 ;
    $eventShiftUpdates = 0 ;

    // set our default connection if one is not passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    // query construction
    $shiftsQuery = agDoctrineQuery::create($conn)
      ->update('agEventShift')
        ->set('shift_status_id', '?', $shiftStatusId)
        ->whereIn('id', $eventShiftIds) ;

    // wrap it all in a transaction and a try/catch to rollback if an exception occurs
    $conn->beginTransaction() ;
    try
    {
      // update shifts
      $eventShiftUpdates = $shiftsQuery->execute() ;

      // release staff if instructed to do so
      if ($releaseStaff) { $eventStaffUpdates = self::releaseShiftStaff($eventShiftIds) ; }
      
      // commit
      $conn->commit() ;
    }
    catch(Exception $e)
    {
      $conn->rollback(); // rollback if we must :(
    }

    // return our respective record operation counts
    return array($eventShiftUpdates,$eventStaffUpdates) ;
  }

  /**
   * Execute a delete query to release allocated staff from the passed event shift ids
   *
   * @param array $eventShiftIds A single-dimension array of all event shift id's from which
   * staff can be released
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return integer The number of rows affected
   */
  public static function releaseShiftStaff($eventShiftIds, Doctrine_Connection $conn = NULL )
  {
    // set our default connection if one isn't passed
    if (! is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    $query = agDoctrineQuery::create($conn)
      ->delete('agEventStaffShift ess')
        ->where('NOT EXISTS(
            SELECT essi.id
              FROM agEventStaffSignIn essi
              WHERE essi.event_staff_shift_id = ess.id)')
          ->andWhereIn('ess.event_shift_id', $eventShiftIds) ;

    // wrap it all in a transaction and make magic happen
    $conn->beginTransaction() ;
    try
    {
      $results = $query->execute() ;
      $conn->commit() ;
    }
    catch(Exception $e)
    {
      $conn->rollback() ;
    }

    // returns the record count
    return $results ;
  }

  /**
   * Returns the shift status id of the shift_disabled_status global parameter.
   *
   * @return integer(2) The shift status id of the global parameterized disabled shift status.
   */
  public static function returnDisabledShiftStatus()
  {
    $statusQuery = agDoctrineQuery::create()
      ->select('ss.id')
        ->from('ag_shift_status ss')
        ->where('ss.shift_status = ?', agGlobal::$param['shift_disabled_status']) ;

    $disabledStatusId = $statusQuery->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR) ;
    return $disabledStatusId ;
  }
}