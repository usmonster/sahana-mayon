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
            $shiftOffset;

  protected $batchSize,
            $fetchCount,
            $fetchPos,
            $totalFetches,
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
   */
  protected function __init($eventId, $eventDebugLevel = NULL)
  {
    // instantiate our event handler
    $this->eh = new agEventHandler($eventDebugLevel, agEventHandler::EVENT_NOTICE);

    // set our connections
    $this->setConnections();

    // grab some global defaults and/or set new vars
    $this->eventId = $eventId;
    $this->addrGeoTypeId = agGeoType::getAddressGeoTypeId();
    $this->eventStaffDeployedStatusId = agStaffAllocationStatus::getEventStaffDeployedStatusId();
    $this->shiftOffset = agGlobal::getParam('shift_change_restriction');

    // reset our statistics
    $this->resetStatistics();

    // get our event facility groups loaded
    $this->getEventFacilityGroups();
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
    $this->_conn[self::CONN_WRITE] = Doctrine_Manager::connection($adapter, self::CONN_WRITE);
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
      $ctResults = $this->getFacGrpShifts(TRUE);
      $this->fetchCount = $ctResults->fetchColumn();
      $eventMsg = 'Found ' . $this->fetchCount . ' active shifts in this facility group.';
      $this->eh->logInfo($eventMsg);

      // now execute the real query and continue
      $pdo = $this->getFacGrpShifts();

      // finally, reset our little iter flag
      $this->iterNextGrp = FALSE;
    }
  }

  /**
   * Method to reset counters, iterators, and statistics for all groups
   */
  public function resetStatistics()
  {
    // set process-wide statistics
    $this->batchSize = agGlobal::getParam('default_batch_size');
    $this->facGrpPos = 0;
    $this->facGrpCt = 0;
    $this->totalFetches = 0;
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

    $eventMsg = 'Beginning processing of shifts ' . $batchStart . ' to ' . $batchEnd . ' of ' .
      $this->fetchCount . '.';
    $this->eh->logInfo($eventMsg);

    // grab our PDO object to continue query fetching
    $pdo = $this->_PDO[self::QUERY_SHIFTS];
    
    // start fetching rows from our batch
    while ($row = $pdo->fetch())
    {
      $this->fetchPos++;

      print_r('Shift: ' . $row['a__id'] . '<br/>');

      // check to see if we've hit a batch marker
      if ($this->fetchPos >= $batchEnd)
      {
        break;
      }
    }

    // log our success
    $eventMsg = 'Successfully processed shifts ' . $batchStart . ' to ' . $this->fetchPos . ' of ' .
      $this->fetchCount . '.';
    $this->eh->logNotice($eventMsg);

    // if we've not reached our batch limit or the limit equals, the end, flag this batch as done
    if ($this->fetchPos == $this->fetchCount)
    {
      $eventMsg = 'Successfully completed processing shifts for this facility group.';
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

  protected function getFacGrpShifts( $asCount = FALSE )
  {
    // build our basic query
    $q = agDoctrineQuery::create()
      ->select('es.id')
          ->addSelect('es.event_facility_resource_id')
          ->addSelect('es.staff_wave')
          ->addSelect('es.minimum_staff')
          ->addSelect('es.maximum_staff')
          ->addSelect('gc.latitude')
          ->addSelect('gc.longitude')
          ->addSelect('efr.id')
          ->addSelect('fr.id')
          ->addSelect('f.id')
          ->addSelect('s.id')
          ->addSelect('e.id')
          ->addSelect('eac.id')
          ->addSelect('a.id')
          ->addSelect('ag.id')
          ->addSelect('g.id')
          ->addSelect('gf.id')
        ->from('agEventShift es')
          ->innerJoin('es.agShiftStatus ss')
          ->innerJoin('es.agEventFacilityResource efr')
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
        ->orderBy('es.staff_wave ASC')
          ->addOrderBy('efr.activation_sequence ASC');
 
    // restrict ourselves only to allocatable shifts
    $allocatableShifts = '(es.minutes_start_to_facility_activation - ' . $this->shiftOffset .
      ') <= CURRENT_TIMESTAMP';
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

  public function deployEventStaff()
  {

  }

  public function updateEventStaffStatus()
  {

  }

  public function test()
  {
    $this->batchSize = 10;

    $continue = TRUE;
    while ($continue == TRUE)
    {
      $batch = $this->processBatch();
      $continue = $batch['continue'];

      print_r($batch);
      echo "<br/><br/>";
    }

    return "<br/><br/>" . "Success!";
  }
}
