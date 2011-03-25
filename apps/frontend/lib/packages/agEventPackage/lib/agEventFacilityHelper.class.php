<?php

/**
 * provides event facility management functions
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

    $query = agDoctrineQuery::create()
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
    $singleFirstShifts = array_values(agEventShiftHelper::returnSingleFirstFacilityResourceShifts($eventId, FALSE)) ;

    // grabbing our blackout facilities
    $blacklistFacilities = array_keys(self::returnActivationBlacklistFacilities($eventId)) ;

    // here lies the meat of this function
    $query = agDoctrineQuery::create()
      ->select('efr.id')
        ->addSelect('f.facility_name')
        ->addSelect('fr.facility_resource_code')
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
        ->andWhereNotIn('efr.id',$blacklistFacilities) ;

    if (! is_null($eventFacilityGroupId)) { $query->andWhere('efg.id = ?', $eventFacilityGroupId) ; }

    if (! is_null($facilityStandbyStatus)) { $query->andWhere('ras.standby = ?', $facilityStandbyStatus) ; }
        
    $results = $query->execute() ;

    return $results ;
  }

  /**
   * Sets the activation time for a group of event facility resources and optionally disables unused
   * or unnecessary resources.
   *
   * @param integer(4) $eventId The event this action occurs against.
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
  public static function setFacilityActivationTime ($eventId, $eventFacilityResourceIds, $activationTime, $shiftChangeRestriction = TRUE, $releaseStaff = FALSE, Doctrine_Connection $conn = NULL)
  {
    // create a new connection object if one is not passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    // convert our dates
    $shiftOffset = ($shiftChangeRestriction) ? (agGlobal::getParam('shift_change_restriction') * 60) : 0 ;
    $currentTimestamp = time() ;

    // do a check for illegal ops (eg staffed facility resource id's < window for set
    $blacklistFacilities = array_keys(self::returnActivationBlacklistFacilities($eventId, $activationTime, $shiftChangeRestriction)) ; 
    $blacklistCount = count(array_intersect($eventFacilityResourceIds, $blacklistFacilities)) ;

    // throw an exception if there is an intersection between the blackout facilities and those
    // to-be acted upon
    if ($blacklistCount > 0) {
      throw new sfStopException('Not Allowed: You are attempting to set the activation time of facilities that are blacklisted for changes.');
    }

    // set vars
    $updatedActivationTimes = 0 ;
    $insertedActivationTimes = 0 ;

    // collect the disabled status to which blackout facilities will be set
    $disabledStatusId = agEventShiftHelper::returnDisabledShiftStatus() ;

    // get inserts
    $insertQuery = agDoctrineQuery::create()
      ->select('efr.id')
        ->from('agEventFacilityResource efr')
          ->leftJoin('efr.agEventFacilityResourceActivationTime efrat')
        ->where('efrat.id IS NULL')
          ->andWhereIn('efr.id', $eventFacilityResourceIds) ;
    $insertIds = $insertQuery->execute(array(), 'single_value_array') ;
    $insertedActivationTimes = count($insertIds) ;

    // define update existing query
    $updateIds = array_diff($eventFacilityResourceIds, $insertIds) ;
    $updateQuery = agDoctrineQuery::create($conn)
      ->update('agEventFacilityResourceActivationTime')
      ->set('activation_time', '?', $activationTime)
      ->whereIn('event_facility_resource_id', $updateIds)
      ->setConnection($conn);

    // define blackout query
    $disabledShiftQuery = agDoctrineQuery::create($conn)
      ->select('es.id')
        ->from('agEventShift es')
        ->whereIn('event_facility_resource_id', $eventFacilityResourceIds)
          ->andWhere('((minutes_start_to_facility_activation * 60) + ? + ?) < ?', array($shiftOffset, $currentTimestamp, $activationTime)) ;
    $disabledShiftIds = $disabledShiftQuery->execute(array(), 'single_value_array') ;

    // wrap it all in a transaction and a try/catch to rollback if an exception occurs
    $conn->beginTransaction() ;
    try
    {
      // change the status of shifts that will not be used (due to blackout windows)
      $disabledShifts = agEventShiftHelper::setEventShiftStatus($disabledShiftIds, $releaseStaff) ;

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
        
        // since we've got it in memory, we can free it, right?
        // $efrat->free() ;
      }

      // save the collection
      $insertCollection->save($conn);

      // commit
      $conn->commit() ;
    }
    catch(Exception $e)
    {
      $conn->rollback();
    }

    $results = $disabledShifts ;
    $results['updatedActivationTimes'] = $updatedActivationTimes ;
    $results['insertedActivationTimes'] = $insertedActivationTimes ;

    return $results ;
  }

  /**
   * Method to set the zero hour of an event and set facility activation times for committed, non-
   * standby facilities.
   *
   * @param integer(4) $eventId The event being updated.
   * @param timestamp $activationTime The activation time being applied.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array An array containing the number of facility operations performed.
   */
  public static function setEventZeroHour ($eventId, $activationTime, Doctrine_Connection $conn = NULL)
  {
    // set counters
    $updates = 0 ;
    $inserts = 0 ;

    // set time variables
    $currentTimestamp = time() ;

    // select current statuses
    $resourceStatuses = self::returnCurrentEventFacilityResourceStatus($eventId) ;
    $groupStatuses = self::returnCurrentEventFacilityGroupStatus($eventId) ;

    // select event facilities where status = committed
    $activeFacilityQuery = agDoctrineQuery::create()
      ->select('efr.id')
      ->from('agEventFacilityResource efr')
        ->innerJoin('efr.agEventFacilityResourceStatus ers')
        ->innerJoin('ers.agFacilityResourceAllocationStatus ras')
        ->innerJoin('efr.agEventFacilityGroup efg')
        ->innerJoin('efg.agEventFacilityGroupStatus egs')
        ->innerJoin('egs.agFacilityGroupAllocationStatus gas')
        ->innerJoin('efr.agEventShift es')
      ->whereIn('ers.id', $resourceStatuses)
        ->andWhereIn('egs.id', $groupStatuses)
        ->andWhere('gas.active = ?', TRUE)
        ->andWhere('ras.committed = ?', TRUE)
        ->andWhere('ras.standby = ?', FALSE);

    $activeFacilities = $activeFacilityQuery->execute(array(), 'single_value_array') ;

    // do a check for facilities that will not be affected
    $blacklistFacilities = array_keys(self::returnActivationBlacklistFacilities($eventId, $activationTime, TRUE)) ;

    // remove blacklist facilities from the actionable list
    $actionableFacilities = array_diff($activeFacilities, $blacklistFacilities) ;

    // create a new connection object if one is not passed and wrap it in a transaction
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }
    $conn->beginTransaction() ;

    try
    {
      // set the event's zero-hour
      $event = Doctrine::getTable('agEvent')->find($eventId) ;
      $event->set('zero_hour',$activationTime) ;
      $event->save() ;
      
      // run the automatic activation time update script
      $results = self::setFacilityActivationTime($eventId, $actionableFacilities, $activationTime, TRUE, TRUE) ;

      // commit
      $conn->commit() ;
    }
    catch(Exception $e)
    {
      $conn->rollback();
    }

    return $results ;
  }

  public static function getCurrentEventFacilityResourceStatus($eventId, $eventFacilityResourceIds)
  {
    $currentStatusIds = agEventFacilityHelper::returnCurrentEventFacilityResourceStatus($eventId) ;

    $q = agDoctrineQuery::create()
      ->select('efrs.event_facility_resource_id')
          ->addSelect('efrs.facility_resource_allocation_status_id')
        ->from('agEventFacilityResourceStatus efrs')
        ->whereIn('efrs.id', array_keys($currentStatusIds))
          ->andWhereIn('efrs.event_facility_resource_id', $eventFacilityResourceIds);

      $results = $q->execute(array(), 'key_value_pair') ;

      return $results ;
  }

  /**
   * Static function to return the current status id's of all facility resources for a specific event
   * 
   * To get the current status of all facilities in a current event
   * $currentStatusIds = agEventFacilityHelper::returnCurrentFacilityResourceStatus($eventId) ;
   *
   * $q = agDoctrineQuery::create()
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
    $query = agDoctrineQuery::create()
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
   * <code>
   * // To get the current status of all facilities in a current event
   * $currentStatusIds = agEventFacilityHelper::returnCurrentFacilityGroupStatus($eventId) ;
   *
   * $q = agDoctrineQuery::create()
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
    $query = agDoctrineQuery::create()
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
   * @param  integer(4) $eventId The event id being queried
   * @return integer    An event_status_type_id.
   */
  public static function returnCurrentEventStatus($eventId)
  {
    $query = agDoctrineQuery::create()
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
   * Returns facilities that are currently in the blackout period based on the passed $activation time.
   *
   * @param integer(4) $eventId The current event being queried.
   * @param timestamp $activationTime The activation time being checked against.
   * @param boolean $shiftChangeRestriction Determines whether or not to apply the offset of the
   * shift_change_restriction global parameter.
   * @return array A single-dimension array containing event_facility_resource_ids.
   */
  public static function returnActivationBlacklistFacilities($eventId, $activationTime = NULL, $shiftChangeRestriction = FALSE)
  {
    // get our statuses
    $groupStatuses = array_keys( self::returnCurrentEventFacilityGroupStatus($eventId) ) ;
    $resourceStatuses = array_keys( self::returnCurrentEventFacilityResourceStatus($eventId) ) ;

    // get the first shift per fac resource for time's sake
    $firstShifts = array_values(agEventShiftHelper::returnSingleFirstFacilityResourceShifts($eventId, TRUE)) ;
    if (empty($firstShifts)) { $firstShifts[] = NULL ; }
    
    // initialize where clause parameter array
    $queryAndWhereParams = array() ;

    // build a nested where clause using named parameters
    $queryAndWhere = '(frs.is_available = ?
      OR (fgas.allocatable = ?
        AND fgas.standby = ?
        AND fgas.active = ?)
      OR fras.committed = ? ' ; // don't forget the trailing space, please

    // add the relevant params
    $queryAndWhereParams[] = FALSE ; // frsAvailable
    $queryAndWhereParams[] = FALSE ; // fgsAllocatable
    $queryAndWhereParams[] = FALSE ; // fgsStandby
    $queryAndWhereParams[] = FALSE ; // fgsActive
    $queryAndWhereParams[] = FALSE ; // frasCommitted

    // if passed an $activationTime add more restrictions
    if ((! is_null($activationTime)) && (! empty($firstShifts)))
    {
        // get some time variables setup
        $shiftOffset = ($shiftChangeRestriction) ? (agGlobal::getParam('shift_change_restriction') * 60) : 0 ;
        $currentTimestamp = time() ;
        $activationOffset = ($activationTime - $shiftOffset) ;

        // append to the where statement
        $queryAndWhere =  $queryAndWhere . 'OR (es.id IN ?
          AND (es.minutes_start_to_facility_activation + ?) < ?)'  ;

        $queryAndWhereParams[] = $firstShifts ; // esArray
        $queryAndWhereParams[] = $currentTimestamp ; // currentTimestamp
        $queryAndWhereParams[] = $activationOffset ; //activationOffset
    }

    // close out the where clause
    $queryAndWhere = $queryAndWhere . ')' ;

    // build the query object
    $query = agDoctrineQuery::create()
      ->select('efr.id')
          ->addSelect('f.facility_name')
          ->addSelect('fr.facility_resource_code')
          ->addSelect('frt.facility_resource_type_abbr')
          ->addSelect('efg.event_facility_group')
          ->addSelect('fr.id')
        ->from('agEventFacilityResource efr')
          ->innerJoin('efr.agEventShift es')
          ->innerJoin('efr.agEventFacilityResourceStatus efrs')
          ->innerJoin('efrs.agFacilityResourceAllocationStatus fras')
          ->innerJoin('efr.agEventFacilityGroup efg')
          ->innerJoin('efg.agEventFacilityGroupStatus efgs')
          ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
          ->innerJoin('efr.agFacilityResource fr')
          ->innerJoin('fr.agFacility f')
          ->innerJoin('fr.agFacilityResourceType frt')
          ->innerJoin('fr.agFacilityResourceStatus frs')
        ->where('efg.event_id = ?', $eventId)
          ->andWhereIn('efrs.id', $resourceStatuses)
          ->andWhereIn('efgs.id', $groupStatuses)
          ->andWhere($queryAndWhere, $queryAndWhereParams) ;

    $results = $query->execute(array(), 'key_value_array') ;
    return $results ;
  }

  /**
   * Method to return all Facility Resource Status ID's that match a columnar parameter
   *
   * @param string $column The boolean column name to be queried.
   * @param boolean $match The value (negative or positive) of the match. Defaults to TRUE.
   * @return array An array of facility_resource_allocation_status_ids.
   * @todo This function, and its kin should be magic so that
   * getFacilityResourceAllocationStatus('staffed', FALSE) get*
   */
  public static function getFacilityResourceAllocationStatus ($column, $match = TRUE)
  {
    $query = agDoctrineQuery::create()
      ->select('id')
        ->from('agFacilityResourceAllocationStatus')
        ->where($column . '= ?', $match) ;

    $results = $query->execute(array(), 'single_value_array') ;
    return $results ;
  }

  /**
   * Method to release event facility resource.
   *
   * @param integer(5) $eventFacilityResourceId The id of the event facility resource to which this
   * is applied.
   * @param timestamp $actionTime An optional time uses as a basis for the time this action is
   * applied. Defaults to CURRENT_TIMESTAMP.
   * @param boolean $shiftChangeRestriction An optional boolean determining whether or not the
   * shift change restriction will be applied in the release of facility resources. Defaults to TRUE.
   * @param Doctrine_Connection $conn An optional doctrine connection object.
   */
  public static function releaseEventFacilityResource ($eventFacilityResourceId, $actionTime = NULL, $shiftChangeRestriction = TRUE, Doctrine_Connection $conn = NULL)
  {
    // set up our basic time parameters
    $shiftOffset = ($shiftChangeRestriction) ? (agGlobal::getParam('shift_change_restriction') * 60) : 0 ;
    if (is_null($actionTime)) { $actionTime = time() ; }
    $actionTimeOffset = ($actionTime + $shiftOffset) ;

    // pick up our disabled shift status
    $disabledStatusId = agEventShiftHelper::returnDisabledShiftStatus() ;

    // define a nested where clause
    $andWhereClause = '(efrat.id IS NULL
      OR ((efrat.activation_time + es.minutes_start_to_facility_activation) > ?))' ;

    $query = agDoctrineQuery::create()
      ->select('es.id')
        ->from('agEventShift es')
          ->addFrom('es.agEventFacilityResource efr')
          ->addFrom('efr.agEventFacilityResourceActivationTime efrat')
        ->where('es.event_facility_resource_id = ?', $eventFacilityResourceId)
          ->andWhere($andWhereClause, $actionTimeOffset) ;
    $releasedShifts = $query->execute(array(), 'key_value_array') ;

    $results = agEventShiftHelper::setEventShiftStatus($releasedShifts, $disabledStatusId, TRUE) ;
    return $results ;
  }

  /**
   * Method to execute a mass-update to event facility resource statuses.
   *
   * @param array $eventIds An array of event id's being affected.
   * @param array $facilityResourceIds An array of facility resource id's affected by this action.
   * @param integer(2) $allocationStatusId The status being applied.
   * @param timestamp $actionTime The time this action will be applied. Defaults to NOW().
   * @param Doctrine_Connection $conn An optional doctrine connection object.
   * @return integer The number of operations performed.
   */
  public static function setEventFacilityResourceStatus ($eventIds, $facilityResourceIds, $allocationStatusId, $actionTime = NULL, Doctrine_Connection $conn = NULL)
  {
    // set up some variables
    $updates = 0 ;
    if (is_null($actionTime)) { $actionTime = time() ; }
    $tsString = agDateTimeHelper::tsToString($actionTime) ;

    // grab our facility resource groups
    $eventFacilityResourceQuery = agDoctrineQuery::create()
      ->select('efr.id')
        ->from('agEventFacilityResource efr')
          ->innerJoin('efr.agEventFacilityGroup efg')
        ->whereIn('efg.event_id', $eventIds)
          ->andWhereIn('efr.facility_resource_id', $facilityResourceIds) ;
    $eventFacilityResourceIds = $eventFacilityResourceQuery->execute(array(), 'single_value_array') ;

    // set our default connection if one is not passed and wrap it all in a transaction
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }
    $conn->beginTransaction() ;
    try
    {
      // set up a new collection for inserts
      $collection = new Doctrine_Collection('agEventFacilityResourceStatus');

      foreach ($eventFacilityResourceIds as $id)
      {
        // build our values array
        $data = array() ;
        $data['event_facility_resource_id'] = $id ;
        $data['time_stamp'] = $tsString ;
        $data['facility_resource_allocation_status_id'] = $allocationStatusId ;

        // new efrat object
        $efrs = new agEventFacilityResourceStatus();
        $efrs->fromArray($data) ;

        // add it to the collection.
        $collection->add($efrs);

        $updates++ ;
      }

      // save the collection
      $collection->save();

      // commit
      $conn->commit() ;
    }
    catch(Exception $e)
    {
      $conn->rollback(); // rollback if we must :(
    }

    return $updates ;
  }
}
