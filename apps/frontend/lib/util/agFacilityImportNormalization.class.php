<?php

/**
 * Normalizing facility import data.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agFacilityImportNormalization //extends agImportNormalization
{

  public $summary = array();
  private $importStaffList = array();
  private $staffMapping = array();
  private $errMsg;

  function __construct($scenarioId, $sourceTable, $dataType)
  {
    //TODO: uncomment when agImportNormalization is ready to be extended
    //parent::__construct($sourceTable);
    // declare variables
    $this->dataType = $dataType;
    $this->scenarioId = $scenarioId;
    $this->sourceTable = $sourceTable;
    $this->defineStatusTypes();
    $this->summary = array();
    $this->warningMessages = array();
    $this->nonprocessedRecords = array();
    $this->totalProcessedRecordCount = 0;
    $this->totalNewFacilityCount = 0;
    $this->totalNewFacilityGroupCount = 0;
    $this->processedFacilityIds = array();
    $this->conn = null;
    $this->errMsg;
  }

  function __destruct()
  {
//    //drop temp table.
//    $this->conn->export->dropTable($this->sourceTable);
//    $this->conn->close();
  }

  /**
   * Method to define status and type variables.
   */
  private function defineStatusTypes()
  {
    $this->facilityContactType = 'work';
    $this->defaultPhoneFormatTypes = array('USA 10 digit', 'USA 10 digit with an extension');
    $this->emailContactTypes = array_change_key_case(array_flip(agContactHelper::getContactTypes('email')), CASE_LOWER);
    $this->phoneContactTypes = array_change_key_case(array_flip(agContactHelper::getContactTypes('phone')), CASE_LOWER);
    $this->addressContactTypes = array_change_key_case(array_flip(agContactHelper::getContactTypes('address')), CASE_LOWER);
    $this->phoneFormatTypes = array_flip(agContactHelper::getPhoneFormatTypes($this->defaultPhoneFormatTypes));
    $this->staffResourceTypes = array_change_key_case(array_flip(agStaffHelper::getStaffResourceTypes(TRUE)), CASE_LOWER);
    $this->mapStaffColumn();
    $addressHelper = new agAddressHelper();
    $this->addressStandards = $addressHelper->getAddressStandardId();
    $this->addressElements = array_flip($addressHelper->getAddressAllowedElements());
    $this->geoType = 'point';
    $this->geoTypeId = agFacilityHelper::getGeoTypes($this->geoType);
    $this->defaultGeoMatchScore = 'good';
    $this->geoMatchScores = array_flip(agGisHelper::getGeoMatchScores());
    $geoMatchScores = $this->geoMatchScores;
    $this->geoMatchScoreId = $geoMatchScores[$this->defaultGeoMatchScore];
    $this->geoSourceParamVal = agGlobal::getParam('facility_import_geo_source');
    $this->geoSources = array_flip(agGisHelper::getGeoSources());
    $this->geoSourceId = $this->geoSources[$this->geoSourceParamVal];

    if ($this->dataType == 'facility') {
      $this->facilityResourceTypes = array_change_key_case(array_flip(agFacilityHelper::getFacilityResourceAbbrTypes()), CASE_LOWER);
      $this->facilityResourceStatuses = array_change_key_case(array_flip(agFacilityHelper::getFacilityResourceStatuses()), CASE_LOWER);
      $this->facilityResourceAllocationStatuses = array_change_key_case(array_flip(agFacilityHelper::getFacilityResourceAllocationStatuses()), CASE_LOWER);
      $this->facilityGroupTypes = array_change_key_case(array_flip(agFacilityHelper::getFacilityGroupTypes()), CASE_LOWER);
      $this->facilityGroupAllocationStatuses = array_change_key_case(array_flip(agFacilityHelper::getFacilityGroupAllocationStatuses()), CASE_LOWER);
    }
  }

  /**
   * Verify data in record for required fields and valid statuses and types
   *
   * @param array $record
   * @return boolean TRUE if data in record satisfies all requirements for processing.  FALSE otherwise.
   */
  private function dataValidation(array $record)
  {
    // Check for required fields
    if (empty($record['facility_name'])) {
      return array('pass' => FALSE,
        'status' => 'ERROR',
        'type' => 'Facility Name',
        'message' => 'Invalid facility name.');
    }

    // Check for valid facility_code
    if (empty($record['facility_code'])) {
      return array('pass' => FALSE,
        'status' => 'ERROR',
        'type' => 'Facility Code',
        'message' => 'Invalid facility code.');
    }

    $this->fullAddress = array('line 1' => $record['street_1'],
      'line 2' => $record['street_2'],
      'city' => $record['city'],
      'state' => $record['state'],
      'zip5' => $record['postal_code'],
      'borough' => $record['borough'],
      'country' => $record['country']);

    if (empty($record['street_1']) || empty($record['city']) ||
        empty($record['state']) || empty($record['postal_code'])) {
      return array('pass' => FALSE,
        'status' => 'ERROR',
        'type' => 'Mail Address',
        'message' => 'Invalid street 1/city/state/postal_code address.');
    }

    if (empty($record['longitude']) or empty($record['latitude'])) {
      return array('pass' => FALSE,
        'status' => 'ERROR',
        'type' => 'Geo',
        'message' => 'Invalid longitutde/latitude.');
    }

    // Check for min/max set validation:
    // (1) Check for valid staff type.
    // (2) Either both min and max are provided or neither should be provided for each staff type.
    // (3) max >= min.
    foreach ($this->importStaffList as $staff) {
      $staffMin = $staff . '_min';
      $staffMax = $staff . '_max';
      if (!array_key_exists($staff, $this->staffMapping)) {
        return array('pass' => FALSE,
          'status' => 'ERROR',
          'type' => 'Staff Resource',
          'message' => 'Invalid staff resource type.');
      }

      // Check if column min/max exists.
      if (!array_key_exists($staffMin, $record) || !array_key_exists($staffMax, $record)) {
        return array('pass' => FALSE,
          'status' => 'ERROR',
          'type' => 'Staff Resource',
          'message' => 'Invalid min/max set: missing column.');
      }

      // Check if a set value is provided.
      if (( empty($record[$staffMin]) && !empty($record[$staffMax]) )
          || (!empty($record[$staffMin]) && empty($record[$staffMax]) )) {
        return array('pass' => FALSE,
          'status' => 'ERROR',
          'type' => 'Staff Resource',
          'message' => 'Invalid min/max set: missing value.');
      }
      // Check if min <= max
      if ($record[$staffMin] > $record[$staffMax]) {
        return array('pass' => FALSE,
          'status' => 'ERROR',
          'type' => 'Staff Resource',
          'message' => 'Invalid min/max set: min > max.');
      }
    }

    // Check for valid email address
    if (!empty($record['work_email']) && !preg_match('/^.+\@.+\..+$/', $record['work_email'])) {
      return array('pass' => FALSE,
        'status' => 'WARNING',
        'type' => 'Email',
        'message' => 'Invalid email address.');
    }

    // Check for valid phone number where it's either 10 digit or
    // 10 digit follow by an 'x' and the extension.
    if (!empty($record['work_phone']) && !preg_match('/^((\([\d]{3}\) *[\d]{3} *-?[\d]{4})|(([\d]{3}(.|-)? *){2}[\d]{4})) *(x\d+)?$/', $record['work_phone'])) {
      return array('pass' => FALSE,
        'status' => 'WARNING',
        'type' => 'Phone',
        'message' => 'Invalid phone number.');
    }

    // Check for valid status and type.

    if (!array_key_exists(strtolower($record['facility_resource_type_abbr']), $this->facilityResourceTypes)) {
      return array('pass' => FALSE,
        'status' => 'ERROR',
        'type' => 'Facility Resource Type Abbr',
        'message' => 'Undefined facility resource type abbreviation.');
    }

    if (!array_key_exists(strtolower($record['facility_resource_status']), $this->facilityResourceStatuses)) {
      return array('pass' => FALSE,
        'status' => 'ERROR',
        'type' => 'Facility Resource status',
        'message' => 'Undefined facility resource status.');
    }

    if (!array_key_exists(strtolower($record['facility_allocation_status']), $this->facilityResourceAllocationStatuses)) {
      return array('pass' => FALSE,
        'status' => 'ERROR',
        'type' => 'Facility Resource Allocation Status',
        'message' => 'Undefined facility resource allocation status.');
    }

    if (!array_key_exists(strtolower($record['facility_group_type']), $this->facilityGroupTypes)) {
      return array('pass' => FALSE,
        'status' => 'ERROR',
        'type' => 'Facility Group Type',
        'message' => 'Undefined facility group type.');
    }

    if (!array_key_exists(strtolower($record['facility_group_allocation_status']), $this->facilityGroupAllocationStatuses)) {
      return array('pass' => FALSE,
        'status' => 'ERROR',
        'type' => 'Facility Group Allocation Status',
        'message' => 'Undefined facility group allocation status.');
    }

    return array('pass' => TRUE,
      'status' => 'SUCCESS',
      'type' => null,
      'message' => null);
  }

  /**
   * Method to identify all the staff requirement fields, suffixed with either a _min or _max string.
   *
   * @param array $columnHeaders  An array of column headers.
   */
  private function getImportStaffList($columnHeaders)
  {

    $setHeaders = preg_grep('/_(min|max)$/i', $columnHeaders);
    foreach ($setHeaders as $key => $column) {
      $this->importStaffList[] = rtrim(rtrim(strtolower($column), '_min'), '_max');
    }
    $this->importStaffList = array_values(array_unique($this->importStaffList));
  }

  /**
   * Method to replace a white space with underscore.
   *
   * @param string $name A string for replacement.
   * @return string $name A reformatted string.
   */
  private function stripName($name = NULL)
  {
    if (is_null($name) || !is_string($name)) {
      return $name;
    }

    $strippedName = strtolower(str_replace(' ', '_', $name));
    return $strippedName;
  }

  /**
   * Method to create a mapping of the staff resource types to their save column header formats.
   */
  private function mapStaffColumn()
  {
    foreach ($this->staffResourceTypes as $staff => $id) {
      $cleanName = $this->stripName($staff);
      $this->staffMapping[$cleanName] = $staff;
    }
  }

  /**
   * Method to dynamically generate staff requirements based off of the the pass in record.
   *
   * @param array $record An associative array of an entry from the import temp table.
   */
  private function dynamicStaffing($record)
  {
    $staffingRequirements = array();
    foreach ($this->importStaffList as $staff) {
      $staffId = $this->staffResourceTypes[$this->staffMapping[$staff]];
      $staffMin = $staff . '_min';
      $staffMax = $staff . '_max';
      $staffingRequirements[$staffId] = array('min' => $record[$staffMin],
        'max' => $record[$staffMax]);
    }
    return $staffingRequirements;
  }

  /**
   * Method to normalize data from temp table.
   */
  public function normalizeImport()
  {
    // Declare static variables.
    $facilityContactType = $this->facilityContactType;

    // Setup db connection.
    $conn = Doctrine_Manager::connection();

    // Fetch import data.
    $query = 'SELECT * FROM ' . $this->sourceTable . ' AS i';
    $pdo = $conn->execute($query);
    $pdo->setFetchMode(Doctrine_Core::FETCH_ASSOC);
    $sourceRecords = $pdo->fetchAll();

    // Grab the dynamical columns of staff requirements.
    if (count($sourceRecords) > 0) {
      $this->getImportStaffList(array_keys($sourceRecords[0]));
    }

    //loop through records.
    foreach ($sourceRecords as $record) {
      $validEmail = 1;
      $validPhone = 1;
      $validAddress = 1;
      $isNewFacilityRecord = 0;
      $isNewFacilityGroupRecord = 0;

      $isValidData = $this->dataValidation($record);
      if (!$isValidData['pass']) {
        switch ($isValidData['status']) {
          case 'ERROR':
            $this->nonprocessedRecords[] = array('message' => $isValidData['message'],
              'record' => $record);
            continue 2;
          case 'WARNING':
            switch ($isValidData['type']) {
              case 'Email':
                $validEmail = 0;
                $this->warningMessages[] = array('message' => $isValidData['message'],
                  'record' => $record);
                break;
              case 'Phone':
                $validPhone = 0;
                $this->warningMessages[] = array('message' => $isValidData['message'],
                  'record' => $record);
                break;
              case 'Mail Address':
                $validAddress = 0;
                $this->warningMessages[] = array('message' => $isValidData['message'],
                  'record' => $record);
                break;
            }
            break;
          default:
            $this->nonprocessedRecords[] = array('message' => $isValidData['message'],
              'record' => $record);
            continue 2;
        }
      }

      // Declare variables.
      $facility_name = $record['facility_name'];
      $facility_code = $record['facility_code'];
      $facility_resource_type_abbr = strtolower($record['facility_resource_type_abbr']);
      $facility_resource_status = strtolower($record['facility_resource_status']);
      $capacity = $record['facility_capacity'];
      $facility_activation_sequence = $record['facility_activation_sequence'];
      $facility_allocation_status = strtolower($record['facility_allocation_status']);
      $facility_group = $record['facility_group'];
      $facility_group_type = strtolower($record['facility_group_type']);
      $facility_group_allocation_status = strtolower($record['facility_group_allocation_status']);
      $facility_group_activation_sequence = $record['facility_group_activation_sequence'];
      $email = $record['work_email'];
      $phone = $record['work_phone'];
      $fullAddress = $this->fullAddress;
      $geoInfo = array('longitude' => $record['longitude'], 'latitude' => $record['latitude']);
      $staffing = $this->dynamicStaffing($record);

      $facility_resource_type_id = $this->facilityResourceTypes[$facility_resource_type_abbr];
      $facility_resource_status_id = $this->facilityResourceStatuses[$facility_resource_status];
      $facility_group_type_id = $this->facilityGroupTypes[$facility_group_type];
      $facility_group_allocation_status_id = $this->facilityGroupAllocationStatuses[$facility_group_allocation_status];
      $facility_resource_allocation_status_id = $this->facilityResourceAllocationStatuses[$facility_allocation_status];
      $workEmailTypeId = $this->emailContactTypes[$facilityContactType];
      $workPhoneTypeId = $this->phoneContactTypes[$facilityContactType];
      $defaultPhoneFormatTypes = $this->defaultPhoneFormatTypes;
      $workPhoneFormatId = $this->phoneFormatTypes[$defaultPhoneFormatTypes[(preg_match('/^\d{10}$/', $phone) ? 0 : 1)]];
      $workAddressTypeId = $this->addressContactTypes[$facilityContactType];
      $workAddressStandardId = $this->addressStandards;
      $addressElementIds = $this->addressElements;

      try {
        // here we check our current transaction scope and create a transaction or savepoint based on need
        $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
        if ($useSavepoint) {
          $conn->beginTransaction(__FUNCTION__);
        } else {
          $conn->beginTransaction();
        }

        // facility
        // tries to find an existing record based on a unique identifier.
        $facility = agDoctrineQuery::create($conn)
                ->from('agFacility f')
                ->where('f.facility_code = ?', $facility_code)
                ->fetchOne();
        $facilityResource = NULL;
        $scenarioFacilityResource = NULL;

        if (empty($facility)) {
          $facility = $this->createFacility($facility_name, $facility_code, $conn);
          $isNewFacilityRecord = 1;
        } else {
          $facility = $this->updateFacility($facility, $facility_name, $conn);

          // tries to find an existing record based on a set of unique identifiers.
          $facilityResource = agDoctrineQuery::create($conn)
                  ->from('agFacilityResource fr')
                  ->where('fr.facility_id = ?', $facility->id)
                  ->andWhere('fr.facility_resource_type_id = ?', $facility_resource_type_id)
                  ->fetchOne();
        }

        // Facility Resource
        if (empty($facilityResource)) {
          $facilityResource = $this->createFacilityResource($facility, $facility_resource_type_id,
                  $facility_resource_status_id, $capacity, $conn);
        } else {
          $facilityResource = $this->updateFacilityResource($facilityResource,
                  $facility_resource_status_id, $capacity, $conn);

          $scenarioFacilityResource = agDoctrineQuery::create($conn)
                  ->from('agScenarioFacilityResource sfr')
                  ->innerJoin('sfr.agScenarioFacilityGroup sfg')
                  ->where('sfg.scenario_id = ?', $this->scenarioId)
                  ->andWhere('facility_resource_id = ?', $facilityResource->id)
                  ->fetchOne();
        }

        // facility group

        $scenarioFacilityGroup = agDoctrineQuery::create()
                ->from('agScenarioFacilityGroup')
                ->where('scenario_id = ?', $this->scenarioId)
                ->andWhere('scenario_facility_group = ?', $facility_group)
                ->fetchOne();

        if (empty($scenarioFacilityGroup)) {
          $scenarioFacilityGroup = $this->createScenarioFacilityGroup($facility_group,
                  $facility_group_type_id,
                  $facility_group_allocation_status_id,
                  $facility_group_activation_sequence,
                  $conn);
          $isNewFacilityGroupRecord = 1;
        } else {
          $scenarioFacilityGroup = $this->updateScenarioFacilityGroup($scenarioFacilityGroup,
                  $facility_group_type_id,
                  $facility_group_allocation_status_id,
                  $facility_group_activation_sequence,
                  $conn);
        }

        // facility resource
        if (empty($scenarioFacilityResource)) {
          $scenarioFacilityResource = $this->createScenarioFacilityResource($facilityResource,
                  $scenarioFacilityGroup,
                  $facility_resource_allocation_status_id,
                  $facility_activation_sequence,
                  $conn);
        } else {
          $scenarioFacilityResource = $this->updateScenarioFacilityResource($scenarioFacilityResource,
                  $scenarioFacilityGroup->id,
                  $facility_resource_allocation_status_id,
                  $facility_activation_sequence,
                  $conn);
        }

        //facility staff resource
        $this->updateFacilityStaffResources($scenarioFacilityResource->getId(), $staffing, $conn);

        // email
        if ($validEmail) {
          $this->updateFacilityEmail($facility, $email, $workEmailTypeId, $conn);
        }

        // phone
        if ($validPhone) {
          $this->updateFacilityPhone($facility, $phone, $workPhoneTypeId,
              $workPhoneFormatId, $conn);
        }

        // address
        if ($validAddress) {
          $addressId = $this->updateFacilityAddress($facility, $fullAddress,
                  $workAddressTypeId,
                  $workAddressStandardId,
                  $addressElementIds, $conn);
          $this->updateFacilityGeo($facility, $addressId, $workAddressTypeId,
              $workAddressStandardId, $geoInfo, $conn);
        }


// Update script to reflect table modification with a required geo_hash field.
//        $this->updateFacilityGeo($facility, $addressId, $workAddressTypeId,
//                                 $workAddressStandardId, $geoInfo, $conn);
        // Set summary counts
        if ($isNewFacilityRecord) {
          $this->totalNewFacilityCount++;
        }
        if ($isNewFacilityGroupRecord) {
          $this->totalNewFacilityGroupCount++;
        }
        $this->totalProcessedRecordCount++;

        $facilityId = $facility->id;
        if (!is_integer(array_search($facilityId, $this->processedFacilityIds))) {
          array_push($this->processedFacilityIds, $facilityId);
        }

        if ($useSavepoint) {
          $conn->commit(__FUNCTION__);
        } else {
          $conn->commit();
        }
      } catch (Exception $e) {
        $this->errMsg .= '  Unable to normalize data.  Exception error message: ' . $e->getMessage();
        $this->nonprocessedRecords[] = array('message' => $this->errMsg,
          'record' => $record);
        sfContext::getInstance()->getLogger()->err($errMsg);

        // if we started with a savepoint, let's end with one, otherwise, rollback globally
        if ($useSavepoint) {
          $conn->rollback(__FUNCTION__);
        } else {
          $conn->rollback();
        }

        break;
      }
    } // end foreach
    //drop temp table.
    $conn->export->dropTable($this->sourceTable);
    $conn->close();


    $this->summary = array('totalProcessedRecordCount' => $this->totalProcessedRecordCount,
      'TotalFacilityProcessed' => count($this->processedFacilityIds),
      'totalNewFacilityCount' => $this->totalNewFacilityCount,
      'totalNewFacilityGroupCount' => $this->totalNewFacilityGroupCount,
      'nonprocessedRecords' => $this->nonprocessedRecords,
      'warningMessages' => $this->warningMessages);

    // sets import state as done
    // TODO: fire an event instead
//    agImportHelper::setImportState('facility', agImportHelper::IMPORT_FINISHED,
//        array(count($this->processedFacilityIds), $this->totalProcessedRecordCount));
  }

  /* Facility */

  protected function createFacility($facilityName, $facilityCode, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $entity = new agEntity();
      $entity->save($conn);
      $site = new agSite();
      $site->set('entity_id', $entity->id);
      $site->save($conn);
      $facility = new agFacility(null, true, false);
      $facility->set('site_id', $site->id)
          ->set('facility_name', $facilityName)
          ->set('facility_code', $facilityCode);
      $facility->save($conn);

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t insert facility  with facility code %s!
                               Rolled back changes!', $facilityCode);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }

    return $facility;
  }

  protected function updateFacility($facility, $facilityName, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      if ($facility->facility_name != $facilityName) {
        $updateQuery = Doctrine_Query::create($conn)
                ->update('agFacility f')
                ->set('f.facility_name', '?', $facilityName)
                ->where('f.id = ?', $facility->id)
                ->execute();
      }

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t update facility with facility code %s!
                               Rolled back changes!', $facility->facility_code);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $facility;
  }

  /* Facility Resource */

  protected function createFacilityResource($facility, $facilityResourceTypeAbbrId,
                                            $facilityResourceStatusId, $capacity, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $facilityResource = new agFacilityResource();
      $facilityResource->set('facility_id', $facility->id)
          ->set('facility_resource_type_id', $facilityResourceTypeAbbrId)
          ->set('facility_resource_status_id', $facilityResourceStatusId)
          ->set('capacity', $capacity);
      $facilityResource->save($conn);

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t insert facility resource (%s, %s)!
                              Rolled back changes!', $facility->facility_code,
              array_search($facilityResourceTypeAbbrId,
                  $this->facilityResourceTypes));

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $facilityResource;
  }

  protected function updateFacilityResource($facilityResource, $facilityResourceStatusId, $capacity,
                                            $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $doUpdate = FALSE;
      $updateQuery = agDoctrineQuery::create($conn)
              ->update('agFacilityResource fr')
              ->where('id = ?', $facilityResource->id);

      if ($facilityResource->facility_resource_status_id != $facilityResourceStatusId) {
        $updateQuery->set('facility_resource_status_id', '?', $facilityResourceStatusId);
        $doUpdate = TRUE;
      }

      if ($facilityResource->capacity != $capacity) {
        $updateQuery->set('capacity', '?', $capacity);
        $doUpdate = TRUE;
      }

      if ($doUpdate) {
        $updateQuery = $updateQuery->execute();
      } else {
        $updateQuery = NULL;
      }

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t update facility resource (%s, %s)!
                               Rolled back changes!',
              $facilityResource->getAgFacility()->facility_code,
              array_search($facilityResource->facility_resource_type_id,
                  $this->facilityResourceTypes));

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $facilityResource;
  }

  /* Scenario Facility Group */

  protected function createScenarioFacilityGroup($facilityGroup, $facilityGroupTypeId,
                                                 $facilityGroupAllocationStatusId,
                                                 $facilityGroupActivationSequence, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $scenarioFacilityGroup = new agScenarioFacilityGroup();
      $scenarioFacilityGroup->set('scenario_id', $this->scenarioId)
          ->set('scenario_facility_group', $facilityGroup)
          ->set('facility_group_type_id', $facilityGroupTypeId)
          ->set('facility_group_allocation_status_id', $facilityGroupAllocationStatusId)
          ->set('activation_sequence', $facilityGroupActivationSequence);
      $scenarioFacilityGroup->save($conn);

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t insert facility group %s! Rolled back changes!',
              $facilityGroup);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }

    return $scenarioFacilityGroup;
  }

  protected function updateScenarioFacilityGroup($scenarioFacilityGroup, $facilityGroupTypeId,
                                                 $facilityGroupAllocationStatusId,
                                                 $facilityGroupActivationSequence, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $doUpdate = FALSE;
      $updateQuery = agDoctrineQuery::create($conn)
              ->update('agScenarioFacilityGroup sfg')
              ->where('id = ?', $scenarioFacilityGroup->id);

      if (strtolower($scenarioFacilityGroup->facility_group_type_id) != strtolower($facilityGroupTypeId)) {
        $updateQuery->set('facility_group_type_id', '?', $facilityGroupTypeId);
        $doUpdate = TRUE;
      }

      if ($scenarioFacilityGroup->facility_group_allocation_status_id != $facilityGroupAllocationStatusId) {
        $updateQuery->set('facility_group_allocation_status_id', '?', $facilityGroupAllocationStatusId);
        $doUpdate = TRUE;
      }

      if ($scenarioFacilityGroup->activation_sequence != $facilityGroupActivationSequence) {
        $updateQuery->set('activation_sequence', '?', $facilityGroupActivationSequence);
        $doUpdate = TRUE;
      }

      if ($doUpdate) {
        $updateQuery = $updateQuery->execute();
      } else {
        $updateQuery = NULL;
      }

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t update facility group %s!
                               Rolled back changes!',
              $scenarioFacilityGroup->scenario_facility_group);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $scenarioFacilityGroup;
  }

  /* Scenario Facility Resource */

  protected function createScenarioFacilityResource($facilityResource, $scenarioFacilityGroup,
                                                    $facilityResourceAllocationStatusId,
                                                    $facilityActivationSequence, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $scenarioFacilityResource = new agScenarioFacilityResource();
      $scenarioFacilityResource->set('facility_resource_id', $facilityResource->id)
          ->set('scenario_facility_group_id', $scenarioFacilityGroup->id)
          ->set('facility_resource_allocation_status_id', $facilityResourceAllocationStatusId)
          ->set('activation_sequence', $facilityActivationSequence);
      $scenarioFacilityResource->save($conn);

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t insert scenario facility resource
                               (%s, %s, %s)! Rolled back changes!',
              $facilityResource->getAgFacility()->facility_name,
              $facilityResource->getAgFacilityResourceType()->facility_resource_type_abbr,
              $scenarioFacilityGroup->scenario_facility_group);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $scenarioFacilityResource;
  }

  protected function updateScenarioFacilityResource($scenarioFacilityResource,
                                                    $scenarioFacilityGroupId,
                                                    $facilityResourceAllocationStatusId,
                                                    $facilityActivationSequence, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $doUpdate = FALSE;
      $updateQuery = agDoctrineQuery::create($conn)
              ->update('agScenarioFacilityResource')
              ->where('id = ?', $scenarioFacilityResource->id);

      if ($scenarioFacilityResource->scenario_facility_group_id != $scenarioFacilityGroupId) {
        $updateQuery->set('scenario_facility_group_id', $scenarioFacilityGroupId);
        $doUpdate = TRUE;
      }

      if ($scenarioFacilityResource->facility_resource_allocation_status_id != $facilityResourceAllocationStatusId) {
        $updateQuery->set('facility_resource_allocation_status_id', $facilityResourceAllocationStatusId);
        $doUpdate = TRUE;
      }

      if ($scenarioFacilityResource->activation_sequence != $facilityActivationSequence) {
        $updateQuery->set('activation_sequence', $facilityActivationSequence);
        $doUpdate = TRUE;
      }

      if ($doUpdate) {
        $updateQuery = $updateQuery->execute();
      } else {
        $updateQuery = NULL;
      }

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t update scenario facility resource
                               (%s, %s, %s)! Rolled back changes!',
              $scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name,
              $scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type_abbr,
              Doctrine_Core::getTable('agScenarioFacilityGroup')->find($scenarioFacilityGroupId)->scenario_facility_group);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $scenarioFacilityResource;
  }

  protected function updateFacilityStaffResources($scenarioFacilityResourceId, $staff_data,
                                                  $conn = NULL)
  {
    $facilityStaffResource = agDoctrineQuery::create()
            ->select('srt.id, fsr.minimum_staff, fsr.maximum_staff, fsr.id')
            ->from('agStaffResourceType srt')
            ->innerJoin('srt.agFacilityStaffResource fsr')
            ->where('fsr.scenario_facility_resource_id = ?', $scenarioFacilityResourceId)
            ->execute(array(), 'key_value_array');

    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $deleteFacStfResId = array();
      foreach ($staff_data as $staffTypeId => $count) {
        if (array_key_exists($staffTypeId, $facilityStaffResource)) {
          $doUpdate = FALSE;
          $updateQuery = agDoctrineQuery::create($conn)
                  ->update('agFacilityStaffResource')
                  ->where('id = ?', $facilityStaffResource[$staffTypeId][2]);

          if ($count['min'] == 0 && $count['max'] == 0) {
            $deleteFacStfResId[] = $facilityStaffResource[$staffTypeId][2];
            continue;
          }

          if ($facilityStaffResource[$staffTypeId][0] != $count['min']) {
            $updateQuery->set('minimum_staff', $count['min']);
            $doUpdate = TRUE;
          }

          if ($facilityStaffResource[$staffTypeId][1] != $count['max']) {
            $updateQuery->set('maximum_staff', $count['max']);
            $doUpdate = TRUE;
          }

          if ($doUpdate) {
            $updateQuery->execute();
          } else {
            $updateQuery = NULL;
          }
        } else {
          if ($count['min'] != 0 && $count['max'] != 0) {
            $this->createFacilityStaffResource($scenarioFacilityResourceId, $staffTypeId, $count['min'], $count['max'], $conn);
          }
        }
      }

      if (!empty($deleteFacStfResId)) {
        $deleteQuery = agDoctrineQuery::create($conn)
                ->delete('agFacilityStaffResource')
                ->whereIn('id', $deleteFacStfResId)
                ->execute();
      }

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg .= sprintf('  Couldn\'t update facility staff resource!
                                Rolled back changes!');

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $facilityStaffResource;
  }

  protected function createFacilityStaffResource($scenarioFacilityResourceId, $staffTypeId,
                                                 $min_staff, $max_staff, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $facilityStaffResource = new agFacilityStaffResource();
      $facilityStaffResource->set('staff_resource_type_id', $staffTypeId)
          ->set('scenario_facility_resource_id', $scenarioFacilityResourceId)
          ->set('minimum_staff', $min_staff)
          ->set('maximum_staff', $max_staff);
      $facilityStaffResource->save($conn);

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      $scenarioFacilityResource = Doctrine_Core::getTable('agScenarioFacilityResource')
              ->find($scenarioFacilityResourceId);
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t create facility staff resource (%s, %s)!',
              $scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name,
              array_search($staffTypeId, $this->staffResourceTypes));

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $facilityStaffResource;
  }

  /* EMAIL */

  protected function getEmailObject($email)
  {
    $emailContact = agDoctrineQuery::create()
            ->from('agEmailContact')
            ->where('email_contact = ?', $email)
            ->fetchOne();
    return $emailContact;
  }

  protected function createEmail($email, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $emailContact = new agEmailContact();
      $emailContact->set('email_contact', $email);
      $emailContact->save($conn);

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t create email %s! Rolled back changes!', $email);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $emailContact;
  }

  protected function createEntityEmail($entityId, $emailId, $typeId, $conn = NULl)
  {
    $priority = $this->getPriorityCounter('email', $entityId);

    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $entityEmail = new agEntityEmailContact();
      $entityEmail->set('entity_id', $entityId)
          ->set('email_contact_id', $emailId)
          ->set('email_contact_type_id', $typeId)
          ->set('priority', $priority);
      $entityEmail->save($conn);

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t create entity email (%s, %s)! Rolled back changes!',
              array_search($typeId, $this->emailContactTypes),
              Doctrine_Core::getTable('agEmailContact')->find($emailId)->email_contact);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $entityEmail;
  }

  protected function updateEntityEmail($entityEmailObject, $emailObject, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $entityEmailObject->set('email_contact_id', $emailObject->id);
      $entityEmailObject->save($conn);

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t update entity email (%s, %s)! Rolled back changes!',
              $entityEmailObject->getAgEmailContactType()->email_contact_type,
              $emailObject->email_contact);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $entityEmailObject;
  }

  /**
   *
   * @param <type> $facility
   * @param <type> $email
   * @param <type> $workEmailTypeId
   * @return bool true if an update or create happened, false otherwise.
   */
  protected function updateFacilityEmail($facility, $email, $workEmailTypeId, $conn)
  {
    $entityId = $facility->getAgSite()->entity_id;
    $entityEmail = $this->getEntityContactObject('email', $entityId, $workEmailTypeId);

    if (empty($entityEmail)) {
      $facilityEmail = '';
    } else {
      $facilityEmail = $entityEmail->getAgEmailContact()->email_contact;
    }

    //  if oldEmail is importedEmail
    if (strtolower($email) == strtolower($facilityEmail)) {
      return FALSE;
    }

    // only useful for nonrequired emails
    //  if importedEmail null
    if (empty($email) && !empty($facilityEmail)) {
      // here we check our current transaction scope and create a transaction
      // or savepoint based on need
      $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
      if ($useSavepoint) {
        $conn->beginTransaction(__FUNCTION__);
      } else {
        $conn->beginTransaction();
      }

      try {
        $entityEmail->delete($conn);
        if ($useSavepoint) {
          $conn->commit(__FUNCTION__);
        } else {
          $conn->commit();
        }
        return TRUE;
      } catch (Exception $e) {
        // ALWAYS log rollbacks with as much useful information as possible
        $this->errMsg = sprintf('Couldn\'t remove old entity email (%s, %s)!
                                 Rolled back changes!',
                array_search($workEmailTypeId,
                    $this->emailContactTypes),
                $facilityEmail);

        // if we started with a savepoint, let's end with one,
        // otherwise, rollback globally
        if ($useSavepoint) {
          $conn->rollback(__FUNCTION__);
        } else {
          $conn->rollback();
        }

        throw $e; // always remember to throw an exception after rollback
      }
      return TRUE;
    }

    $emailEntity = $this->getEmailObject($email);

    if (empty($emailEntity)) {
      $emailEntity = $this->createEmail($email, $conn);
    }

    if (empty($facilityEmail)) {
      $this->createEntityEmail($entityId, $emailEntity->id, $workEmailTypeId, $conn);
      return TRUE;
    }

    // Facility email exists and does not match import email.
    $this->updateEntityEmail($entityEmail, $emailEntity, $conn);
    return TRUE;
  }

  /* PHONE */

  protected function getPhoneObject($phone)
  {
    $phoneContact = NULL;
    $phoneContact = agDoctrineQuery::create()
            ->from('agPhoneContact')
            ->where('phone_contact = ?', $phone)
            ->fetchOne();
    return $phoneContact;
  }

  protected function createPhone($phone, $phoneFormatId, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $phoneContact = new agPhoneContact();
      $phoneContact
          ->set('phone_contact', $phone)
          ->set('phone_format_id', $phoneFormatId);
      $phoneContact->save($conn);

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t insert phone %s! Rolled back changes!', $phone);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }

    return $phoneContact;
  }

  protected function createEntityPhone($entityId, $phoneContactId, $typeId, $conn = NULL)
  {
    $priority = $this->getPriorityCounter('phone', $entityId);

    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $entityPhone = new agEntityPhoneContact();
      $entityPhone->set('entity_id', $entityId)
          ->set('phone_contact_id', $phoneContactId)
          ->set('phone_contact_type_id', $typeId)
          ->set('priority', $priority);
      $entityPhone->save($conn);

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t create entity phone (%s, %s)! Rolled back changes!',
              array_search($typeId, $this->phoneContactTypes),
              Doctrine_Core::getTable('agPhoneContact')->find($phoneContactId)->phone_contact);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $entityPhone;
  }

  protected function updateEntityPhone($entityPhoneObject, $phoneObject, $conn = NULL)
  {
    // here you can pick up the default connection if not passed one explicitly
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }

    // here we check our current transaction scope and create a transaction or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $entityPhoneObject->set('phone_contact_id', $phoneObject->id);
      $entityPhoneObject->save($conn);

      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t update entity email (%s, %s)! Rolled back changes!',
              $entityEmailObject->getAgEmailContactType()->email_contact_type,
              $emailObject->email_contact);

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $entityPhone;
  }

  /**
   *
   * @param <type> $facility
   * @param <type> $phone
   * @param <type> $workEmailTypeId
   * @return bool true if an update or create happened, false otherwise.
   */
  protected function updateFacilityPhone($facility, $phone, $workPhoneTypeId, $phoneFormatId,
                                         $conn = NULL)
  {
    //TODO
    $entityId = $facility->getAgSite()->entity_id;
    $entityPhone = $this->getEntityContactObject('phone', $entityId, $workPhoneTypeId);

    if (empty($entityPhone)) {
      $facilityPhone = '';
    } else {
      $facilityPhone = $entityPhone->getAgPhoneContact()->phone_contact;
    }

    //  if oldEmail is importedEmail
    if (strtolower($phone) == strtolower($facilityPhone)) {
      return FALSE;
    }

    // only useful for nonrequired phones
    //  if importedEmail null
    if (empty($phone)) {
      // here we check our current transaction scope and create a transaction
      // or savepoint based on need
      $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
      if ($useSavepoint) {
        $conn->beginTransaction(__FUNCTION__);
      } else {
        $conn->beginTransaction();
      }

      try {
        $entityPhone->delete($conn);
        if ($useSavepoint) {
          $conn->commit(__FUNCTION__);
        } else {
          $conn->commit();
        }
      } catch (Exception $e) {
        // ALWAYS log rollbacks with as much useful information as possible
        $this->errMsg = sprintf('Couldn\'t remove old entity phone (%s, %s)! Rolled back changes!',
                array_search($workPhoneTypeId, $this->phoneContactTypes),
                $facilityPhone);

        // if we started with a savepoint, let's end with one,
        // otherwise, rollback globally
        if ($useSavepoint) {
          $conn->rollback(__FUNCTION__);
        } else {
          $conn->rollback();
        }

        throw $e; // always remember to throw an exception after rollback
      }
      return TRUE;
    }

    $phoneEntity = $this->getPhoneObject($phone);

    if (empty($phoneEntity)) {
      $phoneEntity = $this->createPhone($phone, $phoneFormatId, $conn);
    }

    if (empty($facilityPhone)) {
      $this->createEntityPhone($entityId, $phoneEntity->id, $workPhoneTypeId, $conn);
      return TRUE;
    }

    // Facility phone exists and does not match import phone
    $this->updateEntityPhone($entityPhone, $phoneEntity, $conn);
    return TRUE;
  }

  /* Address */

  protected function getAssociateAddressElementValues($addressId, $addressElementIds)
  {
    $entityAddressElementValues = agDoctrineQuery::create()
            ->select('ae.address_element, av.value')
            ->from('agAddressElement ae')
            ->innerJoin('ae.agAddressValue av')
            ->innerJoin('av.agAddressMjAgAddressValue aav')
            ->where('aav.address_id = ?', $addressId)
            ->andWhereIn('ae.id', array_values($addressElementIds));
    $querySql = $entityAddressElementValues->getSqlQuery();
    $entityAddressElementValues = $entityAddressElementValues->execute(array(), 'key_value_pair');
    return $entityAddressElementValues;
  }

  private function isEmptyStringArray($vals)
  {
    if (empty($vals)) {
      return FALSE;
    }

    foreach ($vals as $val) {
      if (strlen($val) != 0) {
        return FALSE;
      }
    }
    return TRUE;
  }

  protected function createAddressValues($fullAddress, $workAddressStandardId, $addressElementIds,
                                         $conn = NULL)
  {
    $newAddressValueIds = array();
    foreach ($fullAddress as $elem => $elemVal) {
      if (empty($elemVal)) {
        continue;
      }
      $addressValue = agDoctrineQuery::create()
              ->from('agAddressValue a')
              ->innerJoin('a.agAddressElement ae')
              ->where('a.value = ?', $elemVal)
              ->andWhere('ae.address_element = ?', $elem)
              ->fetchOne();
      if (empty($addressValue)) {
        // here we check our current transaction scope and create a transaction
        // or savepoint based on need
        $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
        if ($useSavepoint) {
          $conn->beginTransaction(__FUNCTION__);
        } else {
          $conn->beginTransaction();
        }

        try {
          $newAddressValue = new agAddressValue();
          $newAddressValue->set('value', $elemVal)
              ->set('address_element_id', $addressElementIds[$elem]);
          $newAddressValue->save($conn);
          $newAddressValueIds[] = $newAddressValue->id;
          if ($useSavepoint) {
            $conn->commit(__FUNCTION__);
          } else {
            $conn->commit();
          }
        } catch (Exception $e) {
          // ALWAYS log rollbacks with as much useful information as possible
          $this->errMsg = sprintf('Couldn\'t create address element value %s!
                                   Rolled back changes!', $elemVal);

          // if we started with a savepoint, let's end with one,
          // otherwise, rollback globally
          if ($useSavepoint) {
            $conn->rollback(__FUNCTION__);
          } else {
            $conn->rollback();
          }

          throw $e; // always remember to throw an exception after rollback
        }
      } else {
        $newAddressValueIds[] = $addressValue->id;
      }
    }
    return $newAddressValueIds;
  }

  protected function createAddress($addressStandardId, $conn)
  {
    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $address = new agAddress();
      $address->set('address_standard_id', $addressStandardId);
      $address->save($conn);
      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t create address! Rolled back changes!');

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $address;
  }

  protected function createEntityAddress($entityId, $addressTypeId, $fullAddress,
                                         $addressStandardId, $addressElementIds, $conn = NULL)
  {
    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      // create new address with importedElements
      $address = $this->createAddress($addressStandardId, $conn);

      $addressId = $address->id;

      $newAddressValueIds = $this->createAddressValues($fullAddress, $addressStandardId, $addressElementIds, $conn);

      foreach ($newAddressValueIds as $addressValueId) {
        $mappingAddress = new agAddressMjAgAddressValue();
        $mappingAddress->set('address_id', $addressId)
            ->set('address_value_id', $addressValueId);
        $mappingAddress->save($conn);
      }

      $priority = $this->getPriorityCounter('address', $entityId);

      $entityAddress = new agEntityAddressContact();
      $entityAddress->set('entity_id', $entityId)
          ->set('address_id', $addressId)
          ->set('priority', $priority)
          ->set('address_contact_type_id', $addressTypeId);
      $entityAddress->save($conn);
      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t create entity address! Rolled back changes!');

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
    return $addressId;
  }

  protected function updateFacilityAddress($facility, $importedAddress, $workAddressTypeId,
                                           $workAddressStandardId, $addressElementIds, $conn = NULL)
  {
    $entityId = $facility->getAgSite()->entity_id;
    $isImportAddressEmpty = $this->isEmptyStringArray($importedAddress);
    $facilityAddress = $this->getEntityContactObject('address', $entityId, $workAddressTypeId);

    // Remove existing facility work address if import address is null.
    if ((!empty($facilityAddress)) && $isImportAddressEmpty) {
      $this->deleteEntityAddressMapping($facilityAddress->id, $conn);
      return NULL;
    }

    // Create new facility address with import address where facility does not have an address and
    // an address is given in import.
    $isCreateNew = FALSE;
    if (empty($facilityAddress) && !$isImportAddressEmpty) {
      $isCreateNew = TRUE;
    }

    // Compares existing facility address with imported address.
    if (!empty($facilityAddress)) {
      $addressId = $facilityAddress->address_id;
      $facilityAddressElements = $this->getAssociateAddressElementValues($addressId, $addressElementIds);

      // if the addresses are different in any way
      if (
          $facilityAddress->getAgAddress()->address_hash !=
          agBulkRecordHelper::getRecordComponentsHash($importedAddress)
      ) {
        // removes mappings between the different elements
        foreach ($facilityAddressElements as $eltName => $eltVal) {
          if (
              array_key_exists($eltName, $importedAddress) &&
              (strtolower($eltVal) != strtolower($importedAddress[$eltName]))
          ) {
            $this->deleteEntityAddressMapping($facilityAddress->id, $conn);
            $isCreateNew = TRUE;
            break;
          }
        }
      } else {
        $isCreateNew = TRUE;
      }
    }

    if ($isCreateNew) {
      $addressId = $this->createEntityAddress($entityId, $workAddressTypeId, $importedAddress, $workAddressStandardId, $addressElementIds, $conn);
    }

    return $addressId;
  }

  protected function deleteEntityAddressMapping($entityAddressId, $conn = NULL)
  {
    // here we check our current transaction scope and create a transaction
    // or savepoint based on need
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      $query = agDoctrineQuery::create($conn)
              ->delete('agEntityAddressContact')
              ->where('id = ?', $entityAddressId)
              ->execute();
      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // ALWAYS log rollbacks with as much useful information as possible
      $this->errMsg = sprintf('Couldn\'t remove old address! Rolled back changes!');

      // if we started with a savepoint, let's end with one,
      // otherwise, rollback globally
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }

      throw $e; // always remember to throw an exception after rollback
    }
  }

  /* Geo */

  protected function updateFacilityGeo($facility, $addressId, $addressTypeId, $addressStandardId,
                                       $geoInfo, $conn = NULL)
  {
    // Create an address container to assign geo info for facility with no address given in import.
    if (empty($addressId)) {
      $entityId = $facility->getAgSite()->entity_id;
      $addressId = $this->createEntityAddress($entityId, $addressTypeId, array(), $addressStandardId, array(), $conn);
    }

    // Form array to process geo record.
    $latitude = $geoInfo['latitude'];
    $longitude = $geoInfo['longitude'];
    $addrCoord = array($addressId => array(array(array($latitude, $longitude)), $this->geoMatchScoreId));

    $geoHelper = new AgGeoHelper();
    $count = $geoHelper->setAddressGeo($addrCoord, $this->geoSourceId);

    return ($count == 1) ? TRUE : FALSE;
  }

  /* ENTITY */

  protected function getEntityContactObject($contactMedium, $entityId, $contactTypeId)
  {
    $entityContactObject = NULL;

    switch (strtolower($contactMedium)) {
      case 'phone':
        $table = 'agEntityPhoneContact';
        $contactType = 'phone_contact_type_id';
        break;
      case 'email':
        $table = 'agEntityEmailContact';
        $contactType = 'email_contact_type_id';
        break;
      case 'address':
        $table = 'agEntityAddressContact';
        $contactType = 'address_contact_type_id';
        break;
      default:
        return $entityContactObject;
    }

    $entityContactObject = agDoctrineQuery::create()
            ->from($table)
            ->where('entity_id = ?', $entityId)
            ->andWhere($contactType . ' = ?', $contactTypeId)
            ->orderBy('priority')
            ->fetchOne();

    return $entityContactObject;
  }

  protected function getPriorityCounter($contactMedium, $entityId)
  {
    $priority = NULL;

    switch (strtolower($contactMedium)) {
      case 'phone':
        $table = 'agEntityPhoneContact';
        break;
      case 'email':
        $table = 'agEntityEmailContact';
        break;
      case 'address':
        $table = 'agEntityAddressContact';
        break;
      default:
        return $priority;
    }

    $currentPriority = agDoctrineQuery::create()
            ->select('max(priority)')
            ->from($table)
            ->where('entity_id = ?', $entityId)
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    if (empty($currentPriority)) {
      $priority = 1;
    } else {
      $priority = (int) $currentPriority + 1;
    }

    return $priority;
  }

}
