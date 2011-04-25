<?php

/**
 * Normalizing import data.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
abstract class agImportNormalization extends agImportHelper
{
  public    $summary = array(),
            $batchSize;
  
  protected $newEntityCount = 0,
            $errMsg,
            $warningMessages = array(),
            $nonprocessedRecords = array(),
            $totalProcessedRecordCount = 0,
            $helperObjects = array(),
            $defaultBatchSize,
            $tempOffset,

            // array( [order] => array(componentName => component name, helperName => Name of the helper object, throwOnError => boolean, methodName => method name) )
            $importComponents = array(),

           //array( [importRowId] => array( _rawData => array(fetched data), primaryKeys => array(keyName => keyValue), success => boolean) 
            $importData = array();

  public function __construct()
  {
    parent::__construct();
    $this->batchSize = agGlobal::getParam('default_batch_size');
  }

  public function __destruct()
  {
//    //drop temp table.
//    $this->conn->export->dropTable($this->sourceTable);
//    $this->conn->close();
  }

  protected function loadHelperObject($helperClassName)
  {
    if (! array_key_exists($helperClassName, $this->helperObjects))
    {
      $this->helperObjects[$helperClassName] = new $helperClassName ;
    }
  }

  protected function loadImportRawData()
  {

  }

  protected function normalizeData()
  {
    $err = NULL ;

    // get our connection object and start an outer transaction for the batch
    $conn = $this->getConnection() ;
    $conn->beginTransaction() ;

    foreach ($this->importComponents as $index => $componentData)
    {
      // load any helper objects we might need
      if (array_key_exists('helperClass', $componentData))
      {
        $this->loadHelperObject($componentData['helperClass']);
      }

      // need this as a var so we can use it in a variable call
      $method = $componentData['method'];
      $savepoint = __FUNCTION__ . '_'. $componentData['component'];

      // start an inner transaction / savepoint per component
      $conn->beginTransaction($savepoint) ;
      try
      {
        // Calling method to set data.
        $this->$method($componentData['throwOnError']);
        $conn->commit($savepoint);
      }
      catch(Exception $e)
      {
        $errMsg = sprintf('agImportNormalization failed during method: %s.',
          $componentData['method']);

        // our rollback and error logging happen regardless of whether this is an optional component
        sfContext::getInstance()->getLogger()->err($errMsg) ;
        $conn->rollback($savepoint);

        // if it's not an optional component, we do a bit more and stop execution entirely
        if($componentData['throwOnError'])
        {
          $conn->rollback() ;
          throw new Exception($e) ;
        }
      }

      // you'll want to do your records keeping here (eg, counts or similar)
      // be sure to use class properties to store that info so additional loops can
      // reference it
    }

    // since we encountered no throw-able errors up to this point, we can commit
    $conn->commit() ;
  }

  /**
   * Simple method for instantiating what are effectively blank records.
   * @param string $recordName The name of the record / model that will be created.
   * @param array $foreignKeys An array of foreign keys that will be set with the new record.
   * <code>array( $columnName => $columnValue, ...)</code>
   * @return integer The newly instantiated record's ID
   */
  protected function createNewRec( $recordName, $foreignKeys )
  {
    // instantiate the new record object
    $newRec = new $recordName();

    // loop through our keys and set values
    foreach($foreignKeys as $columnName => $columnValue)
    {
      $newRec[$columnName] = $columnValue;
    }

    // save and return our new id
    $newRec->save($this->conn);
    // @todo Figure out why this causes a huge chain of queries; likely this forces an update
    // of the record and it tries to populate with related records
    
    return $newRec['id'];
  }

  /**
   * Method to remove zero-length-string values in importData['_rawData']
   */
  protected function clearZLS()
  {
    foreach ($this->importData as $rowId => &$rowData)
    {
      foreach($rowData['_rawData'] as $key => &$val)
      {
        $val = trim($val);
        if ($val == '')
        {
          unset($rowdata['_rawData'][$key]);
        }
     }
     unset($val);
    }

    unset($rowData);
  }













