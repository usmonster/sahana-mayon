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
  const CONN_NORMALIZE_READ = 'import_normalize_read';
  const CONN_NORMALIZE_WRITE = 'import_normalize_write';


  protected $helperObjects = array(),
  $tempToRawQueryName = 'import_temp_to_raw',
  // array( [order] => array(componentName => component name, helperName => Name of the helper object, throwOnError => boolean, methodName => method name) )
  $importComponents = array(),
  //array( [importRowId] => array( _rawData => array(fetched data), primaryKeys => array(keyName => keyValue))
  $importData = array(),
  $importCount = 0;

  /**
   * This classes' destructor.
   */
  public function __destruct()
  {
    parent::__destruct();
  }

  /**
   * Method to dynamically build a (mostly) static tempSelectQuery
   * @return string Returns a string query
   */
  abstract protected function buildTempSelectQuery();

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
    //TODO: really figure out these bounds. -UA
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
    $this->eh->logDebug('Removing existing pre-normalization import data.');
    $this->importData = array();
  }

  /**
   * Method to set connection objects. (Includes both parent connections and normalization
   * connections.
   */
  protected function setConnections()
  {
    parent::setConnections();

    $dm = Doctrine_Manager::getInstance();

    // always re-parent properly
    $dm->setCurrentConnection('doctrine');
    $adapter = $dm->getCurrentConnection()->getDbh();
    $conn = Doctrine_Manager::connection($adapter, self::CONN_NORMALIZE_WRITE);
    $conn->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, TRUE);
    $conn->setAttribute(Doctrine_Core::ATTR_AUTOCOMMIT, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, TRUE);
    $conn->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_LOAD_REFERENCES, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_VALIDATE, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_CASCADE_SAVES, FALSE);
    $this->_conn[self::CONN_NORMALIZE_WRITE] = $conn;


    $dm->setCurrentConnection('doctrine');
    $adapter = $dm->getCurrentConnection()->getDbh();
    $conn = Doctrine_Manager::connection($adapter, self::CONN_NORMALIZE_READ);
    $dm->setCurrentConnection(self::CONN_NORMALIZE_READ);
    $this->_conn[self::CONN_NORMALIZE_READ] = $conn;
  }

  /**
   * Method to lazily load helper objects used during data normalization
   * @param string $helperClassName Name of the helper class to load
   */
  protected function loadHelperObject($helperClassName)
  {
    if (!array_key_exists($helperClassName, $this->helperObjects)) {
      $this->helperObjects[$helperClassName] = new $helperClassName;
    }
  }

  /**
   * Method to update the temp table and mark this batch as successful or failed print("Import is Done<br>");
   * @param boolean $success The success value to set
   */
  protected function updateTempSuccess($success)
  {
    $this->eh->logDebug("Updating temp with batch success data");

    // grab our connection object
    $conn = $this->getConnection(self::CONN_TEMP_WRITE);

    // create our query statement
    $q = 'UPDATE ' . $this->tempTable .
        ' SET ' . $this->successColumn . '=?' .
        ' WHERE ' . $this->idColumn .
        ' IN(' . implode(',', array_fill(0, count($this->importData), '?')) . ');';

    // create our parameter array
    $qParam = array_merge(array($success), array_keys($this->importData));

    // mark this batch accordingly
    $this->executePdoQuery($conn, $q, $qParam);

    $this->eh->logNotice("Temp table successfully updated with batch success data");
  }

  /**
   * Method to remove the successes from our data table so we can return just the unprocessed and
   * failed.
   */
  protected function removeTempSuccesses()
  {
    // grab our connection object
    $conn = $this->getConnection(self::CONN_TEMP_WRITE);

    // create our query statement
    $q = sprintf('DELETE FROM %s WHERE %s = 1;', $this->tempTable, $this->successColumn);

    // remove the successes from our data table so we can return just the unprocessed and failed
    $this->executePdoQuery($conn, $query);
  }

  public function getImportStatistics()
  {
    // @todo write method to return import statistics
    return $this->importCount;
  }

  public function getImportDataDump()
  {
    return $this->importData;
  }

  public function getImportEvents()
  {
    return $this->eh->getEvents();
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
   * @return int The number of records left to process or -1 if a fatal error was encountered
   */
  public function processBatch()
  {
    // preempt this method with a check on our error threshold and stop if we shouldn't continue
    try {

      // check it once before we start anything and once after
      $this->eh->checkErrThreshold();

      // load up a batch into the helper
      $this->fetchNextBatch();

      // normalize and insert our data
      $normalizeSuccess = $this->normalizeData();

      // update our temp table
      $this->updateTempSuccess($normalizeSuccess);

      // probably best to check this one more time
      $this->eh->checkErrThreshold();
    } catch (Exception $e) {
      return -1;
    }

    // since everything's hunky-dory, let's count what's left and return that
    $remaining = ($this->iterData['fetchCount'] - $this->iterData['fetchPosition']);

    // Get memory usage
    $memoryUsage = $this->getPeakMemoryUsage();
    
    $this->eh->logCrit("Memory: $memoryUsage");
    

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
    $fetchPosition = & $this->iterData['fetchPosition'];
    $batchPosition = & $this->iterData['batchPosition'];
    $batchStart = $fetchPosition;
    $batchEnd = ($fetchPosition + $batchSize - 1);

    // get our PDO object
    $pdo = $this->_PDO[$this->tempToRawQueryName];

    // log this event
    $eventMsg = "Loading batch starting at {$fetchPosition} from the temp table";
    $this->eh->logDebug($eventMsg);

    // fetch the data up until it ends or we hit our batchsize limit
    while (($row = $pdo->fetch()) && ($fetchPosition <= $batchEnd)) {
      // modify the record just a little
      $rowId = $row->id;

      // add it to import data array and iterate our counter
      $this->eh->logDebug('Fetching row {' . $rowId . '} from temp table into import data.');

      // use the import spec as our definitive columns list and add the obj properties magically
      foreach ($this->importSpec as $columnName => $columnSpec) {
        // checking for empty negates the need for an explicit removal step
        if (!empty($row->$columnName)) {
          $this->importData[$rowId]['_rawData'][$columnName] = $row->$columnName;
        }
      }
      $this->importData[$rowId]['primaryKeys'] = array();
      $fetchPosition++;
    }

    // iterate our batch counter too
    $batchPosition++;

    $eventMsg = "Successfully fetched batch {$batchPosition} (Records {$batchStart} to {$fetchPosition})";
    $this->eh->logInfo($eventMsg);
  }

  /**
   * Method to initiate the import query from temp
   * @param $query A SQL query string
   */
  protected function tempToRaw($query)
  {
    $conn = $this->getConnection(self::CONN_TEMP_READ);

    // first get a count of what we need from temp
    $this->eh->logDebug('Fetching the total number of records and establishing batch size.');
    $ctQuery = sprintf('SELECT COUNT(*) FROM (%s) AS t;', $query);
    $ctResults = $this->executePdoQuery($conn, $ctQuery);
    $this->iterData['fetchCount'] = $ctResults->fetchColumn();

    // now caclulate the number of batches we'll need to process it all
    $this->iterData['batchCount'] = intval(ceil(($this->iterData['fetchCount'] / $this->iterData['batchSize'])));
    $this->eh->logInfo('Dataset comprised of {' . $this->iterData['fetchCount'] . '} records divided ' .
        'into {' . $this->iterData['batchCount'] . '} batches of {' . $this->iterData['batchSize'] .
        '} records per batch.');

    // now we can legitimately execute our real search
    $this->eh->logDebug('Starting initial fetch from temp.');
    $this->executePdoQuery($conn, $query, array(), Doctrine_Core::FETCH_OBJ,
                           $this->tempToRawQueryName);
    $this->eh->logInfo("Successfully established the PDO fetch iterator.");
  }

  /**
   * Method to normalize and insert raw data. Returns TRUE if successful or FALSE if unsucessful.
   * @return boolean Boolean value indicating whether the import was successful or unsucessful.
   */
  protected function normalizeData()
  {
    $err = NULL;
    $this->eh->logInfo("Normalizing and inserting batch data into database.");

    $conn = $this->getConnection(self::CONN_NORMALIZE_WRITE);
    $m = memory_get_usage();
    self::clearConnection($conn);
    self::clearConnection($this->getConnection(self::CONN_NORMALIZE_READ));
    $m = memory_get_usage();
    $conn->beginTransaction();

    foreach ($this->importComponents as $index => $componentData) {
      // load any helper objects we might need
      if (array_key_exists('helperClass', $componentData)) {
        $this->loadHelperObject($componentData['helperClass']);
      }

      // need this as a var so we can use it in a variable call
      $method = $componentData['method'];
      $savepoint = __FUNCTION__ . '_' . $componentData['component'];

      // log an event so we can follow what portion of the insert was begun
      $eventMsg = $this->eh->logInfo("Calling batch processing method {$method}");

      // start an inner transaction / savepoint per component
      $conn->beginTransaction($savepoint);
      try {
        // Calling method to set data.
        $this->$method($componentData['throwOnError'], $conn);
        $conn->commit($savepoint);
      } catch (Exception $e) {
        $errMsg = sprintf('Import batch processing for batch %s failed during method: %s.',
                          $this->iterData['batchPosition'], $componentData['method']);

        // let's capture this error, regardless of whether we'll throw
        $this->eh->logErr($errMsg, count($this->importData));

        $conn->rollback($savepoint);

        // if it's not an optional component, we do a bit more and stop execution entirely
        if ($componentData['throwOnError']) {
          $err = $e;
          $this->eh->logErr($e->getMessage(), 0);
          break;
        } else {
          $this->eh->logDebug($e->getMessage());
        }
      }
    }

    // attempt our final commit
    // because doctrine's savepoints are broken, we must also wrap this since earlier failures
    // may not have been detected
    if (is_null($err)) {
      try {
        $conn->commit();
      } catch (Exception $e) {
        // set our err variables
        $err = $e;
        $errMsg = sprintf('Failed to execute final commit for batch #%d of %d (batch_size: %d)',
                          $this->iterData['batchPosition'], $this->iterData['batchCount'],
                          $this->iterData['batchSize']);
      }
    }

    if (is_null($err)) {
      $this->eh->logNotice("Successfully inserted and normalized batch data");

      // you'll want to do your record keeping here (eg, counts or similar)
      // be sure to use class properties to store that info so additional loops can
      // reference it

      $this->importCount += count($this->importData);

      return TRUE;
    } else {
      // our rollback and error logging happen regardless of whether this is an optional component
      $this->eh->logErr($errMsg, count($this->importData));
      $conn->rollback();

      // return false if not
      return FALSE;
    }
  }

  /**
   *
   * @param sfEvent $event
   * @todo document this
   */
  public static function processImportEvent(sfEvent $event)
  {
    // gets the action, context, and importer object
    $action = $event->getSubject();
    $context = $action->getContext();
    ////$context = sfContext::getInstance();
    $importer = $action->importer;
    $moduleName = $action->getModuleName();

    //TODO: get import data directory root info from global param
    $importDataRoot = sfConfig::get('sf_upload_dir');
    $importDir = $importDataRoot . DIRECTORY_SEPARATOR . $moduleName;
    if (!file_exists($importDir)) {
      mkdir($importDir);
    }
    $statusFile = $importDir . DIRECTORY_SEPARATOR . 'status.yml';

    // generates the identifier for the event status
//    $statusId = implode('_', array($moduleName, /* $action->actionName, */ 'status'));
//    if ($context->has($statusId)) {
//      $status = $context->get($statusId);
//    }
    if (!file_exists($statusFile)) {
      //TODO: check if directory is writeable? -UA
      touch($statusFile);
    }
    if (!is_writable($statusFile)) {
      $importer->eh->logEmerg('Status file not writeable: ' . $statusFile);
      return;
    }

    // let other things happen
    $action->getUser()->shutdown();
    session_write_close();

    // blocks import if already in progress
    $status = sfYaml::load($statusFile);
    $abortFlagId = 'aborted';
    if (isset($status) && 0 != $status['batchesLeft']) {
      if (isset($status[$abortFlagId]) && $status[$abortFlagId]) {
        $importer->eh->logNotice('Starting new import after user aborted previous attempt.');
        unset($status[$abortFlagId]);
      } else {
        //TODO: distinguish between the two, add cleanup method (action?) for recovery workflow. -UA
        $importer->eh->logAlert('Import in progress, or attempting new import after failed attempt?');
        return;
      }
    }

    // uploads the import file
    $uploadedFile = $action->uploadedFile;
    $importPath = $importDir . DIRECTORY_SEPARATOR . 'import.xls' /* $uploadedFile['name'] */;
    if (!move_uploaded_file($uploadedFile['tmp_name'], $importPath)) {
      $importer->eh->logEmerg('Cannot move uploaded file to destination!');
      // exception is already thrown by logEmerg ^ , but just in case...
      return;
    }

    $importer->processXlsImportFile($importPath);

    // initializes the job progress information
    $totalBatchCount = $importer->iterData['batchCount'];
    $batchesLeft = $totalBatchCount;
    $totalRecordCount = $importer->iterData['tempCount'];
    $recordsLeft = $totalRecordCount;

    $startTime = time();
    //$context->set($statusId, array($batchesLeft, $totalBatchCount, $startTime));
    $status['batchesLeft'] = $batchesLeft;
    $status['totalBatchCount'] = $totalBatchCount;
    $status['startTime'] = $startTime;
    file_put_contents($statusFile, sfYaml::dump($status), LOCK_EX);

    // processes batches until complete, aborted, or an unrecoverable error occurs
    //$abortFlagId = implode('_', array('abort', $statusId));
    while ($batchesLeft > 0) {
//      if ($context->has($abortFlagId) && $context->get($abortFlagId)) {
//        $context->set($abortFlagId, NULL);
//        break;
//      }
      $status = sfYaml::load($statusFile);
      if (isset($status[$abortFlagId]) && $status[$abortFlagId]) {
        $importer->eh->logAlert('User canceled import, stopping import.');
        unset($status[$abortFlagId]);
        file_put_contents($statusFile, sfYaml::dump($status), LOCK_EX);
        break;
      }
      $batchResult = $importer->processBatch();
      // if the last batch did nothing
      if ($batchResult == $recordsLeft) {
        $importer->eh->logEmerg('No progress since last batch! Stopping import.');
        // exception is already thrown by logEmerg ^ , but just in case...
        break;
      } else {
        $recordsLeft = $batchResult;
      }

      //TODO: use $batchResult ("records left") instead?
      $batchesLeft = $importer->iterData['batchCount'] - $importer->iterData['batchPosition'] - 1;
      //$context->set($statusId, array($batchesLeft, $totalBatchCount, $startTime));
      $status = sfYaml::load($statusFile);
      $status['batchesLeft'] = $batchesLeft;
      $status['totalBatchCount'] = $totalBatchCount;
      $status['startTime'] = $startTime;
      file_put_contents($statusFile, sfYaml::dump($status), LOCK_EX);
    }

    $context->getEventDispatcher()->notify(new sfEvent($action, 'import.do_reindex'));

    unset($action->importer);
    //unset($importer);
  }

  public static function resetImportStatus($moduleName)
  {
    //TODO: get import data directory root info from global param
    $importDataRoot = sfConfig::get('sf_upload_dir');
    $importDir = $importDataRoot . DIRECTORY_SEPARATOR . $moduleName;
    $statusFile = $importDir . DIRECTORY_SEPARATOR . 'status.yml';
    if (is_writable($statusFile)) {
      $status = sfYaml::load($statusFile);
      $status['batchesLeft'] = 0;
      $status['totalBatchCount'] = 0;
      $status['startTime'] = 0;
      file_put_contents($statusFile, sfYaml::dump($status), LOCK_EX);
    }
  }

  /**
   * Simple method for instantiating what are effectively blank records.
   * @param string $recordName The name of the Doctrine_Record / model that will be created.
   * @param array $foreignKeys An array of foreign keys that will be set with the new record.
   * <code>array( $columnName => $columnValue, ...)</code>
   * @return integer The newly instantiated record's ID
   */
  protected function createNewRec(Doctrine_Table $tableObject, array $foreignKeys)
  {
    // get our connection object
    $conn = $this->getConnection(self::CONN_NORMALIZE_WRITE);
    $recordName = $tableObject->getComponentName();

    // instantiate the new record object
    $newRec = new $recordName($tableObject, TRUE, FALSE);
    $newRec->synchronizeWithArray($foreignKeys);

    // save and return our new id
    $newRec->save($conn);

    return $newRec['id'];
  }

  /**
   * Method to remove null values from _rawData
   * @deprecated The use of the FETCH_OBJ allows value-by-value examination at the fetch and
   * avoids inserting empty values altogether
   */
  protected function clearNullRawData()
  {
    $this->eh->logInfo("Removing null data from batch.");
    foreach ($this->importData as $rowId => &$rowData) {
      foreach ($rowData['_rawData'] as $key => $val) {
        if (empty($val)) {
          unset($rowData['_rawData'][$key]);
        }
      }
    }
    unset($rowData);
  }

}
