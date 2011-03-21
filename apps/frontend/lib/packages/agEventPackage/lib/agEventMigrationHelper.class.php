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

class agEventMigrationHelper
{
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

  public static function staffPoolCheck($scenario_id)
  {
    $staffPoolQuery = agDoctrineQuery::create()
            ->from('agScenarioStaffResource')
            ->where('scenario_id =?', $scenario_id);
    return $staffPoolQuery->count();
  }

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

  public static function migrateFacilityGroups($scenario_id, $event_id)
  {
    $existingScenarioFacilityGroups = Doctrine_Core::getTable('agScenarioFacilityGroup')->findBy('scenario_id', $scenario_id);

    foreach ($existingScenarioFacilityGroups as $scenFacGrp) {
      $eventFacilityGroup = new agEventFacilityGroup();
      $eventFacilityGroup->set('event_id', $event_id)
          ->set('event_facility_group', $scenFacGrp->scenario_facility_group)
          ->set('facility_group_type_id', $scenFacGrp->facility_group_type_id)
          ->set('activation_sequence', $scenFacGrp->activation_sequence);
      $eventFacilityGroup->save();

      $eventFacilityGroupStatus = new agEventFacilityGroupStatus();
      $eventFacilityGroupStatus->set('event_facility_group_id', $eventFacilityGroup->id)
          ->set('time_stamp', new Doctrine_Expression('CURRENT_TIMESTAMP'))
          ->set('facility_group_allocation_status_id', $scenFacGrp->facility_group_allocation_status_id);
      $eventFacilityGroupStatus->save();

      $existingFacilityResources = self::migrateFacilityResources($scenFacGrp, $eventFacilityGroup->id);

      $eventFacilityGroup->free(TRUE);
      $eventFacilityGroupStatus->free(TRUE);
    }
    $existingScenarioFacilityGroups->free(TRUE);
  }

  public static function migrateFacilityResources($scenarioFacilityGroup, $event_facility_group_id)
  {
    $existingScenarioFacilityResources = $scenarioFacilityGroup->getAgScenarioFacilityResource();
    foreach ($existingScenarioFacilityResources as $scenFacRes) {
      $eventFacilityResource = new agEventFacilityResource();
      $eventFacilityResource->set('event_facility_group_id', $event_facility_group_id)
          ->set('facility_resource_id', $scenFacRes->facility_resource_id)
          ->set('activation_sequence', $scenFacRes->activation_sequence);
      $eventFacilityResource->save();

      $eventFacilityResourceStatus = new agEventFacilityResourceStatus();
      $eventFacilityResourceStatus->set('event_facility_resource_id', $eventFacilityResource->id)
          ->set('time_stamp', new Doctrine_Expression('CURRENT_TIMESTAMP'))
          ->set('facility_resource_allocation_status_id', $scenFacRes->facility_resource_allocation_status_id);
      $eventFacilityResourceStatus->save();

      // Should the shifts be process as the facility resources get processed?  Another solution is to create a temp table mapping the scenario to event faciltiy resources?
      self::migrateShifts($scenFacRes->id, $eventFacilityResource->id);

      $eventFacilityResource->free(TRUE);
      $eventFacilityResourceStatus->free(TRUE);
    }

    $existingScenarioFacilityResources->free(TRUE);
    return 1;
  }

  public static function migrateShifts($scenarioFacilityResourceId, $eventFacilityResourceId)
  {
    $scenarioShifts = Doctrine_Core::getTable('agScenarioShift')->findby('scenario_facility_resource_id', $scenarioFacilityResourceId);
    foreach ($scenarioShifts as $scenShift) {
      // At this point all fields in agEventShifts will be populated with agScenarioShifts.  Only
      // the real time fields in agEvnetShifts will not be populated.  It will be done so at a later
      // time when agEventFacilityActivationTime is populated.
      $eventShift = new agEventShift();
      $eventShift->set('event_facility_resource_id', $eventFacilityResourceId)
          ->set('staff_resource_type_id', $scenShift->staff_resource_type_id)
          ->set('minimum_staff', $scenShift->minimum_staff)
          ->set('maximum_staff', $scenShift->maximum_staff)
          ->set('minutes_start_to_facility_activation', $scenShift->minutes_start_to_facility_activation)
          ->set('task_length_minutes', $scenShift->task_length_minutes)
          ->set('break_length_minutes', $scenShift->break_length_minutes)
          ->set('task_id', $scenShift->task_id)
          ->set('shift_status_id', $scenShift->shift_status_id)
          ->set('staff_wave', $scenShift->staff_wave)
          ->set('deployment_algorithm_id', $scenShift->deployment_algorithm_id);
      $eventShift->save();
      $eventShift->free(TRUE);
    }
    $scenarioShifts->free(TRUE);
    return 1;
  }

  public static function migrateStaffPool($scenario_id, $event_id)
  {
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

      // @TODO Staff allocation status should be determine by the message responses.  Currently it is hard-coded to 1 as available.
      $unAvailableStaffStatus = Doctrine_Core::getTable('agStaffAllocationStatus')->findby('staff_allocation_status', 'unavailable');
      $eventStaffStatus = new agEventStaffStatus();
      $eventStaffStatus->set('event_staff_id', $eventStaff->id)
          ->set('time_stamp', new Doctrine_Expression('CURRENT_TIMESTAMP'))
          ->set('staff_allocation_status_id', $unAvailableStaffStatus);
      $eventStaffStatus->free(TRUE);

      $eventStaff->free(TRUE);
    }

    $existingScenarioStaffPools->free(TRUE);
    return 1;
  }

  public static function migrateScenarioToEvent($scenario_id, $event_id)
  {

    $con = Doctrine_Manager::getInstance()->getConnectionForComponent('agEvent');

    try {
      $con->beginTransaction();

      /**
       * @todo
       * 0a. Check event status.  Event status must be 'pre-deploy' state.  DO NOT migrate scenario for any other event status.
       * 0b. Clean-out event related tables prior to migrating any scenario related tables.
       * 1a. Regenerate scenario shift
       */
      agScenarioGenerator::shiftGenerator();
      /**
       * @todo
       * 1b. Copy Faciltiy Group
       * 1c. Copy Facility Resource
       * 1d. Copy over scenario shift
       */
      self::migrateFacilityGroups($scenario_id, $event_id);

      /**
       * @todo
       * 2. Populate facility start time, update event shift with real time, update facility resource/group status.
       * 3. Regenerate staff pool
       */
      Doctrine_query::create()->from('agScenarioStaffResource')->delete();
      Doctrine_query::create()->from('agEventStaff')->delete();
      /**
       * @todo Wrap in an event helper class.
       */
      $lucene_queries = agDoctrineQuery::create()
              ->select('ssg.id, ssg.scenario_id, ssg.search_weight, ls.query_condition, ls.id')
              ->from('agScenarioStaffGenerator ssg')
              ->innerJoin('ssg.agLuceneSearch ls')
              ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
      foreach ($lucene_queries as $lucene_query) {
        $staff_resource_ids = agScenarioGenerator::staffPoolGenerator($lucene_query['ls_query_condition'], $lucene_query['ssg_scenario_id']);
        agScenarioGenerator::saveStaffPool($staff_resource_ids, $scenario_id, $lucene_query['ssg_search_weight']);
      }

      // 4. Copy over staff pool
      self::migrateStaffPool($scenario_id, $event_id);
      // 5. Populate agEventStaffShift (assigning event staffs to shifts).
      // 6. Update event status to deployed/active?

      $con->commit();
    } catch (Exception $e) {
      $con->rollBack();

      throw $e;
    }

    return $migrationResult;
  }
}