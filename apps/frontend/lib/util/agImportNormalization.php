<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of agImportNormalization
 *
 * @author shirley.chan
 */
class agImportNormalization {

  public $events = array();
  public $numRecordsNormalized;
  public $numRecordsFailed;

  function __construct($scenarioId, $sourceTable, $XLSColumnHeader) {
    $this->scenarioId = $scenarioId;
    $this->sourceTable = $sourceTable;
    $this->XLSColumnHeader = is_array($XLSColumnHeader) ? $XLSColumnHeader : array();
  }

  function __destruct() {

  }

  private function dataValidation() {
// Do data validations here.
  }

  public function normalizeImport() {
    try {
      $facilityResourceTypeIds = Doctrine_Query::create()
                      ->select('frt.facility_resource_type_abbr, frt.id')
                      ->from('agFacilityResourceType frt')
                      ->execute(array(), 'key_value_pair');
      $facilityResourceStatusIds = Doctrine_Query::create()
                      ->select('frs.facility_resource_status, frs.id')
                      ->from('agFacilityResourceStatus frs')
                      ->execute(array(), 'key_value_pair');
      $facilityResourceAllocationStatusIds = Doctrine_Query::create()
                      ->select('facility_resource_allocation_status, id')
                      ->from('agFacilityResourceAllocationStatus')
                      ->execute(array(), 'key_value_pair');
      $facilityGroupTypeIds = Doctrine_Query::create()
                      ->select('facility_group_type, id')
                      ->from('agFacilityGroupType')
                      ->execute(array(), 'key_value_pair');
      $facilityGroupAllocationStatusIds = Doctrine_Query::create()
                      ->select('facility_group_allocation_status, id')
                      ->from('agFacilitygroupAllocationStatus')
                      ->execute(array(), 'key_value_pair');

//      $query = 'SELECT i.facility_name AS facility_name,
//                       i.facility_code AS facility_code,
//                       i.facility_resource_type_abbr,
//                       i.facility_resource_status,
//                       i.facility_capacity AS capacity
//                FROM ' . $this->sourceTable . ' AS i';

      $query = 'SELECT * FROM ' . $this->sourceTable . ' AS i';

//      echo "<BR><BR>query: $query<br><BR>";
      $conn = Doctrine_Manager::connection();
      $pdo = $conn->execute($query);
      $pdo->setFetchMode(Doctrine_Core::FETCH_ASSOC);
      $sourceRecords = $pdo->fetchAll();
//      print_r($sourceRecords);

      $conn->beginTransaction();

      foreach ($sourceRecords as $record) {

        $facility_name = $record['facility_name'];
        $facility_code = $record['facility_code'];
        $facility_resource_type_abbr = $record['facility_resource_type_abbr'];
        $facility_resource_status = $record['facility_resource_status'];
//        $capacity = $record['capacity'];
        $capacity = $record['facility_capacity'];
        $facility_activation_sequence = $record['facility_activation_sequence'];
        $facility_allocation_status = $record['facility_allocation_status'];
        $facility_group = $record['facility_group'];
        $facility_group_type = $record['facility_group_type'];
        $facility_group_allocation_status = $record['facility_group_allocation_status'];
        $facility_group_activation_sequence = 1; // missing from file import $record['facility_group_activation_status'];
// TODO: implement this
        $this->dataValidation();
//        isValid = validate_row(facility_name, facility_code, facility_resource_type_abbr, facility_resource_status, capacity[, ...])
//        if (!isValid):
//          report warning
//          next;

        $facility_resource_type_id = $facilityResourceTypeIds[$facility_resource_type_abbr];
        $facility_resource_status_id = $facilityResourceStatusIds[$facility_resource_status];
        $facility_group_type_id = $facilityGroupTypeIds[$facility_group_type];
        $facility_group_allocation_status_id = $facilityGroupAllocationStatusIds[$facility_group_allocation_status];
        $facility_resource_allocation_status_id = $facilityResourceAllocationStatusIds[$facility_allocation_status];


// tries to find an existing record based on a unique identifier.
//        $facility = Doctrine_Core::getTable('agFacility')->findOneByFacilityCode($record['facility_code']);
        $facility = Doctrine_Query::create()
                        ->from('agFacility f')
                        ->leftJoin('f.agFacilityResource fr')
                        ->leftJoin('fr.agFacilityResourceType frt')
                        ->where('f.facility_code = ?', $facility_code)
                        ->fetchOne();
        $facilityResource = NULL;
        $scenarioFacilityGroup = Doctrine_Query::create()
                        ->from('agScenarioFacilityGroup')
                        ->where('scenario_id = ?', $this->scenarioId)
                        ->andWhere('scenario_facility_group=?', $facility_group)
                        ->fetchOne();
        $scenarioFacilityResource = NULL;

        if (empty($facility)) {
          $facility = $this->createFacility($facility_name, $facility_code);
        } else {
          $facility = $this->updateFacility($facility, $facility_name);
          $facilityResource = Doctrine_Query::create()
                          ->from('agFacilityResource r')
                          ->where('r.facility_id = ?', $facility->getId())
                          ->andWhere('r.facility_resource_type_id = ?', $facility_resource_type_id)
                          ->fetchOne();
          $scenarioFacilityGroup = Doctrine_Query::create()
                          ->from('agScenarioFacilityGroup')
                          ->where('scenario_id = ?', $this->scenarioId)
                          ->andWhere('scenario_facility_group', $facility_group)
                          ->fetchOne();
        }

        if (empty($facilityResource)) {
          $facilityResource = $this->createFacilityResource($facility, $facility_resource_type_id, $facility_resource_status_id, $capacity);
        } else {
          $facilityResource = $this->updateFacilityResource($facilityResource, $facility_resource_status, $capacity);
        }

        if (empty($scenarioFacilityGroup)) {
          $scenarioFacilityGroup = $this->createScenarioFacilityGroup($this->scenarioId, $facility_group, $facility_group_type_id, $facility_group_allocation_status_id, $facility_group_activation_sequence);
        } else {
          $scenarioFacilityGroup = $this->updateScenarioFacilityGroup($scenarioFacilityGroup, $facility_group_type_id, $facility_group_allocation_status_id, $facility_group_activation_sequence);
        }

        if (empty($scenarioFacilityResource)) {
          $scenarioFacilityResource = $this->createScenarioFacilityResource($facilityResource, $scenarioFacilityGroup, $facility_resource_allocation_status_id, $facility_activation_sequence);
        } else {
          $scenarioFacilityResource = $this->updateScenarioFacilityResource($scenarioFacilityResource, $facility_resource_allocation_status_id, $facility_activation_sequence);
        }

        $facility->save();
        $facilityResource->save();
        $scenarioFacilityGroup->save();
        $scenarioFacilityResource->save();
      } // end foreach

      $conn->commit();
    } catch (Exception $e) {
      echo '<BR><BR>Unable to normalize data.  Exception error mesage: ' . $e;
      $this->event[] = array('type' => 'ERROR', 'message' => 'Unable to normalize data.  Exception error mesage: ' . $e);
      $conn->rollBack();
    }
  }

