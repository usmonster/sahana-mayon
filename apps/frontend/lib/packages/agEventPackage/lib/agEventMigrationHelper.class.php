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

  /**
   * Provides check information (count of empty facility groups) for this scenario
   * @param integer $scenario_id the scenario to pre-check for migration
   * @return integer count of empty facility groups
   */
  public static function facilityGroupCheck($scenario_id)
  {
    $facilityGroupQuery = agDoctrineQuery::create()
        ->select('aFG.id, aFG.scenario_facility_group')
        ->from('agScenarioFacilityGroup aFG')
        ->leftJoin('aFG.agScenarioFacilityResource aFR')
        ->where('aFR.id is NULL');
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
   */
  protected static function migrateFacilityGroups($scenario_id, $event_id, Doctrine_Connection $conn = NULL)
  {
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    $existingScenarioFacilityGroups = Doctrine_Core::getTable('agScenarioFacilityGroup')->findBy('scenario_id',
                                                                                                 $scenario_id);

    foreach ($existingScenarioFacilityGroups as $scenFacGrp) {
      $eventFacilityGroup = new agEventFacilityGroup();
      $eventFacilityGroup->set('event_id', $event_id)
          ->set('event_facility_group', $scenFacGrp->scenario_facility_group)
          ->set('facility_group_type_id', $scenFacGrp->facility_group_type_id)
          ->set('activation_sequence', $scenFacGrp->activation_sequence);
      $eventFacilityGroup->save($conn);

      $eventFacilityGroupStatus = new agEventFacilityGroupStatus();
      $eventFacilityGroupStatus->set('event_facility_group_id', $eventFacilityGroup->id)
          ->set('time_stamp', date('Y-m-d H:i:s', time()))
          ->set('facility_group_allocation_status_id',
                $scenFacGrp->facility_group_allocation_status_id);
      $eventFacilityGroupStatus->save($conn);

      $existingFacilityResources = self::migrateFacilityResources($scenFacGrp,
                                                                  $eventFacilityGroup->id,
                                                                  $event_id,
                                                                  $conn);

      $eventFacilityGroup->free(TRUE);
      $eventFacilityGroupStatus->free(TRUE);
    }
    $existingScenarioFacilityGroups->free(TRUE);
  }

  /**
   * Migrates facility resources from a scenario facility group to an event facility group
   * @param integer $scenarioFacilityResourceId the facility resource with shifts to be migrated
   * @param integer $eventFacilityResourceId the facility resource with shifts for migration
   * @param Doctrine_Connection $conn A doctrine connection object
   * @return integer Count of shifts migrated from Scenario to Event
   */
  protected static function migrateFacilityResources($scenarioFacilityGroup, $event_facility_group_id,
      $event_id, Doctrine_Connection $conn = NULL)
  {
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    //get the event's zero hour for setting in facility resources with the proper conditions
    $eventZeroHour =  agDoctrineQuery::create()
        ->select('ae.zero_hour')
        ->from('agEvent ae')
        ->where('ae.id = ?', $event_id)
        ->execute(array(),Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    
    $existingScenarioFacilityResources = $scenarioFacilityGroup->getAgScenarioFacilityResource();
    foreach ($existingScenarioFacilityResources as $scenFacRes) {
      $eventFacilityResource = new agEventFacilityResource();
      $eventFacilityResource->set('event_facility_group_id', $event_facility_group_id)
          ->set('facility_resource_id', $scenFacRes->facility_resource_id)
          ->set('activation_sequence', $scenFacRes->activation_sequence);
      $eventFacilityResource->save($conn);

      $eventFacilityResourceStatus = new agEventFacilityResourceStatus();
      $eventFacilityResourceStatus->set('event_facility_resource_id', $eventFacilityResource->id)
          ->set('time_stamp', date('Y-m-d H:i:s', time()))
          ->set('facility_resource_allocation_status_id',
                $scenFacRes->facility_resource_allocation_status_id);
      $eventFacilityResourceStatus->save($conn);

      $staffedAllocationStatus = agDoctrineQuery::create()
        ->select('afras.id')
        ->from('agFacilityResourceAllocationStatus afras')
        ->where('afras.staffed = ?', TRUE)
        ->useResultCache(TRUE, 3600)
        ->execute(array(),agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

      $activeGroupAllocationStatus = agDoctrineQuery::create()
        ->select('afgas.id')
        ->from('agFacilityGroupAllocationStatus afgas')
        ->where('afgas.active = ?', TRUE)
        ->useResultCache(TRUE, 3600)
        ->execute(array(),agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

      //if the coniditions are met, set the activation time to the event zero hour.
      if(in_array($scenFacRes['facility_resource_allocation_status_id'], $staffedAllocationStatus)
         && in_array($scenarioFacilityGroup['facility_group_allocation_status_id'], $activeGroupAllocationStatus))
      {

        $eventFacilityResourceActivationTime = new agEventFacilityResourceActivationTime();
        $eventFacilityResourceActivationTime->set('event_facility_resource_id', $eventFacilityResource->id)
          ->set('activation_time', $eventZeroHour);
        $eventFacilityResourceActivationTime->save($conn);
        
      }

      // Should the shifts be process as the facility resources get processed?
      // Another solution: create a temp table mapping the scenario to event facility resources?
      self::migrateShifts($scenFacRes->id, $eventFacilityResource->id, $conn);

      $eventFacilityResource->free(TRUE);
      $eventFacilityResourceStatus->free(TRUE);
    }

    $existingScenarioFacilityResources->free(TRUE);
    //return 1;
  }

  /**
   * Migrates shifts from a scenrio facility resource to an event facility resource
   * @param integer $scenarioFacilityResourceId the facility resource with shifts to be migrated
   * @param integer $eventFacilityResourceId the facility resource with shifts for migration
   * @param Doctrine_Connection $conn A doctrine connection object
   * @return integer Count of shifts migrated from Scenario to Event
   * @todo Don't use the getTable methods or findby (all of which are VERY expensive)
   */
  protected static function migrateShifts($scenarioFacilityResourceId, $eventFacilityResourceId,
      Doctrine_Connection $conn = NULL)
  {
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    $scenarioShifts = Doctrine_Core::getTable('agScenarioShift')->findby('scenario_facility_resource_id',
                                                                         $scenarioFacilityResourceId);
    foreach ($scenarioShifts as $scenShift) {
      // At this point all fields in agEventShifts will be populated with agScenarioShifts.  Only
      // the real time fields in agEvnetShifts will not be populated.  It will be done so at a later
      // time when agEventFacilityActivationTime is populated.
      $eventShift = new agEventShift();
      $eventShift->set('event_facility_resource_id', $eventFacilityResourceId)
          ->set('staff_resource_type_id', $scenShift['staff_resource_type_id'])
          ->set('minimum_staff', $scenShift['minimum_staff'])
          ->set('maximum_staff', $scenShift['maximum_staff'])
          ->set('minutes_start_to_facility_activation',
                $scenShift['minutes_start_to_facility_activation'])
          ->set('task_length_minutes', $scenShift['task_length_minutes'])
          ->set('break_length_minutes', $scenShift['break_length_minutes'])
          ->set('task_id', $scenShift['task_id'])
          ->set('shift_status_id', $scenShift['shift_status_id'])
          ->set('staff_wave', $scenShift['staff_wave'])
          ->set('deployment_algorithm_id', $scenShift['deployment_algorithm_id'])
          ->set('originator_id', $scenShift['originator_id']);
      $eventShift->save();
      $eventShift->free(TRUE);
    }
    $scenarioShifts->free(TRUE);
    return count($scenarioShifts);
    //return 1;
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

    $existingScenarioStaffPools = agDoctrineQuery::create()
        ->from('agScenarioStaffResource ssr')
        ->where('scenario_id', $scenario_id)
        ->orderBy('deployment_weight')
        ->execute();
    foreach ($existingScenarioStaffPools AS $scenStfPool) {
      $eventStaff = new agEventStaff();
      $eventStaff->set('event_id', $event_id)
          ->set('staff_resource_id', $scenStfPool->staff_resource_id)
          ->set('deployment_weight', $scenStfPool->deployment_weight);
      $eventStaff->save();

      $unAllocatedStaffStatus = agEventStaffHelper::returnDefaultEventStaffStatus();
      
      $eventStaffStatus = new agEventStaffStatus();
      $eventStaffStatus->set('event_staff_id', $eventStaff->id)
          ->set('time_stamp', date('Y-m-d H:i:s', time()))
          ->set('staff_allocation_status_id', $unAllocatedStaffStatus);
      $eventStaffStatus->save();
      $eventStaffStatus->free(TRUE);

      $eventStaff->free(TRUE);
    }

    $existingScenarioStaffPools->free(TRUE);
    //return 1;
    return count($existingScenarioStaffPools);
  }

  /**
   * Parent function to migrate a scenario to an event
   * @param integer $scenario_id the id of the scenario being operated on
   * @param integer $event_id the id of the event being operated on
   * @return status code indicating whether the migration was a success or not
   */
  public static function migrateScenarioToEvent($scenario_id, $event_id)
  {

    $conn = Doctrine_Manager::connection();
    $conn->beginTransaction();

    try {

      /**
       * @todo
       * 0a. Check event status.  Event status must be 'pre-deploy' state.  DO NOT migrate scenario for any other event status.
       * 0b. Clean-out event related tables prior to migrating any scenario related tables.
       * 1a. Regenerate scenario shift
       */
      $test = agShiftGeneratorHelper::shiftGenerator($scenario_id, $conn);
      /**
       * @todo
       * 1b. Copy Facility Group
       * 1c. Copy Facility Resource
       * 1d. Copy over scenario shift
       */
      self::migrateFacilityGroups($scenario_id, $event_id, $conn);

      /**
       * @todo
       * 2. Populate facility start time, update event shift with real time, update facility resource/group status.
       * 3. Regenerate staff pool
       */
      agDoctrineQuery::create($conn)
        ->delete()
          ->from('agEventStaff')
          ->where('event_id = ?', $eventId)
          ->execute();
      /**
       * @todo Wrap in an event helper class.
       */
      //regenerate staff pool with new search conditions
      agStaffGeneratorHelper::generateStaffPool($scenario_id, FALSE, $conn);

      // 4. Copy over staff pool
      self::migrateStaffPool($scenario_id, $event_id, $conn);
      // 5. Populate agEventStaffShift (assigning event staffs to shifts).
      // 6. Update event status to deployed/active?

      $conn->commit();
    } catch (Exception $e) {
      $conn->rollBack();

      throw $e;
    }

    return $migrationResult;
  }

}