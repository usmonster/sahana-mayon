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
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEventMigrationHelper
{
  const     CONN_MIGRATION_WRITE = 'CONN_MIGRATION_WRITE';
  protected $evFacGrps = 0,
            $evFacRscs = 0,
            $evShifts = 0,
            $evStfRscs = 0,
            $err = FALSE,
            $conn,
            $scenarioId,
            $eventId,
            $zeroHour,
            $recTimestamp,
            $startTime,
            $endTime,
            $batchSize,
            $batchTime;

  public function __construct() {
    $this->batchSize = agGlobal::getParam('default_batch_size');
    $this->batchTime = agGlobal::getParam('bulk_operation_max_batch_time');

    // all of this jazz helps us keep a low memory profile
    $dm = Doctrine_Manager::getInstance();
    $dm->setCurrentConnection('doctrine');
    $adapter = $dm->getCurrentConnection()->getDbh();
    $this->conn = Doctrine_Manager::connection($adapter, self::CONN_MIGRATION_WRITE);

    // this will disable re-use of query objects but keeps the connection memory profile low
    $this->conn->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, TRUE);
    $this->conn->setAttribute(Doctrine_Core::ATTR_AUTOCOMMIT, FALSE);
    $this->conn->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, FALSE);
    $this->conn->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, FALSE);
    $this->conn->setAttribute(Doctrine_Core::ATTR_VALIDATE, FALSE);
    $this->conn->setAttribute(Doctrine_Core::ATTR_CASCADE_SAVES, FALSE);

    // return back to our 'normal' doctrine connection before moving further
    $dm->setCurrentConnection('doctrine');
  }

  public function __destruct() {
    // all of this jazz helps us keep a low memory profile
    $dm = Doctrine_Manager::getInstance();
    $dm->setCurrentConnection('doctrine');

    $dm->closeConnection($this->conn);
  }


  /**
   * Provides check information (count of empty facility groups) for this scenario
   * @param integer $scenario_id the scenario to pre-check for migration
   * @return integer count of empty facility groups
   */
  public static function facilityGroupCheck($scenario_id)
  {
    $facilityGroupQuery = agDoctrineQuery::create()
        ->select('aFG.id')
        ->addSelect('aFG.scenario_facility_group')
        ->from('agScenarioFacilityGroup aFG')
        ->leftJoin('aFG.agScenarioFacilityResource aFR')
        ->where('aFR.id is NULL')
        ->andWhere('aFG.scenario_id = ?', $scenario_id);
    $returnvalue = $facilityGroupQuery->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
    $facilityGroupQuery->free();
    return $returnvalue;
  }

  /**
   * Provides check information (count of undefined facility and staff shifts) for this scenario
   * @param integer $scenario_id the scenario to pre-check for migration
   * @return array : count of undefined facility shifts, count of undefined staff shifts
   */
  public static function undefinedShiftCheck($scenario_id)
  {
    $undefinedFacilityShiftQuery = agDoctrineQuery::create()
        ->select('aFR.id')
        ->from('agScenarioFacilityResource aFR')
        ->innerJoin('aFR.agScenarioFacilityGroup aFG')
        ->leftJoin('aFR.agScenarioShift aSS')
        ->where('aSS.id is NULL')
        ->andWhere('aFG.scenario_id =?', $scenario_id); //returns the facility resources without shift
    $facilityShiftReturn = $undefinedFacilityShiftQuery->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);
    $undefinedFacilityShiftQuery->free();

    return $facilityShiftReturn;
  }

  /**
   * Provides check information (count of staff resources) available for this scenario
   * @param integer $scenario_id the scenario to pre-check for migration
   * @return integer count of staff resources
   */
  public static function staffPoolCheck($scenario_id)
  {
    $staffPoolCount = agDoctrineQuery::create()
        ->select('COUNT(*)')
        ->from('agScenarioStaffResource')
        ->where('scenario_id =?', $scenario_id)
        ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    return $staffPoolCount;
  }

  /**
   * Performs checks to see if a scenario is ready for deployment
   * @param integer $scenario_id the scenario to pre-check for migration
   * @return array of Empty Facility Groups, the count of Staff Pool, Undefined Facility Shifts
   *                  Undefined Staff Shifts
   */
  public static function preMigrationCheck($scenario_id)
  {
    // 0. Pre check: check event status (only proceed if event status is pre-deploy), clean event related tables in pre-deploy state, empty facility group, undefined staff pool rules making sure pools not empty, undefined shifts for staff/facility resource.
    $facilityGroupCheck = self::facilityGroupCheck($scenario_id);
    $staffPoolCheck = self::staffPoolCheck($scenario_id);
    $undefinedFacilityShifts = self::undefinedShiftCheck($scenario_id);

    return array('Empty facility groups' => $facilityGroupCheck, 'Staff pool count' => $staffPoolCheck, 'Undefined facility shifts' => $undefinedFacilityShifts);
  }

  /**
   * Method to migrate a scenario data to an event
   * @param integer $scenarioId A scenarioId
   * @param integer $eventId An eventId
   * @return array An array of results
   */
  public function migrateScenarioToEvent($scenarioId, $eventId) {
    // re-set this at the start
    $this->err = FALSE;

    // first set some static declarations
    $this->startTime = microtime(TRUE);
    $this->recTimestamp = date('Y-m-d H:i:s', time());

    // set our mirgration scenario / event
    $this->scenarioId = $scenarioId;
    $this->eventId = $eventId;

    // pick up the event zero hour now
    $this->zeroHour = agDoctrineQuery::create()
      ->select('ae.zero_hour')
        ->from('agEvent ae')
        ->where('ae.id = ?', $this->eventId)
        ->useResultCache(TRUE, 1800)
        ->execute(array(),Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    $staffedAllocationStatus = agDoctrineQuery::create()
      ->select('afras.id')
        ->from('agFacilityResourceAllocationStatus afras')
        ->where('afras.staffed = ?', TRUE)
        ->useResultCache(TRUE, 3600)
        ->execute(array(),agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    // yay, transactions = data integrity
    $this->conn->beginTransaction();

    // start by re-generating our staff pool
    set_time_limit($this->batchTime);
    try {
      agStaffGeneratorHelper::generateStaffPool($this->scenarioId, FALSE, $this->conn);
    } catch(Exception $e) {
      $this->err = TRUE;
    }
    $this->conn->evictTables();
    $this->conn->clear();
    
    // since we have staff on our mind, we might as well use them
    if (!$this->err) {
      set_time_limit($this->batchTime);
      try {
        $this->migrateStaff();
      } catch(Exception $e) {
        $this->err = TRUE;
      }
      $this->conn->evictTables();
      $this->conn->clear();
    }

    // next we create our event facility groups
    if (!$this->err) {
      set_time_limit($this->batchTime);
      try {
        $evFacGrpStatusColl = $this->migrateFacilityGroups();
      } catch(Exception $e) {
        $this->err = TRUE;
      }
    }

    // then we create our facility resources
    if (!$this->err) {
      set_time_limit($this->batchTime);
      try {
        $scFacRscMap = $this->migrateFacilityResources($evFacGrpStatusColl);
      } catch(Exception $e) {
        $this->err = TRUE;
      }
      unset($evFacGrpStatusColl);
    }

    // finally we create our shifts
    if (!$this->err) {
      set_time_limit($this->batchTime);
      try {
        $this->migrateShifts($scFacRscMap);
      } catch(Exception $e) {
        $this->err = TRUE;
      }
      unset($scFacRscMap);
    }

    // commit or rollback
    if (!$this->err) {
      try {
        $this->conn->commit();
      } catch (Exception $e) {
        $err = TRUE;
      }
    }

    if ($this->err) {
      $this->conn->rollBack();
      throw $e;
    }

    $this->endTime = microtime(TRUE);
    $duration = $this->endTime - $this->startTime;

    $results = array();
    $results['Facility Groups'] = $this->evFacGrps;
    $results['Facility Resources'] = $this->evFacRscs;
    $results['Shifts'] = $this->evShifts;
    $results['Staffs'] = $this->evStfRscs;

    return $results;
  }

  /**
   * Method to migrate scenario staff to event staff
   */
  protected function migrateStaff() {
    // this will be default staff status applied to all new event staff
    $unAllocatedStaffStatus = agEventStaffHelper::returnDefaultEventStaffStatus();

    // pretty standard object instantiation
    $eventStaffTable = $this->conn->getTable('agEventStaffBulkLoad');
    $eventStaffStatusTable = $this->conn->getTable('agEventStaffStatus');
    $evStaffColl = new Doctrine_Collection('agEventStaffBulkLoad');
    $evStaffStatusColl = new Doctrine_Collection('agEventStaffStatus');

    // get our scenario staff
    $existingScenarioStaff = agDoctrineQuery::create()
      ->select('ssr.*')
        ->from('agScenarioStaffResource ssr')
        ->where('scenario_id = ?', $this->scenarioId)
        ->execute(array(), Doctrine_Core::HYDRATE_ON_DEMAND);

    $this->evStfRscs = 0;
    foreach ($existingScenarioStaff AS $scenStfPool) {
      // start by iterating our iterator
      $this->evStfRscs++;

      // create an agEventStaff record
      $eventStaff = new agEventStaffBulkLoad($eventStaffTable, TRUE);
      $eventStaff['event_id'] = $this->eventId;
      $eventStaff['staff_resource_id'] = $scenStfPool['staff_resource_id'];
      $eventStaff['deployment_weight'] = $scenStfPool['deployment_weight'];
      $evStaffColl->add($eventStaff);

      if (($this->evStfRscs % $this->batchSize) == 0) {
        $evStaffColl->save($this->conn);

        // create the staff status records in a batch
        foreach ($evStaffColl as $evStaff) {
          $eventStaffStatus = new agEventStaffStatus($eventStaffStatusTable, TRUE);
          $eventStaffStatus['event_staff_id'] = $evStaff['id'];
          $eventStaffStatus['time_stamp'] = $this->recTimestamp;
          $eventStaffStatus['staff_allocation_status_id'] = $unAllocatedStaffStatus;
          $evStaffStatusColl->add($eventStaffStatus);
        }

        $evStaffStatusColl->save($this->conn);
        $evStaffColl->free(TRUE);
        $evStaffStatusColl->free(TRUE);
        $evStaffColl = new Doctrine_Collection('agEventStaffBulkLoad');
        $evStaffStatusColl = new Doctrine_Collection('agEventStaffStatus');

        set_time_limit($this->batchTime);
      }
    }

    // now we sweep all the leftovers that weren't caught in a batch process
    if ($evStaffColl->isModified()) {
      $evStaffColl->save($this->conn);

      // create the staff status record
      foreach ($evStaffColl as $evStaff) {
        $eventStaffStatus = new agEventStaffStatus($eventStaffStatusTable, TRUE);
        $eventStaffStatus['event_staff_id'] = $evStaff['id'];
        $eventStaffStatus['time_stamp'] = $this->recTimestamp;
        $eventStaffStatus['staff_allocation_status_id'] = $unAllocatedStaffStatus;
        $evStaffStatusColl->add($eventStaffStatus);
      }

      $evStaffStatusColl->save($this->conn);
      $evStaffColl->free(TRUE);
      $evStaffStatusColl->free(TRUE);
    }
  }
/**
 * Method to migrate facility groups to an event
 * @return Doctrine_Collection A doctrine collection of agFacilityGroupStatus records
 */
  protected function migrateFacilityGroups() {
    // pretty standard collections pre-config
    $evFacGrpTable = $this->conn->getTable('agEventFacilityGroup');
    $evFacGrpStatusTable = $this->conn->getTable('agEventFacilityGroupStatus');
    $evFacGrpColl = new Doctrine_Collection('agEventFacilityGroup');
    $evFacGrpStatusColl = new Doctrine_Collection('agEventFacilityGroupStatus');

    $scenarioFacilityGroups = agDoctrineQuery::create()
      ->select('sfg.id')
        ->addSelect('sfg.scenario_facility_group')
        ->addSelect('sfg.facility_group_type_id')
        ->addSelect('sfg.facility_group_allocation_status_id')
        ->addSelect('sfg.activation_sequence')
        ->from('agScenarioFacilityGroup sfg INDEXBY sfg.id')
        ->where('sfg.scenario_id = ?', $this->scenarioId)
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    // always set this back to zero in case it's being called again
    $this->evFacGrps = 0;
    foreach ($scenarioFacilityGroups as $scenFacGrpId => $scenFacGrp) {
      $evFacGrp = new agEventFacilityGroup($evFacGrpTable, TRUE);
      $evFacGrp['event_id'] = $this->eventId;
      $evFacGrp['event_facility_group'] = $scenFacGrp['scenario_facility_group'];
      $evFacGrp['facility_group_type_id'] = $scenFacGrp['facility_group_type_id'];
      $evFacGrp['activation_sequence'] = $scenFacGrp['activation_sequence'];

      $evFacGrpColl->add($evFacGrp, $scenFacGrpId);
    }

    $evFacGrpColl->save($this->conn);

    foreach ($evFacGrpColl as $scenFacGrpId => $evFacGrp) {
      $evFacGrpStatus = new agEventFacilityGroupStatus($evFacGrpStatusTable, TRUE);
      $evFacGrpStatus['event_facility_group_id'] = $evFacGrp['id'];
      $evFacGrpStatus['time_stamp'] = $this->recTimestamp;
      $evFacGrpStatus['facility_group_allocation_status_id'] = $scenarioFacilityGroups[$scenFacGrpId]['facility_group_allocation_status_id'];
      $evFacGrpStatusColl->add($evFacGrpStatus, $scenFacGrpId);
    }

    $evFacGrpStatusColl->save($this->conn);
    $evFacGrpColl->free(TRUE);
    unset($evFacGrpColl);

    return $evFacGrpStatusColl;
  }

  /**
   * Method to migrate facility resources
   * @param Doctrine_Collection $evFacGrpStatusColl A collection of event facility group status records
   * @return stdClass A standard object used for storage of scenario => event facility resource ids
   */
  protected function migrateFacilityResources(Doctrine_Collection $evFacGrpStatusColl) {
    // use some explicit declarations at the top
    $scFacRscMap = (object) array();
    $activeGrpScFacRsc = array();
    $evFacRscTable = $this->conn->getTable('agEventFacilityResource');
    $evFacRscStatusTable = $this->conn->getTable('agEventFacilityResourceStatusBulkLoad');
    $evFacRscActivationTable = $this->conn->getTable('agEventFacilityResourceActivationTime');
    $evFacRscColl = new Doctrine_Collection('agEventFacilityResource');
    $evFacRscStatusColl = new Doctrine_Collection('agEventFacilityResourceStatusBulkLoad');
    $evFacRscActivationColl = new Doctrine_Collection('agEventFacilityResourceActivationTime');

    // get our allocation status
    $staffedResourceAllocationStatus = agDoctrineQuery::create()
      ->select('afras.id')
        ->addSelect('afras.staffed')
      ->from('agFacilityResourceAllocationStatus afras')
      ->where('afras.staffed = ?', TRUE)
      ->useResultCache(TRUE, 3600)
      ->execute(array(),agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    $activeGroupAllocationStatus = agDoctrineQuery::create()
      ->select('afgas.id')
        ->addSelect('afgas.active')
        ->from('agFacilityGroupAllocationStatus afgas')
        ->where('active = ?', TRUE)
        ->useResultCache(TRUE,3600)
        ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    // instantiate our fac resource iterator
    $scenarioFacilityResources = agDoctrineQuery::create()
      ->select('sfr.*')
        ->from('agScenarioFacilityResource sfr INDEXBY sfr.id')
        ->whereIn('sfr.scenario_facility_group_id', $evFacGrpStatusColl->getKeys())
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    $this->evFacRscs = 0;
    foreach ($scenarioFacilityResources as $scFacRscId => $scFacRsc) {
      $this->evFacRscs++;
      $evFacGrp = $evFacGrpStatusColl[$scFacRsc['scenario_facility_group_id']];

      $evFacRsc = new agEventFacilityResource($evFacRscTable, TRUE);
      $evFacRsc['event_facility_group_id'] = $evFacGrp['event_facility_group_id'];
      $evFacRsc['facility_resource_id'] = $scFacRsc['facility_resource_id'];
      $evFacRsc['activation_sequence'] = $scFacRsc['activation_sequence'];
      $evFacRscColl->add($evFacRsc, $scFacRscId);

      // we hold onto the group allocation status for activation time setting
      if (isset($activeGroupAllocationStatus[$evFacGrp['facility_group_allocation_status_id']])) {
        $activeGrpScFacRsc[$scFacRscId] = TRUE;
      }
    }
    
    // release a little memory
    $evFacGrpStatusColl->free(TRUE);
    unset($evFacGrpStatusColl);

    // save the collection to get the new ids, now iterate and create status records
    $evFacRscColl->save($this->conn);
    foreach ($evFacRscColl as $scFacRscId => $evFacRsc) {
      $evFacRscStatus = new agEventFacilityResourceStatusBulkLoad($evFacRscStatusTable, TRUE);
      $evFacRscStatus['event_facility_resource_id'] = $evFacRsc['id'];
      $evFacRscStatus['time_stamp'] = $this->recTimestamp;
      $evFacRscStatus['facility_resource_allocation_status_id'] = $scenarioFacilityResources[$scFacRscId]['facility_resource_allocation_status_id'];
      $evFacRscStatusColl->add($evFacRscStatus, $scFacRscId);
    }

    // free a little memory
    unset($evFacRsc);
    $evFacRscColl->free(TRUE);
    unset($evFacRscColl);

    // save and check whether or not we need to add an activation time
    $evFacRscStatusColl->save($this->conn);
    foreach($evFacRscStatusColl as $scFacRscId => $evFacRscStatus) {
      // going to use this twice, might as well only pay for it once
      $evFacRscId = $evFacRscStatus['event_facility_resource_id'];

      // build this map for pass-through to the next series of methods
      $scFacRscMap->$scFacRscId = $evFacRscId;

      // check for conditions that indicate a new activation time record
      if(isset($staffedResourceAllocationStatus[$evFacRscStatus['facility_resource_allocation_status_id']]) &&
        isset($activeGrpScFacRsc[$scFacRscId])) {
        $evFacRscActivation = new agEventFacilityResourceActivationTime($evFacRscActivationTable, TRUE);
        $evFacRscActivation['event_facility_resource_id'] = $evFacRscId;
        $evFacRscActivation['activation_time'] = $this->zeroHour;
        $evFacRscActivationColl->add($evFacRscActivation);
      }
    }
    unset($evFacRscStatus);
    $evFacRscStatusColl->free(TRUE);

    $evFacRscActivationColl->save($this->conn);
    $evFacRscActivationColl->free(TRUE);

    return $scFacRscMap;
  }

  /**
   * Method to migrate event shifts
   * @param stdClass $scFacRscMap A standard object used for storage of scenario => event facility
   * resource ids
   */
  protected function migrateShifts(stdClass $scFacRscMap)
  {
    // we only need to do this once
    $table = $this->conn->getTable('agEventShift');
    $this->evShifts = 0;

    // iterate each facility resource
    foreach ($scFacRscMap as $scenarioFacilityResourceId => $eventFacilityResourceId) {
      // reset our timer
      set_time_limit($this->batchTime);

      // create a new collection
      $coll = new Doctrine_Collection('agEventShift');

      // query for the shifts associated with this facility resource
      $scenarioShifts = agDoctrineQuery::create($this->conn)
        ->select('ss.*')
          ->from('agScenarioShift ss')
          ->where('ss.scenario_facility_resource_id = ?', $scenarioFacilityResourceId)
          ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

      // add the shifts to the collection
      foreach ($scenarioShifts as $scenShift) {
        $this->evShifts++;

        // create a new shift record
        $eventShift = new agEventShift($table, TRUE);
        $eventShift['event_facility_resource_id'] = $eventFacilityResourceId;
        $eventShift['staff_resource_type_id'] = $scenShift['staff_resource_type_id'];
        $eventShift['minimum_staff'] = $scenShift['minimum_staff'];
        $eventShift['maximum_staff'] = $scenShift['maximum_staff'];
        $eventShift['minutes_start_to_facility_activation'] = $scenShift['minutes_start_to_facility_activation'];
        $eventShift['task_length_minutes'] = $scenShift['task_length_minutes'];
        $eventShift['break_length_minutes'] = $scenShift['break_length_minutes'];
        $eventShift['task_id'] = $scenShift['task_id'];
        $eventShift['shift_status_id'] = $scenShift['shift_status_id'];
        $eventShift['staff_wave'] = $scenShift['staff_wave'];
        $eventShift['deployment_algorithm_id'] = $scenShift['deployment_algorithm_id'];
        $eventShift['originator_id'] = $scenShift['originator_id'];

        // add the record to our collection
        $coll->add($eventShift);
      }

      // save the collection and release circular references
      $coll->save($this->conn);
      $coll->free(TRUE);
    }
  }
}