  protected function createFacility($facilityName, $facilityCode) {
    $facility = NULL;

    $arrayFacility = array();
    $entity = new agEntity();
    $entity->save();
    $site = new agSite();
    $site->set('entity_id', $entity->id);
    $site->save();
    $facility = new agFacility();
    $facility->set('site_id', $site->id)
            ->set('facility_name', $facilityName)
            ->set('facility_code', $facilityCode);
    $facility->save();
    return $facility;
  }

  protected function updateFacility($facility, $facilityName) {
    return NULL;
  }

  protected function createFacilityResource($facility, $facilityResourceTypeAbbrId, $facilityResourceStatusId, $capacity) {
    $facilityResource = NULL;

    $facilityResource = new agFacilityResource();
    $facilityResource->set('facility_id', $facility->id)
            ->set('facility_resource_type_id', $facilityResourceTypeAbbrId)
            ->set('facility_resource_status_id', $facilityResourceStatusId)
            ->set('capacity', $capacity);
    $facilityResource->save();
    return $facilityResource;
  }

  protected function updateFacilityResource($facilityResource, $facilityResourceStatus, $capacity) {
    return $facilityResource;
  }

  protected function createScenarioFacilityGroup($scenarioId, $facilityGroup, $facilityGroupTypeId, $facilityGroupAllocationStatusId, $facilityGroupActivationSequence) {
    $scenarioFacilityGroup = NULL;

    $scenarioFacilityGroup = new agScenarioFacilityGroup();
    $scenarioFacilityGroup->set('scenario_id', $scenarioId)
            ->set('scenario_facility_group', $facilityGroup)
            ->set('facility_group_type_id', $facilityGroupTypeId)
            ->set('facility_group_allocation_status_id', $facilityGroupAllocationStatusId)
            ->set('activation_sequence', $facilityGroupActivationSequence);
    $scenarioFacilityGroup->save();
    return $scenarioFacilityGroup;
  }

  protected function updateScenarioFacilityGroup($scenarioFacilityGroup, $facilityGroupTypeId, $facilityGroupAllocationStatusId, $facilityGroupActivationSequence) {
    return $scenarioFacilityGroup;
  }

  protected function createScenarioFacilityResource($facilityResource, $scenarioFacilityGroup, $facilityResourceAllocationStatusId, $facilityActivationSequence) {
    $scenarioFacilityResource = NULL;

    $scenarioFacilityResource = new agScenarioFacilityResource();
    $scenarioFacilityResource->set('facility_resource_id', $facilityResource->id)
            ->set('scenario_facility_group_id', $scenarioFacilityGroup->id)
            ->set('facility_resource_allocation_status_id', $facilityResourceAllocationStatusId)
            ->set('activation_sequence', $facilityActivationSequence);
    $scenarioFacilityResource->save();
    return $scenarioFacilityResource;
  }

  protected function updateScenarioFacilityResource($scenarioFacilityResource, $facilityResourceAllocationStatusId, $facilityActivationSequence) {
    return $scenarioFacilityResource;
  }

}
