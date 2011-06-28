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
    $returnvalue = $facilityGroupQuery->execute(array(), 'key_value_pair');
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
    $facilityShiftReturn = $undefinedFacilityShiftQuery->execute(array(), 'single_value_array');
    $undefinedFacilityShiftQuery->free();

    $undefinedStaffShiftQuery = agDoctrineQuery::create()
        ->select('aSRT.id, aSSR.*, aSR.id')
        ->from('agScenarioStaffResource aSSR')
        ->innerJoin('aSSR.agStaffResource aSR')
        ->innerJoin('aSR.agStaffResourceType aSRT')
        ->leftJoin('aSRT.agScenarioShift aSS')
        ->where('aSSR.scenario_id =?', $scenario_id)
        ->andWhere('aSRT.id is NULL'); //returns the staff resource types without a shift
    $staffShiftReturn = $undefinedStaffShiftQuery->execute(array(), 'single_value_array');
    $undefinedStaffShiftQuery->free();

    return array($facilityShiftReturn, $staffShiftReturn);
  }

  /**
   * Provides check information (count of staff resources) available for this scenario
   * @param integer $scenario_id the scenario to pre-check for migration
   * @return integer count of staff resources
   */
  public static function staffPoolCheck($scenario_id)
  {
    $staffPoolQuery = agDoctrineQuery::create()
        ->from('agScenarioStaffResource')
        ->where('scenario_id =?', $scenario_id);
    return $staffPoolQuery->count();
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
    $undefinedShiftCheck = self::undefinedShiftCheck($scenario_id);
    $undefinedFacilityShifts = $undefinedShiftCheck[0];
    $undefinedStaffShifts = $undefinedShiftCheck[1];

    return array('Empty facility groups' => $facilityGroupCheck, 'Staff pool count' => $staffPoolCheck, 'Undefined facility shifts' => $undefinedFacilityShifts, 'Undefined staff shifts' => $undefinedStaffShifts);
  }

  /**
   * Migrates facility groups from a scenario to an event
   * @param integer $scenario_id the id of the scenario from which we will copy facility groups
   * @param integer $event_id the id of the event to which facility groups are migrated
   * @param Doctrine_Connection $conn A doctrine connection object
   * @return array Returns an array of migrated counts of elements, keyed by Facility Groups,
   * Facility Resources, and Shifts.  Returns the following array:
   * array('Facility Groups' => count of migrated facility groups,
   *       'Facility Resources' => count of migrated facility resources,
   *       'Shifts' => count of migrated shifts
   *      )
   */
  protected static function migrateFacilityGroups($scenario_id, $event_id, Doctrine_Connection $conn = NULL)
  {
    $results = array('Facility Resources' => 0, 'Shifts' => 0);

    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    // get the existing scenario facility groups
    $existingScenarioFacilityGroups = agDoctrineQuery::create()
      ->select('sfg.*')
        ->addSelect('fgas.*')
        ->from('agScenarioFacilityGroup sfg')
        ->innerJoin('sfg.agFacilityGroupAllocationStatus fgas')
        ->where('sfg.scenario_id = ?', $scenario_id)
        ->execute(array(), Doctrine_Core::HYDRATE_ON_DEMAND);

    // grab a count for later
    $facilityGroupCount = 0;

    $mt = new agMemoryTester();

    foreach ($existingScenarioFacilityGroups as $scenFacGrp) {

      // create a new facility group
      $eventFacilityGroup = new agEventFacilityGroup();
      $eventFacilityGroup['event_id'] = $event_id;
      $eventFacilityGroup['event_facility_group'] = $scenFacGrp['scenario_facility_group'];
      $eventFacilityGroup['facility_group_type_id'] = $scenFacGrp['facility_group_type_id'];
      $eventFacilityGroup['activation_sequence'] = $scenFacGrp['activation_sequence'];
      $eventFacilityGroup->save($conn);

      // get its id
      $efgId = $eventFacilityGroup->getId();

      // create a new event facility group status
      $eventFacilityGroupStatus = new agEventFacilityGroupStatus();
      $eventFacilityGroupStatus['event_facility_group_id'] = $efgId;
      $eventFacilityGroupStatus['time_stamp'] = date('Y-m-d H:i:s', time());
      $eventFacilityGroupStatus['facility_group_allocation_status_id'] = $scenFacGrp['facility_group_allocation_status_id'];
      $eventFacilityGroupStatus->save($conn);

      $tempResult = self::migrateFacilityResources($scenFacGrp, $efgId, $event_id, $conn);
      $results['Facility Resources'] += $tempResult['Facility Resources'];
      $results['Shifts'] += $tempResult['Shifts'];

      // reclaim some memory
      $eventFacilityGroup->free(TRUE);
      $eventFacilityGroupStatus->free(TRUE);
      unset($eventFacilityGroup);
      unset($eventFacilityGroupStatus);
      $conn->flush();
      $conn->evictTables();
      $conn->clear();

      // up our counter
      $facilityGroupCount++;
      $mt->test();
    }

    return array('Facility Groups' => $facilityGroupCount) + $results;
  }

  /**
   * Migrates facility resources from a scenario facility group to an event facility group
   * @param agScenarioFacilityGroup $scenFacGrp the facility group with resources to be migrated
   * @param integer $eventFacilityGroupId the event facility group to which the resources will belong
   * @param integer $eventId The event being created
   * @param Doctrine_Connection $conn A doctrine connection object
   * @return array Returns an array of migrated counts of elements, keyed by Facility Resources and
   * Shifts.  Returns the following array:
   * array('Facility Resources' => count of migrated facility resources,
   *       'Shifts' => count of migrated shifts
   *      )
   */
  protected static function migrateFacilityResources($scenFacGrp, $eventFacilityGroupId,
      $event_id, Doctrine_Connection $conn = NULL)
  {
    $results = array('Shifts' => 0);

    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    //get the event's zero hour for setting in facility resources with the proper conditions
    // @todo Put this result into a property somewhere so this doesn't get called again and again
    // for each facility group --> that or make these proper objects and share via $this->
    $eventZeroHour =  agDoctrineQuery::create()
        ->select('ae.zero_hour')
        ->from('agEvent ae')
        ->where('ae.id = ?', $event_id)
        ->useResultCache(TRUE, 1800)
        ->execute(array(),Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    // @todo Put this result into a property somewhere so this doesn't get called again and again
    // for each facility group --> that or make these proper objects and share via $this->
    $staffedAllocationStatus = agDoctrineQuery::create()
      ->select('afras.id')
      ->from('agFacilityResourceAllocationStatus afras')
      ->where('afras.staffed = ?', TRUE)
      ->useResultCache(TRUE, 3600)
      ->execute(array(),agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    // grab our scenario facility resources
    $existingScenarioFacilityResources = agDoctrineQuery::create()
      ->select('sfr.*')
        ->from('agScenarioFacilityResource sfr')
        ->where('sfr.scenario_facility_group_id = ?', $scenFacGrp['id'])
        ->execute(array(), Doctrine_Core::HYDRATE_ON_DEMAND);

    // pick this up once so we don't do so in a group
    $activeGroup = $scenFacGrp['agFacilityGroupAllocationStatus']['active'];
    
    // initialize a counter
    $facilityResourceCount = 0;

    foreach ($existingScenarioFacilityResources as $scenFacRes) {

      // create a new event facility resource
      $eventFacilityResource = new agEventFacilityResource();
      $eventFacilityResource['event_facility_group_id'] = $eventFacilityGroupId;
      $eventFacilityResource['facility_resource_id'] = $scenFacRes['facility_resource_id'];
      $eventFacilityResource['activation_sequence'] = $scenFacRes['activation_sequence'];
      $eventFacilityResource->save($conn);

      // get the id to be re-used later
      $efrId = $eventFacilityResource->getId();

      // then create its status entry
      $eventFacilityResourceStatus = new agEventFacilityResourceStatusBulkLoad();
      $eventFacilityResourceStatus['event_facility_resource_id'] = $efrId;
      $eventFacilityResourceStatus['time_stamp'] = date('Y-m-d H:i:s', time());
      $eventFacilityResourceStatus['facility_resource_allocation_status_id'] = $scenFacRes['facility_resource_allocation_status_id'];
      $eventFacilityResourceStatus->save($conn);

      //if the conditions are met, set the activation time to the event zero hour.
      if($activeGroup && in_array($scenFacRes['facility_resource_allocation_status_id'], $staffedAllocationStatus))
      {
        $eventFacilityResourceActivationTime = new agEventFacilityResourceActivationTime();
        $eventFacilityResourceActivationTime['event_facility_resource_id'] = $efrId;
        $eventFacilityResourceActivationTime['activation_time'] = $eventZeroHour;
        $eventFacilityResourceActivationTime->save($conn);
        $eventFacilityResourceActivationTime->free(TRUE);
        unset($eventFacilityResourceActivationTime);
      }

      // release the objects
      $eventFacilityResource->free(TRUE);
      $eventFacilityResourceStatus->free(TRUE);
      unset($eventFacilityResource);
      unset($eventFacilityResourceStatus);

      // Generate shifts
      $results['Shifts'] += self::migrateShifts($scenFacRes['id'], $efrId, $conn);

      // up our counter
      $facilityResourceCount++;
    }
    unset($existingScenarioFacilityResources);

    return array('Facility Resources' => $facilityResourceCount) + $results;
  }

  /**
   * Migrates shifts from a scenrio facility resource to an event facility resource
   * @param integer $scenarioFacilityResourceId the facility resource with shifts to be migrated
   * @param integer $eventFacilityResourceId the facility resource with shifts for migration
   * @param Doctrine_Connection $conn A doctrine connection object
   * @return integer Count of shifts migrated from Scenario to Event
   */
  protected static function migrateShifts($scenarioFacilityResourceId, $eventFacilityResourceId,
      Doctrine_Connection $conn = NULL)
  {
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    $scenarioShifts = agDoctrineQuery::create()
      ->select('ss.*')
        ->from('agScenarioShift ss')
        ->where('ss.scenario_facility_resource_id = ?', $scenarioFacilityResourceId)
        ->execute(array(), Doctrine_Core::HYDRATE_ON_DEMAND);
    
    // create a collection
    $coll = new Doctrine_Collection('agEventShift');

    foreach ($scenarioShifts as $scenShift) {
      
      // create a new shift record
      $eventShift = new agEventShift();
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

    // save the collection
    $coll->save($conn);
    $shiftCount = $coll->count();

    // free up some memory
    $coll->free(TRUE);
    $conn->clear();
    unset($coll);
    unset($scenarioShifts);

    return $shiftCount;
  }

  /**
   * Migrates a pool of staff resources from a scenario to an event
   * @param integer $scenario_id the id of the scenario being operated on
   * @param integer $event_id the id of the event being operated on
   * @param Doctrine_Connection $conn A doctrine connection object
   * @return integer Count of how many staff members were migrated.
   */
  protected static function migrateStaffPool($scenario_id, $event_id, Doctrine_Connection $conn = NULL)
  {
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    // set the staff query
    $existingScenarioStaff = agDoctrineQuery::create()
      ->select('ssr.*')
        ->from('agScenarioStaffResource ssr')
        ->where('scenario_id = ?', $scenario_id)
        ->execute(array(), Doctrine_Core::HYDRATE_ON_DEMAND);

    // get the staff allocation status
    $unAllocatedStaffStatus = agEventStaffHelper::returnDefaultEventStaffStatus();

    // initialize an iterator
    $staffCount = 0;

    $mt = new agMemoryTester();
    foreach ($existingScenarioStaff AS $scenStfPool) {

      // create an agEventStaff record
      $eventStaff = new agEventStaffBulkLoad();
      $eventStaff['event_id'] = $event_id;
      $eventStaff['staff_resource_id'] = $scenStfPool['staff_resource_id'];
      $eventStaff['deployment_weight'] = $scenStfPool['deployment_weight'];
      $eventStaff->save($conn);
      $eventStaffId = $eventStaff->getId();
      $eventStaff->free(TRUE);
      unset($eventStaff);

      // create the staff status record
      $eventStaffStatus = new agEventStaffStatus();
      $eventStaffStatus['event_staff_id'] = $eventStaffId;
      $eventStaffStatus['time_stamp'] = date('Y-m-d H:i:s', time());
      $eventStaffStatus['staff_allocation_status_id'] = $unAllocatedStaffStatus;
      $eventStaffStatus->save($conn);
      $eventStaffStatus->free(TRUE);
      unset($eventStaffStatus);

      $staffCount++;
      $mt->test();
    }

    // free up some memory
    unset($existingScenarioStaff);

    return $staffCount;
  }

  /**
   * Parent function to migrate a scenario to an event
   * @param integer $scenario_id the id of the scenario being operated on
   * @param integer $event_id the id of the event being operated on
   * @return array $migrationResult Returns an array of migrated counts of elements, keyed by
   * Facility Groups, Facility Resources, Shifts, and Staffs.  Returns the following array:
   * array('Facility Groups' => count of migrated facility groups,
   *       'Facility Resources' => count of migrated facility resources,
   *       'Shifts' => count of migrated shifts,
   *       'Staffs' => count of migrated staff resources
   *      )
   */
  public static function migrateScenarioToEvent($scenario_id, $event_id)
  {
    // all of this jazz helps us keep a low memory profile
    $dm = Doctrine_Manager::getInstance();
    $dm->setCurrentConnection('doctrine');
    $adapter = $dm->getCurrentConnection()->getDbh();
//    $adapter = clone $adapter;
    $conn = Doctrine_Manager::connection($adapter, self::CONN_MIGRATION_WRITE);

    // this will disable re-use of query objects but keeps the connection memory profile low
    $conn->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, TRUE);
    $conn->setAttribute(Doctrine_Core::ATTR_AUTOCOMMIT, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_LOAD_REFERENCES, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_VALIDATE, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_CASCADE_SAVES, FALSE);

    // return back to our 'normal' doctrine connection before moving further
    $dm->setCurrentConnection('doctrine');

    // yay, transactions = data integrity
    $conn->beginTransaction();

    $mt = new agMemoryTester();
    try {

      /**
       * @todo
       * 0a. Check event status.  Event status must be 'pre-deploy' state.  DO NOT migrate scenario for any other event status.
       * 0b. Clean-out event related tables prior to migrating any scenario related tables.
       * 1a. Regenerate scenario shift
       */

      /**
       * 1b. Copy Facility Group
       * 1c. Copy Facility Resource
       * 1d. Copy over scenario shift
       */
      $migrationResult = self::migrateFacilityGroups($scenario_id, $event_id, $conn);
      $conn->clear();
      $conn->flush();
      $conn->evictTables();
      $conn->clear();
      $mt->test();

      /**
       * @todo
       * 2. Update event shift with real time
       */

      /**
       * 3. Regenerate staff pool
       */
      agStaffGeneratorHelper::generateStaffPool($scenario_id, FALSE, $conn);
      $conn->clear();
      $conn->flush();
      $conn->evictTables();
      $conn->clear();
      $mt->test();

      // 4. Copy over staff pool
      $migrationResult['Staffs'] = self::migrateStaffPool($scenario_id, $event_id, $conn);
      $mt->test();

      $conn->commit();
    } catch (Exception $e) {
      $conn->rollBack();
      throw $e;
    }
    $conn->clear();
    $conn->flush();
    $conn->evictTables();
    $conn->clear();

    print_r($mt->getResults());
    return $migrationResult;
  }

}