//
//  /**
//   * Method to define status and type variables.
//   */
//  protected function defineStatusTypes()
//  {
//    // Override by child class.
//  }
//
//  /**
//   * Verify data in record for required fields and valid statuses and types
//   *
//   * @param array $record
//   * @return boolean TRUE if data in record satisfies all requirements for processing.
//   * FALSE otherwise.
//   */
//  protected function dataValidation(array $record)
//  {
//    // Check for required fields
//    // Check for full address?
//    // Check for geo info
//    // Check for email
//    // Check for phone number
//    // Check for valid status and types.
//
//    return array('pass' => TRUE,
//      'status' => 'SUCCESS',
//      'type' => null,
//      'message' => null);
//  }
//
//  /**
//   * Method to replace a white space with underscore.
//   *
//   * @param string $name A string for replacement.
//   * @return string $name A reformatted string.
//   */
//  protected function stripName($name = NULL)
//  {
//    if (is_null($name) || !is_string($name)) {
//      return $name;
//    }
//
//    $strippedName = strtolower(str_replace(' ', '_', $name));
//    return $strippedName;
//  }
//
//  /**
//   * Method to normalize data from temp table.
//   */
//  public function normalizeImport()
//  {
//    // Declare static variables.
//    $facilityContactType = $this->facilityContactType;
//
//    // Setup db connection.
//    $conn = Doctrine_Manager::connection();
//
//    // Fetch import data.
//    $query = 'SELECT * FROM ' . $this->sourceTable . ' AS i';
//    $pdo = $conn->execute($query);
//    $pdo->setFetchMode(Doctrine_Core::FETCH_ASSOC);
//    $sourceRecords = $pdo->fetchAll();
//
//    // Grab the dynamical columns of staff requirements.
//    if (count($sourceRecords) > 0) {
//      $this->getImportStaffList(array_keys($sourceRecords[0]));
//    }
//
//    //loop through records.
//    foreach ($sourceRecords as $record) {
//      $validEmail = 1;
//      $validPhone = 1;
//      $validAddress = 1;
//      $isNewFacilityRecord = 0;
//      $isNewFacilityGroupRecord = 0;
//
//      $isValidData = $this->dataValidation($record);
//      if (!$isValidData['pass']) {
//        switch ($isValidData['status']) {
//          case 'ERROR':
//            $this->nonprocessedRecords[] = array('message' => $isValidData['message'],
//              'record' => $record);
//            continue 2;
//          case 'WARNING':
//            switch ($isValidData['type']) {
//              case 'Email':
//                $validEmail = 0;
//                $this->warningMessages[] = array('message' => $isValidData['message'],
//                  'record' => $record);
//                  break;
//              case 'Phone':
//                $validPhone = 0;
//                $this->warningMessages[] = array('message' => $isValidData['message'],
//                  'record' => $record);
//                  break;
//              case 'Mail Address':
//                $validAddress = 0;
//                $this->warningMessages[] = array('message' => $isValidData['message'],
//                  'record' => $record);
//                  break;
//            }
//              break;
//          default:
//            $this->nonprocessedRecords[] = array('message' => $isValidData['message'],
//              'record' => $record);
//            continue 2;
//        }
//      }
//
//      // Declare variables.
//      $facility_name = $record['facility_name'];
//      $facility_code = $record['facility_code'];
//      $facility_resource_type_abbr = strtolower($record['facility_resource_type_abbr']);
//      $facility_resource_status = strtolower($record['facility_resource_status']);
//      $capacity = $record['facility_capacity'];
//      $facility_activation_sequence = $record['facility_activation_sequence'];
//      $facility_allocation_status = strtolower($record['facility_allocation_status']);
//      $facility_group = $record['facility_group'];
//      $facility_group_type = strtolower($record['facility_group_type']);
//      $facility_group_allocation_status = strtolower($record['facility_group_allocation_status']);
//      $facility_group_activation_sequence = $record['facility_group_activation_sequence'];
//      $email = $record['work_email'];
//      $phone = $record['work_phone'];
//      $fullAddress = $this->fullAddress;
//      $geoInfo = array('longitude' => $record['longitude'], 'latitude' => $record['latitude']);
//      $staffing = $this->dynamicStaffing($record);
//
//      $facility_resource_type_id = $this->facilityResourceTypes[$facility_resource_type_abbr];
//      $facility_resource_status_id = $this->facilityResourceStatuses[$facility_resource_status];
//      $facility_group_type_id = $this->facilityGroupTypes[$facility_group_type];
//      $facility_group_allocation_status_id = $this->facilityGroupAllocationStatuses[$facility_group_allocation_status];
//      $facility_resource_allocation_status_id = $this->facilityResourceAllocationStatuses[$facility_allocation_status];
//      $workEmailTypeId = $this->emailContactTypes[$facilityContactType];
//      $workPhoneTypeId = $this->phoneContactTypes[$facilityContactType];
//      $defaultPhoneFormatTypes = $this->defaultPhoneFormatTypes;
//      $workPhoneFormatId = $this->phoneFormatTypes[$defaultPhoneFormatTypes[(preg_match('/^\d{10}$/', $phone) ? 0 : 1)]];
//      $workAddressTypeId = $this->addressContactTypes[$facilityContactType];
//      $workAddressStandardId = $this->addressStandards;
//      $addressElementIds = $this->addressElements;
//
//      try {
//        // here we check our current transaction scope and create a transaction or savepoint based on need
//        $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
//        if ($useSavepoint) {
//          $conn->beginTransaction(__FUNCTION__);
//        } else {
//          $conn->beginTransaction();
//        }
//
//        // facility
//        // tries to find an existing record based on a unique identifier.
//        $facility = agDoctrineQuery::create($conn)
//                ->from('agFacility f')
//                ->where('f.facility_code = ?', $facility_code)
//                ->fetchOne();
//        $facilityResource = NULL;
//        $scenarioFacilityResource = NULL;
//
//        if (empty($facility)) {
//          $facility = $this->createFacility($facility_name, $facility_code, $conn);
//          $isNewFacilityRecord = 1;
//        } else {
//          $facility = $this->updateFacility($facility, $facility_name, $conn);
//
//          // tries to find an existing record based on a set of unique identifiers.
//          $facilityResource = agDoctrineQuery::create($conn)
//                  ->from('agFacilityResource fr')
//                  ->where('fr.facility_id = ?', $facility->id)
//                  ->andWhere('fr.facility_resource_type_id = ?', $facility_resource_type_id)
//                  ->fetchOne();
//        }
//
//        // Facility Resource
//        if (empty($facilityResource)) {
//          $facilityResource
//              = $this
//                  ->createFacilityResource(
//                      $facility,
//                      $facility_resource_type_id,
//                      $facility_resource_status_id,
//                      $capacity,
//                      $conn
//                  );
//        } else {
//          $facilityResource
//              = $this->
//                  updateFacilityResource(
//                      $facilityResource,
//                      $facility_resource_status_id,
//                      $capacity,
//                      $conn
//                  );
//
//          $scenarioFacilityResource = agDoctrineQuery::create($conn)
//                  ->from('agScenarioFacilityResource sfr')
//                  ->innerJoin('sfr.agScenarioFacilityGroup sfg')
//                  ->where('sfg.scenario_id = ?', $this->scenarioId)
//                  ->andWhere('facility_resource_id = ?', $facilityResource->id)
//                  ->fetchOne();
//        }
//
//        // facility group
//
//        $scenarioFacilityGroup = agDoctrineQuery::create()
//                ->from('agScenarioFacilityGroup')
//                ->where('scenario_id = ?', $this->scenarioId)
//                ->andWhere('scenario_facility_group = ?', $facility_group)
//                ->fetchOne();
//
//        if (empty($scenarioFacilityGroup)) {
//          $scenarioFacilityGroup
//              = $this
//                  ->createScenarioFacilityGroup(
//                      $facility_group,
//                      $facility_group_type_id,
//                      $facility_group_allocation_status_id,
//                      $facility_group_activation_sequence,
//                      $conn
//                  );
//          $isNewFacilityGroupRecord = 1;
//        } else {
//          $scenarioFacilityGroup
//              = $this
//                  ->updateScenarioFacilityGroup(
//                      $scenarioFacilityGroup,
//                      $facility_group_type_id,
//                      $facility_group_allocation_status_id,
//                      $facility_group_activation_sequence,
//                      $conn
//                  );
//        }
//
//        // facility resource
//        if (empty($scenarioFacilityResource)) {
//          $scenarioFacilityResource
//              = $this
//                  ->createScenarioFacilityResource(
//                      $facilityResource,
//                      $scenarioFacilityGroup,
//                      $facility_resource_allocation_status_id,
//                      $facility_activation_sequence,
//                      $conn
//                  );
//        } else {
//          $scenarioFacilityResource
//              = $this
//                  ->updateScenarioFacilityResource(
//                      $scenarioFacilityResource,
//                      $scenarioFacilityGroup->id,
//                      $facility_resource_allocation_status_id,
//                      $facility_activation_sequence,
//                      $conn
//                  );
//        }
//
//        //facility staff resource
//        $this->updateFacilityStaffResources($scenarioFacilityResource->getId(), $staffing, $conn);
//
//        // email
//        if ($validEmail) {
//          $this->updateFacilityEmail($facility, $email, $workEmailTypeId, $conn);
//        }
//
//        // phone
//        if ($validPhone) {
//          $this
//              ->updateFacilityPhone(
//                  $facility,
//                  $phone,
//                  $workPhoneTypeId,
//                  $workPhoneFormatId,
//                  $conn
//              );
//        }
//
//        // address
//        if ($validAddress) {
//          $addressId
//              = $this
//                  ->updateFacilityAddress(
//                      $facility,
//                      $fullAddress,
//                      $workAddressTypeId,
//                      $workAddressStandardId,
//                      $addressElementIds,
//                      $conn
//                  );
//        }
//
//        $this
//            ->updateFacilityGeo(
//                $facility,
//                $addressId,
//                $workAddressTypeId,
//                $workAddressStandardId,
//                $geoInfo,
//                $conn
//            );
//
//        // Set summary counts
//        if ($isNewFacilityRecord) {
//          $this->totalNewFacilityCount++;
//        }
//        if ($isNewFacilityGroupRecord) {
//          $this->totalNewFacilityGroupCount++;
//        }
//        $this->totalProcessedRecordCount++;
//
//        $facilityId = $facility->id;
//        if (!is_integer(array_search($facilityId, $this->processedFacilityIds))) {
//          array_push($this->processedFacilityIds, $facilityId);
//        }
//
//        if ($useSavepoint) {
//          $conn->commit(__FUNCTION__);
//        } else {
//          $conn->commit();
//        }
//      } catch (Exception $e) {
//        $this->errMsg .= '  Unable to normalize data.  Exception error message: ' . $e->getMessage();
//        $this->nonprocessedRecords[] = array('message' => $this->errMsg, 'record' => $record);
//        sfContext::getInstance()->getLogger()->err($errMsg);
//
//        // if we started with a savepoint, let's end with one, otherwise, rollback globally
//        if ($useSavepoint) {
//          $conn->rollback(__FUNCTION__);
//        } else {
//          $conn->rollback();
//        }
//
//        break;
//      }
//    } // end foreach
//    //drop temp table.
//    $conn->export->dropTable($this->sourceTable);
//    $conn->close();
//
//    $this->summary = array(
//      'totalProcessedRecordCount' => $this->totalProcessedRecordCount,
//      'nonprocessedRecords' => $this->nonprocessedRecords,
//      'warningMessages' => $this->warningMessages
//    );
//  }
}
