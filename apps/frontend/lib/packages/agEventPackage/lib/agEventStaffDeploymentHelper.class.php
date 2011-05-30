<?php
/**
 * Provides methods related to event staff deployment
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

class agEventStaffDeploymentHelper extends agPdoHelper
{
  const CONN_READ = 'deploy_read';
  const CONN_WRITE = 'deploy_write';
  const QUERY_SHIFTS = 'query_shifts';

  protected $eventId,
            $eventStaffDeployedStatusId,
            $eventFacilityGroups = array(),
            $addrGeoTypeId,
            $shiftOffset,
            $skipUnfilled,
            $err = FALSE,
            $startTime,
            $endTime;

  protected $batchSize,
            $fetchCount,
            $fetchPos,
            $totalWaves,
            $totalShifts,
            $totalStaff,
            $facGrpPos,
            $facGrpCt,
            $currFacGrpId,
            $iterNextGrp;

  /**
   * @var agEventHandler An instance of agEventHandler
   */
  protected   $eh;

  /**
   * Method to return an instance of agEventStaffDeploymentHelper
   * @param integer $eventId An event ID
   * @return self An instance of agEventStaffDeploymentHelper
   */
  public static function getInstance($eventId, $eventDebugLevel = NULL)
  {
    $esdh = new self();
    $esdh->__init($eventId, $eventDebugLevel);
    return $esdh;
  }

  /**
   * A method to act like this classes' own constructor
   * @param integer $eventId An event ID
   * @param boolean $skipUnfilled Tells the class whether or not to halt on facility shifts that
   * could not be filled.
   * @param string $eventDebugLevel One of the EVENT_* constants of agEventHandler
   */
  protected function __init($eventId, $skipUnfilled = TRUE, $eventDebugLevel = NULL)
  {
    // start our timer
    $this->startTime = time();

    // instantiate our event handler
    $this->eh = new agEventHandler($eventDebugLevel, agEventHandler::EVENT_NOTICE);

    // grab some global defaults and/or set new vars
    $this->eventId = $eventId;
    $this->skipUnfilled = $skipUnfilled;
    $this->addrGeoTypeId = agGeoType::getAddressGeoTypeId();
    $this->eventStaffDeployedStatusId = agStaffAllocationStatus::getEventStaffDeployedStatusId();
    $this->shiftOffset = agGlobal::getParam('shift_change_restriction');

    // get our global batch size, then modify it to guesstimate on our worst-case wave
    $this->batchSize = agGlobal::getParam('default_batch_size');
    $maxShifts = $this->getMaxShiftsPerWave();
    $this->batchSize = ceil(($this->batchSize / $maxShifts));

    // reset our statistics
    $this->resetStatistics();

    // get our event facility groups loaded
    $this->getEventFacilityGroups();
  }

  /**
   * Method to save and commit the processed transactions and return a final results tally.
   * @return array Returns an array of statistics for display.
   * <code>
   * array( [err] => $errorBool, [msg] => $lastEventMessage, [waves] => $numWavesProcessed,
   *   [shifts] => $numShiftsProcessed, [staff] => $numStaffProcessed, [start] => $phpTimestampStart,
   *   [end] => $phpTimestampEnd, [duration] => $secondsDuration ) 
   * </code>
   */
  public function save()
  {
    $lastEvent = $this->eh->getLastEvent(agEventHandler::EVENT_NOTICE);
    $lastEvent = $lastEvent['msg'];

    if($this->err)
    {
      $lastEvent = 'Deployment operation failed to complete successfully. The last known event ' .
        'message was: ' . "\n" . $lastEvent;

      $waves = 0;
      $shifts = 0;
      $staff = 0;
    }
    else
    {
      // commit our transaction
      $conn = $this->getConnection(self::CONN_WRITE);

      $conn->commit();

      // grab our statistics
      $waves = $this->totalWaves;
      $shifts = $this->totalShifts;
      $staff = $this->totalStaff;
    }

    // pick up our temporal statistics
    $this->endTime = time();
    $duration = $this->endTime - $this->startTime;

    $results = array();
    $results['err'] = $this->err;
    $results['msg'] = $lastEvent;
    $results['waves'] = $waves;
    $results['shifts'] = $shifts;
    $results['staff'] = $staff;
    $results['start'] = $this->startTime;
    $results['end'] = $this->endTime;
    $results['duration'] = $duration;

    return $results;
  }

  /**
   * Method to return agEventHandler
   * @return agEventHandler This classes' instance of agEventHandler
   */
  public function getEventHandler()
  {
    return $this->eh;
  }

  /**
   * Method to set the import connection object property
   */
  protected function setConnections()
  {
    $this->_conn = array();

    $adapter = Doctrine_Manager::connection()->getDbh();
    $this->_conn[self::CONN_READ] = Doctrine_Manager::connection($adapter, self::CONN_READ);

    // establish a write connection with an open transaction
    $write = Doctrine_Manager::connection($adapter, self::CONN_WRITE);
    $write->beginTransaction();
    $this->_conn[self::CONN_WRITE] = $write;
  }

  /**
   * Method to iterate the FacGrp
   */
  protected function iterFacGrp()
  {
    $currFacGrpId = each($this->eventFacilityGroups);

    if ($currFacGrpId === FALSE)
    {
      // if we don't have anymore entries in this array to process
      $this->eh->logNotice('There are no more facility groups left to process.');

      // reset the original iterator
      reset($this->eventFacilityGroups);

      // return the FALSE to our FacGrp iterator
      $this->currFacGrpId = FALSE;
    }
    else
    {
      $this->facGrpPos++;

      // log a nice little message
      $eventMsg = 'Starting deployment processing on facility group ' . $this->facGrpPos .
        ' of ' . $this->facGrpCt . '.';
      $this->eh->logNotice($eventMsg);

      // set our current facGrpId
      $this->currFacGrpId = $currFacGrpId[1];

      // reset counters and bools
      $this->resetFacGrpStatistics();

      // grab a shift / record count
      $ctResults = $this->getFacGrpShiftWaves(TRUE);
      $this->fetchCount = $ctResults->fetchColumn();
      $eventMsg = 'Found ' . $this->fetchCount . ' active staff waves in this facility group.';
      $this->eh->logInfo($eventMsg);

      // now execute the real query and continue
      $pdo = $this->getFacGrpShiftWaves();

      // finally, reset our little iter flag
      $this->iterNextGrp = FALSE;
    }
  }

  /**
   * Method to reset counters, iterators, and statistics for all groups
   */
  protected function resetStatistics()
  {
    $this->facGrpPos = 0;
    $this->facGrpCt = 0;
    $this->totalWaves = 0;
    $this->totalShifts = 0;
    $this->totalStaff = 0;
    reset($this->eventFacilityGroups);
    $this->iterNextGrp = TRUE;

    // also reset batch-level statistics
    $this->resetFacGrpStatistics();
  }

  /**
   * Method to reset Facility-group level statistics
   */
  protected function resetFacGrpStatistics()
  {
    $this->fetchCount = 0;
    $this->fetchPos = 0;
  }

  /**
   * Method to process a batch of records.
   * @return array An array of iterator statistics and the last event message called.
   * <code>
   * array( [continue] => $bool, [event_msg] => $strMessage, [fac_grp_pos] => $int,
   *   [fac_grp_ct] => $int, [fac_grp_left] => $int, [batch_pos] => $int, [batch_ct] => $int,
   *   [batch_left] => $int )
   * </code>
   * @todo Add a second pass for round-robin processing (eg, something to read results[continue]
   * until mins have completed and keep forcing continue until the round robins are done
   */
  public function processBatch()
  {
    return $this->processBatchMins();
  }

  /**
   * Method to process shifts until their minimum complements have been filled
   * @return array An array of iterator statistics and the last event message called.
   * <code>
   * array( [continue] => $bool, [event_msg] => $strMessage, [fac_grp_pos] => $int,
   *   [fac_grp_ct] => $int, [fac_grp_left] => $int, [batch_pos] => $int, [batch_ct] => $int,
   *   [batch_left] => $int )
   * </code>
   */
  protected function processBatchMins()
  {
    // if we've been flagged to do so, start by iterating our next fac group
    if ($this->iterNextGrp)
    {
      $this->iterFacGrp();
    }

    // determine if we have any groups left to process
    if ($this->currFacGrpId === FALSE)
    {
      // @todo execute cleanup actions here
      // @todo commit
      $eventMsg = 'No more facility groups to process. Exiting.';
      return 0;
    }

    // explicit declarations
    $results = array();
    $continue = TRUE;
    $batchLimitReached = FALSE;
    $batchStart = ($this->fetchCount == 0) ? 0 : ($this->fetchPos + 1);
    $batchEnd = ($this->fetchPos + $this->batchSize);
    $batchEnd = ($batchEnd > $this->fetchCount) ? $this->fetchCount : $batchEnd;

    $eventMsg = 'Beginning processing of staff waves ' . $batchStart . ' to ' . $batchEnd . ' of ' .
      $this->fetchCount . '.';
    $this->eh->logInfo($eventMsg);

    // grab our PDO object to continue query fetching
    $pdo = $this->_PDO[self::QUERY_SHIFTS];

    // get our connection and double-check to make sure a transaction was started
    $conn = $this->getConnection(self::CONN_WRITE);
    if($conn->getTransactionLevel() <= 0)
    {
      $conn->beginTransaction();
    }

    // start fetching rows from our batch
    while ($row = $pdo->fetch())
    {
      $this->totalWaves++;
      $this->fetchPos++;

      // not truly necessary now but -very- helpful if query output changes later
      $evFacRscId = $row['a__id'];
      $staffWave = $row['a2__staff_wave'];
      $shiftOrigin = $row['a2__originator_id'];
      $staffRscTypeId = $row['a2__staff_resource_type_id'];
      $minStaff = $row['a2__0'];
      $maxStaff = $row['a2__1'];
      $shiftCount = $row['a2__2'];
      $facLat = $row['a17__latitude'];
      $facLon = $row['a17__longitude'];

      $eventMsg = 'Processing ' . $shiftCount . ' shifts for event facility resource ' .
        $evFacRscId . ' wave ' . $staffWave . ' origin id ' . $shiftOrigin . '.';
      $this->eh->logDebug($eventMsg);

      $shiftStaff = $this->getDeployableEventStaff($staffRscTypeId, $minStaff, $facLat, $facLon);
      $staffCount = count($shiftStaff);

      if ($staffCount >= $minStaff && $staffCount <= $maxStaff)
      {
        $eventMsg = 'Met minimum staff requirements. Adding ' . $staffCount . ' staff to shifts';
        $this->eh->logDebug($eventMsg);

        try
        {
          $this->setShiftStaff($evFacRscId, $staffWave, $shiftOrigin, $shiftStaff);
        }
        catch(Exception $e)
        {
          $eventMsg = 'Encountered an error processing shifts for event facility resource ' .
            $evFacRscId . ' wave ' . $staffWave . ' origin id ' . $shiftOrigin . '.';
          $this->eh->logNotice($eventMsg);

          $eventMsg = $e->getMessage();
          $this->eh->logCrit($eventMsg);
          $this->err = TRUE;
          break;
        }
      }
      else
      {
        $eventMsg = 'Count not meet minimum staff requirements for shift wave ' . $staffWave .
          ' at facility resource id ' . $evFacRscId . '. Skipping waves.';

        if (! $this->skipUnfilled)
        {
          $this->eh->logErr($eventMsg);
          $this->err = TRUE;
          break;
        }

        $this->eh->logWarning($eventMsg);
      }

      // check to see if we've hit a batch marker
      if ($this->fetchPos >= $batchEnd)
      {
        break;
      }
    }

    // if we've encountered an error, no reason to process further
    if ($this->err)
    {
      // write our our current positions and results as well as give the directive not to continue
      $lastEvent = $this->eh->getLastEvent(agEventHandler::EVENT_NOTICE);
      $results['continue'] = FALSE;
      $results['event_msg'] = $lastEvent['msg'];
      $results['fac_grp_pos'] = $this->facGrpPos;
      $results['fac_grp_ct'] = $this->facGrpCt;
      $results['fac_grp_left'] = ($this->facGrpCt - $this->facGrpPos);
      $results['batch_pos'] = $this->fetchPos;
      $results['batch_ct'] = $this->fetchCount;
      $results['batch_left'] = ($this->fetchCount - $this->fetchPos);

      return $results;
    }

    // log our success
    $eventMsg = 'Successfully processed staff waves ' . $batchStart . ' to ' . $this->fetchPos .
        ' of ' . $this->fetchCount . '.';
    $this->eh->logNotice($eventMsg);

    // if we've not reached our batch limit or the limit equals, the end, flag this batch as done
    if ($this->fetchPos == $this->fetchCount)
    {
      $eventMsg = 'Successfully completed processing staff waves for this facility group.';
      $this->eh->logInfo($eventMsg);

      // if this is, ostensibly, the last facGrp we'll process, tell the caller not to continue
      if ($this->facGrpPos == $this->facGrpCt)
      {
        $continue = FALSE;
      }

      // either way, iter the next group (which will auto-reset)
      $this->iterNextGrp = TRUE;
    }

    // cleanup by writing out our results / statistics
    $lastEvent = $this->eh->getLastEvent(agEventHandler::EVENT_NOTICE);
    $results['continue'] = $continue;
    $results['event_msg'] = $lastEvent['msg'];
    $results['fac_grp_pos'] = $this->facGrpPos;
    $results['fac_grp_ct'] = $this->facGrpCt;
    $results['fac_grp_left'] = ($this->facGrpCt - $this->facGrpPos);
    $results['batch_pos'] = $this->fetchPos;
    $results['batch_ct'] = $this->fetchCount;
    $results['batch_left'] = ($this->fetchCount - $this->fetchPos);

    return $results;
  }

  /**
   * Method to return the shifts associated with a given wave, facRsc, and origin combination.
   * @param integer $eventFacilityResourceId An integer representing a facility resource id
   * @param integer $staffWave An integer representing the staff wave
   * @param integer $shiftOrigin An integer for the shift origin
   * @return array An array of shift ids.
   */
  protected function getWaveShifts( $eventFacilityResourceId,
                                    $staffWave,
                                    $shiftOrigin)
  {
    $q = agDoctrineQuery::create()
      ->select('es.id')
        ->from('agEventShift es')
          ->innerJoin('es.agShiftStatus ss')
        ->where('es.event_facility_resource_id = ?', $eventFacilityResourceId)
          ->andWhere('es.staff_wave = ?', $staffWave)
          ->andWhere('es.originator_id = ?', $shiftOrigin)
          ->andWhere('ss.disabled = ?', FALSE);

    $allocatableShifts = '(60 * (es.minutes_start_to_facility_activation - ' . $this->shiftOffset .
      ')) <= CURRENT_TIMESTAMP';
    $q->andWhere($allocatableShifts);

    return $q->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);
  }

  /**
   * Method to assign event staff to event shifts.
   * @param integer $eventFacilityResourceId The event facility resource that sources the shifts
   * @param integer $staffWave The staff wave to process
   * @param integer $shiftOrigin The shift originator (template)
   * @param array $eventStaffIds An array of event staff ids.
   * @return boolean Whether or not this process was successful
   */
  public function setShiftStaff( $eventFacilityResourceId,
                                 $staffWave,
                                 $shiftOrigin,
                                 array $eventStaffIds)
  {
    $success = TRUE;
    $conn = $this->getConnection(self::CONN_WRITE);

    $shifts = $this->getWaveShifts($eventFacilityResourceId, $staffWave, $shiftOrigin);
    $shiftCount = count($shifts);
    $eventMsg = 'Found ' . $shiftCount . ' shifts for event facility resource ' .
      $eventFacilityResourceId . ' staff wave ' . $staffWave . ' originator id ' .
      $shiftOrigin;
    $this->eh->logInfo($eventMsg);

    // build a collection to store our records and loop through adding records to it
    $coll = new Doctrine_Collection('agEventStaffShift');
    foreach ($shifts as $shift)
    {
      foreach ($eventStaffIds as $staff)
      {
        $this->eh->logDebug('Adding event staff id ' . $staff . ' to shift id ' . $shift . '.');

        // create a new shift record and add it to our collection
       $rec = new agEventStaffShift();
        $rec['event_shift_id'] = $shift;
        $rec['event_staff_id'] = $staff;
        $coll->add($rec);
      }

      // release a little memory
      unset($shifts[$shift]);
    }

    $conn = $this->getConnection(self::CONN_WRITE);
    try
    {
      $coll->save($conn);
    }
    catch(Exception $e)
    {
      // log our error
      $eventMsg = 'An error occurred attempting to add staff to shifts shifts for event facility ' .
      'resource ' . $eventFacilityResourceId . ' staff wave ' . $staffWave . ' originator id ' .
      $shiftOrigin;
      $this->eh->logErr($eventMsg, count($coll));
      $success = FALSE;

      $this->rollbackSafe($conn);

      // rethrow
      throw($e);
    }

    if ($success)
    {
      try
      {
        $this->disableEventStaff($eventStaffIds);
      }
      catch(Exception $e)
      {
        $success = FALSE;
        throw($e);
      }
    }

    $this->totalShifts = $this->totalShifts + $shiftCount;
    $this->totalStaff = $this->totalStaff + count($eventStaffIds);
    return $success;
  }

  /**
   * Method to update event staff status records to reflect committment to a shift.
   * @param array $eventStaffIds A single-dimension of event staff ids.
   */
  protected function disableEventStaff(array $eventStaffIds)
  {
    $conn = $this->getConnection(self::CONN_WRITE);

    $q = agDoctrineQuery::create()
      ->select('es1.id')
        ->from('agEventStaff AS es1')
          ->innerJoin('es1.agStaffResource AS sr1')
          ->innerJoin('sr1.agStaff AS s1')
          ->innerJoin('s1.agStaffResource AS sr2')
          ->innerJoin('sr2.agEventStaff AS es2')
        ->where('es1.event_id = es2.event_id')
          ->andWhereIn('es2.id', $eventStaffIds);

    $eventStaffIds = $q->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);
    $q->free();

    $coll = new Doctrine_Collection('agEventStaffStatus');

    foreach ($eventStaffIds as $eventStaffId)
    {
      $eventMsg = 'Marking eventStaffId ' . $eventStaffId . ' to have its status changed to ' .
        ' staffAllocationStatusId ' . $this->eventStaffDeployedStatusId;
      $this->eh->logDebug($eventMsg);

      $rec = new agEventStaffStatus();
      $rec['event_staff_id'] = $eventStaffId;
      $rec['staff_allocation_status_id'] = $this->eventStaffDeployedStatusId;
      $rec['time_stamp'] = date('Y-m-d H:i:s', time());
      $coll->add($rec);
    }

    $collSize = count($coll);
    $eventMsg = 'Found ' . $collSize . ' event staff records in-need of status updates.';
    $this->eh->logInfo($eventMsg);

    try
    {
      $coll->save($conn);
    }
    catch(Exception $e)
    {
      $eventMsg = 'Encountered an error while setting event staff status. Rolling back changes.';
      $this->eh->logCrit($eventMsg, $collSize);

      $this->rollbackSafe($conn);

      throw($e);
    }

    $this->eh->logInfo('Successfully updated ' . $collSize . ' staff status records.');
  }

  /**
   * Method to return an array of deployable event staff id's sorted by deployment weight and
   * distance to the querying facility.
   * @param integer $staffResourceTypeId The staff resource type id to return
   * @param integer $staffCount The number of staff to return
   * @param decimal $facLat The facility latitude against which the staff should be compared
   * @param decimal $facLon The facility longitude against which the staff should be compared
   * @return array A single-dimension array of event staff ids.
   */
  protected function getDeployableEventStaff($staffResourceTypeId,
                                             $staffCount,
                                             $facLat,
                                             $facLon)
  {
    // start with our basic query object
    $q = agDoctrineQuery::create()
      ->select('evs.id')
        ->from('agEventStaff evs')
          ->innerJoin('evs.agEventStaffStatus ess')
          ->innerJoin('ess.agStaffAllocationStatus sas')
          ->innerJoin('evs.agStaffResource sr')
          ->innerJoin('sr.agStaffResourceStatus srs')
          ->innerJoin('sr.agStaff s')
          ->innerJoin('s.agPerson p')
          ->innerJoin('p.agEntity e')
          ->innerJoin('e.agEntityAddressContact eac')
          ->innerJoin('eac.agAddress a')
          ->innerJoin('a.agAddressGeo ag')
          ->innerJoin('ag.agGeo g')
          ->innerJoin('g.agGeoFeature gf')
          ->innerJoin('gf.agGeoCoordinate gc')
        ->where('evs.event_id = ?', $this->eventId)
          ->andWhere('sr.staff_resource_type_id = ?', $staffResourceTypeId)
          ->andWhere('sas.allocatable = ?', TRUE)
          ->andWhere('srs.is_available = ?', TRUE)
          ->andWhere('g.geo_type_id = ?', $this->addrGeoTypeId)
        ->orderBy('evs.deployment_weight DESC')
        ->limit($staffCount);

    // ensure that we only get the most recent staff status
    $recentStaffStatus = 'EXISTS (' .
      'SELECT sess.id ' .
        'FROM agEventStaffStatus AS sess ' .
        'WHERE sess.time_stamp <= CURRENT_TIMESTAMP ' .
          'AND sess.event_staff_id = ess.event_staff_id ' .
        'HAVING MAX(sess.time_stamp) = ess.time_stamp' .
      ')';
    $q->andWhere($recentStaffStatus);

    // just pick up the lowest priority staff address
    $minStaffAddr = 'EXISTS (' .
      'SELECT seac.id ' .
        'FROM agEntityAddressContact AS seac ' .
        'WHERE seac.id = eac.id ' .
        'HAVING MIN(seac.priority) = eac.priority' .
      ')';
    $q->andWhere($minStaffAddr);

    // add our geo-distance calculation
    $geoDistance = '(acos( sin( radians(gc.latitude) ) * sin( radians(' . $facLat . ') ) ' .
      '+ cos( radians(gc.latitude) ) * cos( radians(' . $facLat . ') ) ' .
      '* cos( radians(' . $facLon . ') - radians(gc.longitude) ) ) * 6378)' ;
    $q->orderBy($geoDistance . ' ASC');

    return $q->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);
  }

  /**
   * Method to query the database for event facility groups and set the class property.
   */
  protected function getEventFacilityGroups()
  {
    $q = agDoctrineQuery::create()
      ->select('efg.id')
        ->from('agEventFacilityGroup efg')
          ->innerJoin('efg.agEventFacilityGroupStatus efgs')
          ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
        ->where('efg.event_id = ?', $this->eventId)
          ->andWhere('fgas.active = ?', TRUE)
        ->orderBy('efg.activation_sequence ASC');

    // ensure that we only get the most recent group status
    $recentGroupStatus = 'EXISTS (' .
      'SELECT sefgs.id ' .
        'FROM agEventFacilityGroupStatus AS sefgs ' .
        'WHERE sefgs.time_stamp <= CURRENT_TIMESTAMP ' .
          'AND sefgs.event_facility_group_id = efgs.event_facility_group_id ' .
        'HAVING MAX(sefgs.time_stamp) = efgs.time_stamp' .
      ')';
    $q->andWhere($recentGroupStatus);

    $this->eventFacilityGroups = $q->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);
    $this->facGrpCt = count($this->eventFacilityGroups);
    $this->eh->logInfo('Found ' . $this->facGrpCt . ' active event facility groups.');
  }

  protected function getMaxShiftsPerWave()
  {
    $results = 1;

    // build our basic query
    $q = agDoctrineQuery::create()
      ->select('COUNT(es.id) AS shift_count')
        ->from('agEventFacilityResource efr')
          ->innerJoin('efr.agEventShift es')
          ->innerJoin('es.agShiftStatus ss')
          ->innerJoin('efr.agEventFacilityResourceActivationTime')
          ->innerJoin('efr.agEventFacilityResourceStatus efrs')
          ->innerJoin('efrs.agFacilityResourceAllocationStatus fras')
          ->innerJoin('efr.agFacilityResource fr')
          ->innerJoin('fr.agFacilityResourceStatus frs')
          ->innerJoin('efr.agEventFacilityGroup efg')
          ->innerJoin('efg.agEventFacilityGroupStatus efgs')
          ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
        ->where('fras.staffed = ?', TRUE)
          ->andWhere('frs.is_available = ?', TRUE)
          ->andWhere('ss.disabled = ?', FALSE)
          ->andWhere('efg.event_id = ?', $this->eventId)
          ->andWhere('fgas.active = ?', TRUE)
        ->groupBy('efr.id')
          ->addGroupBy('es.staff_wave')
          ->addGroupBy('es.originator_id')
        ->orderBy('COUNT(es.id) DESC')
        ->limit(1);

    // restrict ourselves only to allocatable shifts
    $allocatableShifts = '(60 * (es.minutes_start_to_facility_activation - ' . $this->shiftOffset .
      ')) <= CURRENT_TIMESTAMP';
    $q->andWhere($allocatableShifts);

    // ensure that we only get the most recent facility resource status
    $recentFacRscStatus = 'EXISTS (' .
      'SELECT sefrs.id ' .
        'FROM agEventFacilityResourceStatus AS sefrs ' .
        'WHERE sefrs.time_stamp <= CURRENT_TIMESTAMP ' .
          'AND sefrs.event_facility_resource_id = efrs.event_facility_resource_id ' .
        'HAVING MAX(sefrs.time_stamp) = efrs.time_stamp' .
      ')';
    $q->andWhere($recentFacRscStatus);

    // ensure that we only get the most recent group status
    $recentGroupStatus = 'EXISTS (' .
      'SELECT sefgs.id ' .
        'FROM agEventFacilityGroupStatus AS sefgs ' .
        'WHERE sefgs.time_stamp <= CURRENT_TIMESTAMP ' .
          'AND sefgs.event_facility_group_id = efgs.event_facility_group_id ' .
        'HAVING MAX(sefgs.time_stamp) = efgs.time_stamp' .
      ')';
    $q->andWhere($recentGroupStatus);

    $qresults = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    if (!empty($qresults))
    {
      $results = $qresults;
    }

    return $results;
  }

  /**
   * Method to return the number of shift waves in a facility group.
   * @param boolean $asCount Whether or not to return the results as a count.
   * @return PdoStatement Returns a post-execution PDO statement.
   */
  protected function getFacGrpShiftWaves( $asCount = FALSE )
  {
    // build our basic query
    $q = agDoctrineQuery::create()
      ->select('efr.id')
          ->addSelect('es.staff_wave')
          ->addSelect('es.staff_resource_type_id')
          ->addSelect('es.originator_id')
          ->addSelect('MIN(es.minimum_staff) AS minimum_staff')
          ->addSelect('MAX(es.maximum_staff) AS maximum_staff')
          ->addSelect('COUNT(es.id) AS shift_count')
          ->addSelect('gc.latitude')
          ->addSelect('gc.longitude')
          ->addSelect('fr.id')
          ->addSelect('f.id')
          ->addSelect('s.id')
          ->addSelect('e.id')
          ->addSelect('eac.id')
          ->addSelect('a.id')
          ->addSelect('ag.id')
          ->addSelect('g.id')
          ->addSelect('gf.id')
        ->from('agEventFacilityResource efr')
          ->innerJoin('efr.agEventShift es')
          ->innerJoin('es.agShiftStatus ss')
          ->innerJoin('efr.agEventFacilityResourceActivationTime')
          ->innerJoin('efr.agEventFacilityResourceStatus efrs')
          ->innerJoin('efrs.agFacilityResourceAllocationStatus fras')
          ->innerJoin('efr.agFacilityResource fr')
          ->innerJoin('fr.agFacilityResourceStatus frs')
          ->innerJoin('fr.agFacility f')
          ->innerJoin('f.agSite s')
          ->innerJoin('s.agEntity e')
          ->innerJoin('e.agEntityAddressContact eac')
          ->innerJoin('eac.agAddress a')
          ->innerJoin('a.agAddressGeo ag')
          ->innerJoin('ag.agGeo g')
          ->innerJoin('g.agGeoFeature gf')
          ->innerJoin('gf.agGeoCoordinate gc')
        ->where('fras.staffed = ?', TRUE)
          ->andWhere('frs.is_available = ?', TRUE)
          ->andWhere('ss.disabled = ?', FALSE)
          ->andWhere('efr.event_facility_group_id = ?', $this->currFacGrpId)
          ->andWhere('g.geo_type_id = ?', $this->addrGeoTypeId)
        ->groupBy('efr.id')
          ->addGroupBy('es.staff_wave')
          ->addGroupBy('es.staff_resource_type_id')
          ->addGroupBy('es.originator_id')
          ->addGroupBy('gc.latitude')
          ->addGroupBy('gc.longitude')
          ->addGroupBy('efr.id')
          ->addGroupBy('fr.id')
          ->addGroupBy('f.id')
          ->addGroupBy('s.id')
          ->addGroupBy('e.id')
          ->addGroupBy('eac.id')
          ->addGroupBy('a.id')
          ->addGroupBy('ag.id')
          ->addGroupBy('g.id')
          ->addGroupBy('gf.id')
        ->orderBy('es.staff_wave ASC')
          ->addOrderBy('efr.activation_sequence ASC')
          ->addOrderBy('efr.id ASC');
 
    // restrict ourselves only to allocatable shifts
    $allocatableShifts = '(60 * (es.minutes_start_to_facility_activation - ' . $this->shiftOffset .
      ')) <= CURRENT_TIMESTAMP';
    $q->andWhere($allocatableShifts);


    // ensure that we only get the most recent facility resource status
    $recentFacRscStatus = 'EXISTS (' .
      'SELECT sefrs.id ' .
        'FROM agEventFacilityResourceStatus AS sefrs ' .
        'WHERE sefrs.time_stamp <= CURRENT_TIMESTAMP ' .
          'AND sefrs.event_facility_resource_id = efrs.event_facility_resource_id ' .
        'HAVING MAX(sefrs.time_stamp) = efrs.time_stamp' .
      ')';
    $q->andWhere($recentFacRscStatus);

    // just pick up the lowest priority facility address
    $minFacAddr = 'EXISTS (' .
      'SELECT seac.id ' .
        'FROM agEntityAddressContact AS seac ' .
        'WHERE seac.id = eac.id ' .
        'HAVING MIN(seac.priority) = eac.priority' .
      ')';
    $q->andWhere($minFacAddr);

    // grab a connection
    $conn = $this->getConnection(self::CONN_READ);

    // grab the query components
    $sql = $q->getSqlQuery();

    // if this is a count query, wrap it like one
    if ($asCount)
    {
      $sql = 'SELECT COUNT(*) FROM (' . $sql . ') AS t;';
    }

    // execute the pdo query
    $params = $q->getParams();
    $pdoStmt = $this->executePdoQuery($conn, $sql, $params['where'], NULL, self::QUERY_SHIFTS);
    
    return $pdoStmt;
  }

  /**
   * A simple method used to test execution of this class
   * @return <type>
   * @deprecated A test-method only
   */
  public function test()
  {
    $this->batchSize = 10;
    $this->eh->setLogEventLevel(agEventHandler::EVENT_DEBUG);

    $continue = TRUE;
    while ($continue == TRUE)
    {
      $batch = $this->processBatch();
      $continue = $batch['continue'];

      print_r($batch);
      echo "<br/><br/>";
    }

    $results = $this->save();
    print_r($results);
    return "<br/><br/>" . "Success!";
  }
}
