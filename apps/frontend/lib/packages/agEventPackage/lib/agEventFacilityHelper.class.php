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
  /**
   * Function to return event facility groups with an optional parameter to restrict to active or
   * inactive facility group allocation status.
   *
   * @param integer(4) $eventId The event id being queried
   * @param boolean $active Optional parameter to restrict returned only active or inactive facility
   * groups.  Default setting (null) returns both.
   * @return array A scalar array of event facility groups with the group type, and group name.
   */
  public static function returnEventFacilityGroups ($eventId, $active = NULL)
  {

    $groupStatus = self::returnCurrentEventFacilityGroupStatus($eventId) ;

    $query = Doctrine_Query_Extra::create()
      ->select('efg.id')
        ->addSelect('efg.event_facility_group')
        ->addSelect('fgt.facility_group_type')
        ->addSelect('fgt.id')
      ->from('agEventFacilityGroup efg')
        ->innerJoin('efg.agFacilityGroupType fgt')
        ->innerJoin('efg.agEventFacilityGroupStatus egs')
        ->innerJoin('egs.agFacilityGroupAllocationStatus gas')
      ->where('efg.event_id = ?', $eventId)
        ->andWhereIn('egs.id', array_keys($groupStatus)) ;

    if (! is_null($active)) { $query->andWhere('gas.active = ?', $active) ; }

    $results = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    
    return $results ;
  }

  /**
   * Method to return facilities that are candidates for having their activation times set.
   * 
   * @param integer(4) $eventId The event id being queried.
   * @param integer(4) $eventFacilityGroupId An optional parameter to set a specific group by which
   * results can be filtered; default (NULL) returns all groups.
   * @param boolean $facilityStandbyStatus An optional parameter to restrict results only to those
   * facilities that are in a standby state; default (NULL) imposes no restriciton.
   * @return doctrine_collection Returns a collection of doctrine objects related to facility
   * resources.
   */
  public static function returnFacilityResourceActivation ($eventId, $eventFacilityGroupId = NULL, $facilityStandbyStatus = NULL)
  {
    // these are relatively simple where clauses
    $groupStatuses = array_keys( self::returnCurrentEventFacilityGroupStatus($eventId) ) ;
    $resourceStatuses = array_keys( self::returnCurrentEventFacilityResourceStatus($eventId) ) ;

    // grabbing just one of the first shifts per facility resource
    $singleFirstShifts = array_values(self::returnSingleFirstFacilityResourceShifts($eventId, FALSE)) ;

    // here lies the meat of this function
    $query = Doctrine_Query_Extra::create()
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
      ->whereIn('ers.id', $resourceStatuses)
        ->andWhereIn('egs.id', $groupStatuses)
        ->andWhereIn('es.id', $singleFirstShifts)
        ->andWhere('efat.id IS NULL')
        ->andWhere('frs.is_available = ?', true)
        ->andWhere('gas.active = ?', true)
        ->andWhere('(ras.committed = ?)', true)
        ->andWhere('efg.event_id = ?', $eventId) ;

    if (! is_null($eventFacilityGroupId)) { $query->andWhere('efg.id = ?', $eventFacilityGroupId) ; }

    if (! is_null($facilityStandbyStatus)) { $query->andWhere('ras.standby = ?', $facilityStandbyStatus) ; }
        
    $results = $query->execute() ;

    return $results ;
  }

  /**
   * Sets the activation time for a group of event facility resources and optionally disables unused
   * or unnecessary resources.
   *
   * @param array $eventFacilityResourceIds A simple, one-dimensional array of event facility
   * resource id's.
   * @param timestamp $activationTime The activation time to be applied.
   * @param boolean $shiftChangeRestriction An optional parameter whether or not to utilize the
   * global shift_change_restriction (aka a time offset barrier).
   * @param boolean $releaseStaff An optional parameter to control whether or not staff are
   * automatically released from any of the shifts that might be disabled due to this action.
   * Defaults to FALSE.
   * @param Doctrine_Connection $conn An optional doctrine connection object. If one is not passed a
   * new one will be created automatically.
   * @return array Returns an array containing the number of disabled shifts, updated records, and
   * inserted records.
   */
  public static function setFacilityActivationTime ($eventFacilityResourceIds, $activationTime, $shiftChangeRestriction = TRUE, $releaseStaff = FALSE, Doctrine_Connection $conn = NULL)
  {
    // convert our $shiftChangeRestriction to seconds
    $shiftOffset = ($shiftChangeRestriction) ? (agGlobal::$param('shift_change_restriction') * 60) : 0 ;

    // do a check for illegal ops (eg staffed facility resource id's < window for set
    $blacklistFacilities = self::returnActivationBlacklistFacilities($eventId, $activationTime, $shiftChangeRestriction) ;
    $blacklistCount = count(array_intersect($eventFacilityResourceIds, $blacklistFacilities)) ;

    // throw an exception if there is an intersection between the blackout facilities and those
    // to-be acted upon
    if ($blacklistCount > 0) {
      // HELP ME!!!
    }

    // set vars
    $disabledShifts = 0 ;
    $updatedActivationTimes = 0 ;
    $insertedActivationTimes = 0 ;

    // create a new connection object if one is not passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    // collect the disabled status to which blackout facilities will be set
    $disabledStatusId = agEventShiftHelper::returnDisabledShiftStatus() ;

    // get inserts
    $insertQuery = Doctrine_Query_Extra::create()
      ->select('efg.id')
        ->from('agEventFacilityResource efg')
          ->leftJoin('agEventFacilityResourceActivationTime efrat')
        ->where('efrat.id IS NULL')
          ->andWhereIn('efg.id', $eventFacilityResourceIds) ;
    $insertIds = $insertQuery->execute(array(), 'single_value_array') ;

    // define update existing query
    $updateQuery = Doctrine_Query_Extra::create($conn)
      ->update('agEventFacilityResourceActivationTime')
      ->set('activation_time', '?', $activationTime)
      ->whereIn('event_facility_resource_id', $eventFacilityResourceIds) ;

    // define blackout query
    // @todo JUST MAKE THIS A SELECT AND PASS THE SHIFT IDS TO THE UPDATE
    $blackoutQuery = Doctrine_Query_Extra::create($conn)
      ->update('ag_event_shift')
      ->set('shift_status_id', '?', $disabledStatusId)
      ->whereIn('event_facility_resource_id', $eventFacilityResourceIds)
        ->andWhere('(minutes_start_to_facility_activation + ?) < ?', array($shiftOffset, $activationTime)) ;

    // wrap it all in a transaction and a try/catch to rollback if an exception occurs
    $conn->beginTransaction() ;
    try
    {
      // change the status of shifts that will not be used (due to blackout windows)
      $disabledShifts = $blackoutQuery->execute() ;
      // $disabledShifts = agEventShiftHelper::setEventShiftStatus($eventShiftIds, $releaseStaff) ;

      // update existing
      $updatedActivationTimes = $updateQuery->execute() ;

      // set up a new collection for inserts
      $insertCollection = new Doctrine_Collection('agEventFacilityResourceActivationTime');

      foreach ($insertIds as $id)
      {
        // build our values array
        $data = array('event_facility_resource_id' => $id, 'activation_time' => $activationTime) ;

        // new efrat object
        $efrat = new agEventFacilityResourceActivationTime();
        $efrat->fromArray($data) ;

        // add it to the collection.
        $insertCollection->add($efrat);
      }

      // save the collection
      $insertCollection->save();

      // commit
      $conn->commit() ;
    }
    catch(Exception $e)
    {
      $conn->rollback();
    }

    return array($disabledShifts, $updatedActivationTimes, $insertedActivationTimes) ;
  }

  public static function setEventZeroHour ($eventId, $activationTime)
  {
    // select event facility id

    // cannot be staffed


    // -- in a fac_activation_time_update --//
    // disable shifts from before the zero hour

    // enable shifts from before based on boolean y/n?
    $query = Doctrine_Query_Extra::create()
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
      ->whereIn('ers.id', $resourceStatuses)
        ->andWhereIn('egs.id', $groupStatuses)
        ->andWhereIn('es.id', $shifts)
        ->andWhere('efat.id IS NULL')
        ->andWhere('frs.is_available = ?', true)
        ->andWhere('gas.active = ?', true)
        ->andWhere('(ras.allocatable = ? OR ras.committed = ?)', array(true, true))
        ->andWhere('ras.staffed = ?', false)
        ->andWhere('efg.event_id = ?', $eventId) ;
    $results = 'I don\'t do anything yet!' ;
    return $results;
  }

  /**
   * Static function to return the current status id's of all facility resources for a specific event
   *
   * <code>
   * // To get the current status of all facilities in a current event
   * $currentStatusIds = agEventFacilityHelper::returnCurrentFacilityResourceStatus($eventId) ;
   *
   * $q = Doctrine_Query_Extra::create()
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
   * $q = Doctrine_Query_Extra::create()
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
   * @param boolean $staffed An optional parameter to determine whether or not to restrict results
   * to those that are staffed (TRUE) or not staffed (FALSE). By default this parameter is ignored.
   * @return array A two-dimensional associative array keyed by event_shift_id with value array
   * members as event_facility_resource_id
   */
  public static function returnFirstFacilityResourceShifts($eventId, $staffed = NULL)
  {
    $query = Doctrine_Query::create()
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

    $query = Doctrine_Query_Extra::create()
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
   * @param string $time A optional value that adjusted the concept of 'current' from the application's
   * CURRENT_TIMESTAMP (or PHP time()) to the passed value (essentially enabling the user to ask
   * what shifts will be active at this point-in-$time
   * @return array An two-dimensional associative array, keyed by event_shift_id with a value
   * array of event_facility_resource_id
   */
  public static function returnCurrentFacilityResourceShifts($eventId, $staffed = NULL, $time = NULL)
  {
    // convert our start time to unix timestamp or set default if null
    $timestamp = agDateTimeHelper::defaultTimestampFormat($time) ;

    // create our basic query
    $query = Doctrine_Query::create()
      ->select('es.id')
          ->addSelect('es.event_facility_resource_id')
        ->from('agEventShift es')
          ->innerJoin('es.agEventFacilityResource efr')
          ->innerJoin('efr.agEventFacilityGroup efg')
          ->leftJoin('efr.agEventFacilityResourceActivationTime efrat')
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
   * @param string $time A optional value that adjusted the concept of 'current' from the application's
   * CURRENT_TIMESTAMP (or PHP time()) to the passed value (essentially enabling the user to ask
   * what shifts will be active at this point-in-$time
   * @param boolean $minEnd Parameter to determine whether the "first shift" returned will also be
   * the first or last to end. If $minEnd = TRUE (the default), the the returned shift will be among
   * those that ended first.
   * @return array A key value array of ($event_facility_resource_id => $shift_id)
   */
  public static function returnSingleCurrentFacilityResourceShifts($eventId, $staffed = NULL, $time = NULL, $minEnd = TRUE)
  {
    $currentShifts = array_keys( self::returnCurrentFacilityResourceShifts($eventId, $staffed, $time) ) ;

    $shiftQuery = Doctrine_Query_Extra::create()
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
   * Returns facilities that are currently in the blackout period based on the passed $activation time.
   *
   * @param integer(4) $eventId The current event being queried.
   * @param timestamp $activationTime The activation time being checked against.
   * @param boolean $shiftChangeRestriction Determines whether or not to apply the offset of the
   * shift_change_restriction global parameter.
   * @return array A single-dimension array containing event_facility_resource_ids.
   */
  public static function returnActivationBlacklistFacilities($eventId, $activationTime, $shiftChangeRestriction = TRUE)
  {
    // get our statuses
    $groupStatuses = array_keys( self::returnCurrentEventFacilityGroupStatus($eventId) ) ;
    $resourceStatuses = array_keys( self::returnCurrentEventFacilityResourceStatus($eventId) ) ;

    // get the first shift per fac resource for time's sake
    $firstShifts = array_values(self::returnSingleFirstFacilityResourceShifts($eventId, TRUE)) ;
    if (is_empty($firstShifts)) { $firstShifts[] = NULL ; }
    
    // get some time variables setup
    $shiftOffset = ($shiftChangeRestriction) ? (agGlobal::$param('shift_change_restriction') * 60) : 0 ;
    $currentTimestamp = time() ;

    // initialize where clause parameter array
    $queryAndWhereParams = array() ;

    // build a nested where clause using named parameters
    $queryAndWhere = '(frs.is_active = :frsActive
      OR (fgas.allocatable = :fgsAllocatable
        AND fgas.standby = :fgsStandby
        AND fgas.active = :fgsActive)
      OR fras.committed = :frasCommitted
      OR (es.id IN (:esArray)
        AND (es.minutes_start_to_facility_activation + :currentTimestamp)
          < (:activationTime - :shiftOffset)))' ;

    // add the relevant params
    $queryAndWhereParams[':esArray'] = $firstShifts ;
    $queryAndWhereParams[':currentTimestamp'] = $currentTimestamp ;
    $queryAndWhereParams[':activationTime'] = $activationTime ;
    $queryAndWhereParams[':shiftOffset'] = $shiftOffset ;
    $queryAndWhereParams[':frsActive'] = FALSE ;
    $queryAndWhereParams[':fgsAllocatable'] = FALSE ;
    $queryAndWhereParams[':fgsStandby'] = FALSE ;
    $queryAndWhereParams['fgsActive'] = FALSE ;
    $queryAndWhereParams[':frasCommitted'] = FALSE ;

    // merge the results with our class variable for facility group status
    $queryAndWhereParams = array_merge($queryAndWhereParams, self::$facility_group_status_disabled) ;

    // build the query object
    $query = Doctrine_Query_Extra::create()
      ->select('efr.id')
        ->from('agEventFacilityResource efr')
          ->innerJoin('efr.agEventShift es')
          ->innerJoin('efr.agEventFacilityResourceStatus efrs')
          ->innerJoin('efrs.agFacilityResourceAllocationStatus fras')
          ->innerJoin('efr.agEventFacilityGroup efg')
          ->innerJoin('efg.agEventFacilityGroupStatus efgs')
          ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
          ->innerJoin('efr.agFacilityResource fr')
          ->innerJoin('fr.afFacilityResourceStatus frs')
        ->where('efg.id = ?', $eventId)
          ->andWhereIn('efrs.id', $resourceStatuses)
          ->andWhereIn('efgs.id', $groupStatuses)
          ->andWhere($queryAndWhere, $queryAndWhereParams) ;

    $results = $query->execute(array(), 'single_value_array') ;
    return $results ;
  }


}
