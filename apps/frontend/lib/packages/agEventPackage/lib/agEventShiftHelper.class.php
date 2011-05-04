<?php

/**
 * provides event shift management functions
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

class agEventShiftHelper
{
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
        $components = $shiftsQuery->getDql() ;
//   Chad, if you work on this soon, I commented out the line below because it was causing 
//   errors w/ the modal-modal. Somehow, the echo here was getting all the way back to the response,
//   so the string that was expected to be event/[event-name]/fgroup turned out to be something like
//   UPDATE agEventShift SET shift_status_id = ? WHERE id IN (?, ?, ?, ?, ?, ?, ?)event/[event-name]/fgroup.
//        echo $components ;
        // update shifts
        $eventShiftUpdates = $shiftsQuery->execute() ;

        // release staff if instructed to do so
        if ($releaseStaff) { $eventStaffUpdates = self::releaseShiftStaff($eventShiftIds, $conn) ; }

        // commit
        $conn->commit() ;
      }
      catch(Exception $e)
      {
        $conn->rollback(); // rollback if we must :(
    }

    // return our respective record operation counts
    return array('shiftUpdates' => $eventShiftUpdates, 'staffUpdates' => $eventStaffUpdates) ;
  }

  /**
   * Execute a delete query to release allocated staff from the passed event shift ids
   *
   * @param array $eventShiftIds A single-dimension array of all event shift id's from which
   * staff can be released
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return integer The number of rows affected
   * @todo This *must* be broken just from other issues found in it
   */
  public static function releaseShiftStaff($eventShiftIds, Doctrine_Connection $conn = NULL )
  {
    // set results default
    $results = 0 ;

    // set our default connection if one isn't passed and wrap it all in a transaction
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    $query = agDoctrineQuery::create($conn)
      ->delete('agEventStaffShift ess')
        ->where('NOT EXISTS(
            SELECT essi.id
              FROM agEventStaffSignIn essi
              WHERE essi.event_staff_shift_id = ess.id)')
          ->andWhereIn('ess.event_shift_id', $eventShiftIds) ;

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
   * Function to return the first shifts of each facility resource for a given event.
   *
   * @param integer(4) $eventId The current event being queried
   * @param boolean $staffed An optional parameter to determine whether or not to restrict results
   * to those that are staffed (TRUE) or not staffed (FALSE). By default this parameter is ignored.
   * @return array A two-dimensional associative array keyed by event_shift_id with value array
   * members as event_facility_resource_id
   */
  public static function returnFirstFacilityResourceShifts($eventId, $staffed = NULL)
  {
    $query = agDoctrineQuery::create()
      ->select('es.id')
          ->addSelect('es.event_facility_resource_id')
        ->from('agEventShift es')
          ->innerJoin('es.agEventFacilityResource efr')
          ->innerJoin('efr.agEventFacilityGroup efg')
        ->where('efg.event_id = ?', $eventId)
          ->andWhere('EXISTS (
          SELECT s.id
            FROM agEventShift s
            WHERE s.event_facility_resource_id = es.event_facility_resource_id
            HAVING MIN(s.minutes_start_to_facility_activation) = es.minutes_start_to_facility_activation)') ;

    if (! is_null($staffed))
    {
      if ($staffed)
      {
        $query->innerJoin('es.agEventStaffShift ess') ;
      }
      else
      {
        $query->leftJoin('es.agEventStaffShift ess') ;
        $query->andWhere('ess.id IS NULL') ;
      }
    }

    $results = $query->execute(array(), 'key_value_array') ;
    return $results ;
  }

  /**
   * Method to return an associative array keyed by event facility resource id with a value of one
   * of the first shifts of the facility resource. Because there can be many "first" shifts, only
   * one is returned. Parameters $staffed and $minEnd limit whether or not only staffed shifts will
   * be returned or whether or not the returned shift will be among those "first shifts" that ended
   * first or last.
   *
   * @param integer(4) $eventId The event being queried.
   * @param boolean $staffed An optional parameter to determine whether or not to restrict results
   * to those that are staffed (TRUE) or not staffed (FALSE). By default this parameter is ignored.
   * @param boolean $minEnd Parameter to determine whether the "first shift" returned will also be
   * the first or last to end. If $minEnd = TRUE (the default), the the returned shift will be among
   * those that ended first.
   * @return array A key value array of ($event_facility_resource_id => $shift_id)
   */
  public static function returnSingleFirstFacilityResourceShifts($eventId, $staffed = NULL, $minEnd = TRUE)
  {
    $firstShifts = array_keys( self::returnFirstFacilityResourceShifts($eventId, $staffed) ) ;

    $query = agDoctrineQuery::create()
      ->select('es.event_facility_resource_id')
        ->addSelect('MIN(es.id) AS min_shift_id')
        ->from('agEventShift es')
        ->whereIn('es.id', $firstShifts)
        ->groupBy('es.event_facility_resource_id') ;

    if ($minEnd)
    {
      $query->andWhere('EXISTS (
        SELECT s.id,
          MIN(s.minutes_start_to_facility_activation +
            s.task_length_minutes +
            s.break_length_minutes) AS endTimeS,
          (es.minutes_start_to_facility_activation +
            es.task_length_minutes +
            es.break_length_minutes) as endTimeEs
          FROM agEventShift s
          WHERE s.id = es.id
          HAVING endTimeS = endTimeEs)') ;
    }
    else
    {
      $query->andWhere('EXISTS (
        SELECT s.id,
          MAX(s.minutes_start_to_facility_activation +
            s.task_length_minutes +
            s.break_length_minutes) AS endTimeS,
          (es.minutes_start_to_facility_activation +
            es.task_length_minutes +
            es.break_length_minutes) as endTimeEs
          FROM agEventShift s
          WHERE s.id = es.id
          HAVING endTimeS = endTimeEs)') ;
    }

    $singleFirstShifts = $query->execute(array(),'key_value_pair') ;
    return $singleFirstShifts ;
  }

  /**
   * Return an array keyed by the shift id of any shifts that are currently in operation
   *
   * @param integer(4) $eventId The event being queried
   * @param boolean $staffed An optional parameter to determine whether or not to restrict results
   * to those that are staffed (TRUE) or not staffed (FALSE). By default this parameter is ignored.
   * @param timestamp $timestamp A optional value that adjusted the concept of 'current' from the application's
   * CURRENT_TIMESTAMP (or PHP time()) to the passed value (essentially enabling the user to ask
   * what shifts will be active at this point-in-$time
   * @return array An two-dimensional associative array, keyed by event_shift_id with a value
   * array of event_facility_resource_id
   */
  public static function returnCurrentFacilityResourceShifts($eventId, $staffed = NULL, $timestamp = NULL)
  {
    // convert our start time to unix timestamp or set default if null
    if (is_null($timestamp)) { $timestamp = time() ; }

    // create our basic query
    $query = agDoctrineQuery::create()
      ->select('es.id')
          ->addSelect('es.event_facility_resource_id')
        ->from('agEventShift es')
          ->innerJoin('es.agEventFacilityResource efr')
          ->innerJoin('efr.agEventFacilityGroup efg')
          ->innerJoin('efr.agEventFacilityResourceActivationTime efrat')
        ->where('efg.event_id = ?', $eventId)
          ->andWhere('(efrat.activation_time +
            (60 * es.minutes_start_to_facility_activation)) <= ?', $timestamp)
          ->andWhere('(efrat.activation_time +
            (60 * (es.minutes_start_to_facility_activation +
            (es.task_length_minutes + es.break_length_minutes)))) >= ?', $timestamp) ;

    // determine whether or not it needs to be joined to the staff table
    if (! is_null($staffed))
    {
      if ($staffed)
      {
        $query->innerJoin('es.agEventStaffShift ess') ;
      }
      else
      {
        $query->leftJoin('es.agEventStaffShift ess')
          ->andWhere('ess.id IS NULL') ;
      }
    }

    $results = $query->execute(array(), 'key_value_array');
    return $results;
  }

  /**
   * Method to return an associative array keyed by event facility resource id with a value of one
   * of the current shifts of the facility resource. Because there can be many "current" shifts, only
   * one is returned. Parameters $staffed and $minEnd limit whether or not only staffed shifts will
   * be returned or whether or not the returned shift will be among those "current shifts" that ended
   * first or last.
   *
   * @param integer(4) $eventId The event being queried.
   * @param boolean $staffed An optional parameter to determine whether or not to restrict results
   * to those that are staffed (TRUE) or not staffed (FALSE). By default this parameter is ignored.
   * @param timestamp $time A optional value that adjusted the concept of 'current' from the application's
   * CURRENT_TIMESTAMP (or PHP time()) to the passed value (essentially enabling the user to ask
   * what shifts will be active at this point-in-$time
   * @param boolean $minEnd Parameter to determine whether the "first shift" returned will also be
   * the first or last to end. If $minEnd = TRUE (the default), the the returned shift will be among
   * those that ended first.
   * @return array A key value array of ($event_facility_resource_id => $shift_id)
   */
  public static function returnSingleCurrentFacilityResourceShifts($eventId, $staffed = NULL, $timestamp = NULL, $minEnd = TRUE)
  {
    $currentShifts = array_keys( self::returnCurrentFacilityResourceShifts($eventId, $staffed, $timestamp) ) ;

    $shiftQuery = agDoctrineQuery::create()
      ->select('es.event_facility_resource_id')
        ->addSelect('MIN(es.id) AS shift_id')
        ->from('agEventShift es')
        ->whereIn('es.id', $currentShifts)
        ->groupBy('es.event_facility_resource_id') ;

    if ($minEnd)
    {
      $query->andWhere('EXISTS (
        SELECT s.id,
          MIN(s.minutes_start_to_facility_activation +
            s.task_length_minutes +
            s.break_length_minutes) AS endTimeS,
          (es.minutes_start_to_facility_activation +
            es.task_length_minutes +
            es.break_length_minutes) as endTimeEs
          FROM agEventShift s
          WHERE s.id = es.id
          HAVING endTimeS = endTimeEs)') ;
    }
    else
    {
  $query->andWhere('EXISTS (
        SELECT s.id,
          MAX(s.minutes_start_to_facility_activation +
            s.task_length_minutes +
            s.break_length_minutes) AS endTimeS,
          (es.minutes_start_to_facility_activation +
            es.task_length_minutes +
            es.break_length_minutes) as endTimeEs
          FROM agEventShift s
          WHERE s.id = es.id
          HAVING endTimeS = endTimeEs)') ;
    }

    $singleCurrentShifts = $shiftQuery->execute(array(),'key_value_pair') ;
    return $singleCurrentShifts ;
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
        ->from('agShiftStatus ss')
        ->where('ss.shift_status = ?', agGlobal::getParam('shift_disabled_status')) ;

    $disabledStatusId = $statusQuery->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR) ;
    return $disabledStatusId ;
  }
}