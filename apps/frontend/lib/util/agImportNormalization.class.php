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
  public    $errorMsg,
            $summary = array(),
            $warningMessages = array();
  
  protected $tempToRawQueryName = 'import_temp_to_raw',
            $helperObjects = array(),

            // array( [order] => array(componentName => component name, helperName => Name of the helper object, throwOnError => boolean, methodName => method name) )
            $importComponents = array(),

           //array( [importRowId] => array( _rawData => array(fetched data), primaryKeys => array(keyName => keyValue)) 
            $importData = array();

  /**
   * This class's constructor.
   * @param string $tempTable The name of the temporary import table to use
   */
  public function __construct($tempTable)
  {
    // DO NOT REMOVE
    parent::__construct($tempTable);
  }

  public function __destruct()
  {
//    //drop temp table.
//    $this->conn->export->dropTable($this->sourceTable);
//    $this->conn->close();
  }

  /**
   * Method to reset ALL iter data.
   */
  protected function resetIterData()
  {
    // execute the parent's reset
    parent::resetIterData();

    // now reset this data
    $this->resetRawIterData();
  }

  /**
   * Method to reset the iterator data (used in the initial call of an import and at construction)
   */
  protected function resetRawIterData()
  {
    // add additional iter members
    $this->iterData['fetchPosition'] = 0;
    $this->iterData['fetchCount'] = 0;
    $this->iterData['batchPosition'] = 0;
    $this->iterData['batchCount'] = 0;
    $this->iterData['batchSize'] = intval(agGlobal::getParam('default_batch_size'));
  }

  /**
   * Method to reset the import data array
   */
  protected function resetImportData()
  {
    $this->importData = array();
    $this->importData['_rawData'] = array();
    $this->importData['primaryKeys'] = array();
  }

  /**
   * Method to lazily load helper objects used during data normalization
   * @param string $helperClassName Name of the helper class to load
   */
  protected function loadHelperObject($helperClassName)
  {
    if (! array_key_exists($helperClassName, $this->helperObjects))
    {
      $this->helperObjects[$helperClassName] = new $helperClassName ;
    }
  }

  /**
   * Method to update the temp table and mark this batch as successful or failed
   * @param boolean $success The success value to set
   */
  protected function updateTempSuccess($success)
  {
    // grab our connection object
    $conn = $this->getConnection('temp_write');

    // create our query statement
    $q = sprintf('UPDATE %s SET %s=? WHERE id IN(?);', $this->tempTable, $this->successColumn);

    // mark this batch as failed
    $this->executePdoQuery($conn, $query,
      array($success, array_keys($this->importData)));
  }

  /**
   * Method to remove the successes from our data table so we can return just the unprocessed and
   * failed.
   */
  protected function removeTempSuccesses()
  {
    // grab our connection object
    $conn = $this->getConnection('temp_write');

    // create our query statement
    $q = sprintf('DELETE FROM %s WHERE %s = 1;', $this->tempTable, $this->successColumn);

    // remove the successes from our data table so we can return just the unprocessed and failed
    $this->executePdoQuery($conn, $query);
  }

  public function getImportStatistics()
  {
    // @todo write method to return import statistics
  }

  public function concludeImport()
  {
    // @todo write the meta-method to clear an import and return import statistics
  }

  public function getFailedRecords()
  {
    //@todo write method to instantiate / return an excel file with failed records
  }

  public function clearImport()
  {
    // @todo write method to clear/reset import but note in documentation that it will potentially
    // leave the class unusable
  }

  /**
   * Method to load and process a batch of records.
   * @return The number of records left to process
   */
  public function processBatch()
  {
    // preempt this method with a check on our error threshold and stop if we shouldn't continue
    if (! $this->checkErrThreshold())
    {
      return -1;
    }

    // load up a batch into the helper
    $this->fetchNextBatch();

    // clean, normalize, import, etc
    $continue = $this->processRawBatch();

    // determine what we're going to return
    if ($continue)
    {
      $remaining = ($this->iterData['fetchCount'] - $this->iterData['fetchPosition']);
    }
    else
    {
      $remaining = -1;
    }

    return $remaining;
  }

  /**
   * Method to fetch the next batch of raw data records from the PDO object
   */
  protected function fetchNextBatch()
  {
    // first, blow out the existing rawData
    $this->resetImportData();

    // these aren't required but make the code more readable
    $batchSize = $this->iterData['batchSize'];
    $fetchPosition =& $this->iterData['fetchPosition'];
    $batchPosition =& $this->iterData['batchPosition'];

    // get our PDO object
    $pdo = $this->_PDO[$this->tempToRawQueryName];

    // fetch the data up until it ends or we hit our batchsize limit
    while (($row = $pdo->fetch()) && (($fetchPosition % $batchSize) != 0))
    {
      // modify the record just a little
      $rowId = $row['id'];
      unset($row['id']);
      unset($row[$this->successColumn]);

      // add it to import data array and iterate our counter
      $this->importData[$rowId]['_rawData'] = $row;
      $fetchPosition++;
    }

    // iterate our batch counter too
    $batchPosition++;
  }

  /**
   * Method to initiate the import query from temp
   * @param $query A SQL query string
   */
  protected function tempToRaw($query)
  {
    $conn = $this->getConnection('temp_read');

    // first get a count of what we need from temp
    $ctQuery = sprintf('SELECT COUNT(*) FROM (%s) AS t;', $query);
    $ctResults = $this->executePdoQuery($conn, $ctQuery);
    $this->iterData['fetchCount'] = $ctResults::fetchColumn();
    
    // now caclulate the number of batches we'll need to process it all
    $this->iterData['batchCount'] = ceil(($this->iterData['fetchCount'] / $this->iterData['batchSize']));

    // now we can legitimately execute our real search
    $this->executePdoQuery($conn, $query, NULL, NULL, $this->tempToRawQueryName);
  }

  /**
   * Method to process a batch of rawdata.
   * @return boolean Whether or not this batch has breached our error threshold and should or
   * should not continue
   * @todo caller to clean, validate, normalize
   */
  protected function processRawBatch()
  {
    // our results

    // clean our rawData to make it free of zero length strings and related
    $this->clearZLS();

    // normalize and insert our data
    $normalizeSuccess = $this->normalizeData();

    // update our temp table
    $this->updateTempSuccess($normalizeSuccess);

    // if we had an error, up our error count
    if (! $normalizeSuccess)
    {
      $this->iterData['errorCount'] = ($this->iterData['errorCount'] + count($this->importData));
  }

    // check our error threshold to make sure we're still golden to continue
    $continue = $this->checkErrThreshold();
    return $continue;
  }


  /**
   * Method to check against our error threshold and update whether we should continue or not
   */
  protected function checkErrThreshold()
  {
    // continue only if our error count is below our error threshold
    $continue = ($this->iterData['errorCount'] <= $this->errThreshold) ? TRUE : FALSE;
    return $continue;
  }

  /**
   * Method to normalize and insert raw data. Returns TRUE if successful or FALSE if unsucessful.
   * @return boolean Boolean value indicating whether the import was successful or unsucessful.
   */
  protected function normalizeData()
  {
    $err = NULL ;

    // get our connection object and start an outer transaction for the batch
    $conn = $this->getConnection('normalize_write');
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
        $this->$method($componentData['throwOnError'], $conn);
        $conn->commit($savepoint);
      }
      catch(Exception $e)
      {
        $errMsg = sprintf('agImportNormalization failed during method: %s.',
          $componentData['method']);

        $conn->rollback($savepoint);

        // if it's not an optional component, we do a bit more and stop execution entirely
        if($componentData['throwOnError'])
        {
          $err = $e;
          break;
        }
      }

      // you'll want to do your records keeping here (eg, counts or similar)
      // be sure to use class properties to store that info so additional loops can
      // reference it
    }

    // attempt our final commit
    // because doctrine's savepoints are broken, we must also wrap this since earlier failures
    // may not have been detected
    if (is_null($err))
    {
      try
      {
        $conn->commit();
      }
      catch(Exception $e)
      {
        // set our err variables
        $err = $e;
        $errMsg = sprintf('Failed to execute final commit for batch #%d of %d (batch_size: %d',
          $this->iterData['batchPosition'], $this->iterData['batchCount'],
          $this->iterData['batchSize']);
      }
    }

    if (is_null($err))
    {
      // return true if successful
      return TRUE;
    }
    else
    {
      // our rollback and error logging happen regardless of whether this is an optional component
      sfContext::getInstance()->getLogger()->err($errMsg) ;
      $conn->rollback();

      // return false if not
      return FALSE;
    }
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
    // get our connection object
    $conn = $this->getConnection('normalize_write');

    // instantiate the new record object
    $newRec = new $recordName();

    // loop through our keys and set values
    foreach($foreignKeys as $columnName => $columnValue)
    {
      $newRec[$columnName] = $columnValue;
    }

    // save and return our new id
    $newRec->save($conn);
    // @todo Figure out why this causes a huge chain of queries; likely this forces an update
    // of the record and it tries to populate with related records
    
    return $newRec['id'];
  }

  /**
   * Method to remove null values from _rawData
   */
  protected function clearNullRawData()
  {
    foreach ($this->importData as $rowId => &$rowData)
    {
      foreach($rowData['_rawData'] as $key => $val)
      {
        if (is_null($val))
        {
          unset($rowData['_rawData'][$key]);
        }
      }
    }
  }

  /**
   * Method to remove zero-length-string values in importData['_rawData']
   * @deprecated This method is deprecated in favor of clearNullRawData
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
          unset($rowData['_rawData'][$key]);
        }
     }
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
