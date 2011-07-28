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
  $importCount = 0,
  $unprocessedXLS = FALSE,
  $unprocessedBaseName,
  $XlsMaxExportSize;

  /**
   * This classes' destructor.
   */
  public function __destruct()
  {
    parent::__destruct();
  }

  /**
   * Method to initialize this class
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   */
  public function __init($tempTable = NULL, $logEventLevel = NULL)
  {
    // DO NOT REMOVE
    parent::__init($tempTable, $logEventLevel);

    $this->setUnprocessedBaseName();
    $this->XlsMaxExportSize = agGlobal::getParam('xls_max_export_size');
  }


  /**
   * Method to dynamically build a (mostly) static tempSelectQuery
   * @return string Returns a string query
   */
  abstract protected function buildTempSelectQuery();

  /**
   * Method to set the unprocessed records basename
   */
  abstract protected function setUnprocessedBaseName();

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
    $this->iterData['batchStart'] = 0;
    $this->iterData['batchPosition'] = 0;
    $this->iterData['batchCount'] = 0;
    $this->iterData['batchSize'] = intval(agGlobal::getParam('default_batch_size'));
    $this->iterData['processedSuccessful'] = 0;
    $this->iterData['processedFailed'] = 0;
    $this->iterData['unprocessed'] = 0;
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

    // we do this little one explicitly.
    $this->getImportWrite();

    $dm = Doctrine_Manager::getInstance();
    $dm->setCurrentConnection('doctrine');
    $adapter = $dm->getCurrentConnection()->getDbh();
    $conn = Doctrine_Manager::connection($adapter, self::CONN_NORMALIZE_READ);
    $dm->setCurrentConnection(self::CONN_NORMALIZE_READ);
    $this->_conn[self::CONN_NORMALIZE_READ] = $conn;
  }

  /**
   * Method to return the import normalize write connection.
   * @param boolean $new Whether or not this class returns a new instance of the connection
   * @return Doctrine_Connection A doctrine connection object
   */
  protected function getImportWrite($new = TRUE) {
    if ($conn = $this->getConnection(self::CONN_NORMALIZE_WRITE)) {
      if (!$new) {
        return $conn;
      } else {
       $this->closeConnection($conn);
      }
    }

    // need this guy for all the heavy lifting
    $dm = Doctrine_Manager::getInstance();

    // save for re-setting
    $currConn = $dm->getCurrentConnection();
    $currConnName = $dm->getConnectionName($currConn);
    $adapter = $currConn->getDbh();

    // always re-parent properly
    $dm->setCurrentConnection('doctrine');

    $conn = Doctrine_Manager::connection($adapter, self::CONN_NORMALIZE_WRITE);
    $conn->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, TRUE);
    $conn->setAttribute(Doctrine_Core::ATTR_AUTOCOMMIT, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, TRUE);
    $conn->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_LOAD_REFERENCES, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_VALIDATE, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_CASCADE_SAVES, FALSE);
    $this->_conn[self::CONN_NORMALIZE_WRITE] = $conn;

    $dm->setCurrentConnection($currConnName);
    return $conn;
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

    $dataSize = count($this->importData);

    // create our query statement
    $q = 'UPDATE ' . $this->tempTable .
        ' SET ' . $this->successColumn . '=?' .
        ' WHERE ' . $this->idColumn .
        ' IN(' . implode(',', array_fill(0, $dataSize, '?')) . ');';

    // create our parameter array
    $qParam = array_merge(array(intval($success)), array_keys($this->importData));

    // mark this batch accordingly
    $this->executePdoQuery($conn, $q, $qParam);

    if ($success)
    {
      $this->iterData['processedSuccessful'] += $dataSize;
    }
    else
    {
      $this->iterData['processedFailed'] += $dataSize;
    }

    $this->iterData['unprocessed'] -= $dataSize;
    $this->eh->logInfo("Temp table successfully updated with batch success data");
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

  public function getImportDataDump()
  {
    return $this->importData;
  }

  /**
   * Method to return all import events as an array
   * @return array An array of import events
   */
  public function getImportEvents()
  {
    return $this->eh->getEvents(agEventHandler::EVENT_SORT_TS);
  }

  /**
   * Method to close out an import procedure
   */
  public function concludeImport()
  {
    // now, close any unnecessary connections to release as much memory as possible
    Doctrine_Manager::getInstance()->setCurrentConnection('doctrine');
    $this->closeConnection($this->getConnection(self::CONN_TEMP_WRITE));
    $this->closeConnection($this->getConnection(self::CONN_NORMALIZE_READ));
    $this->closeConnection($this->getConnection(self::CONN_NORMALIZE_WRITE));
    
    // build our unprocessed XLS file
    $this->unprocessedXLS = $this->setUnprocessedXLS();

    // now release all remaining connections
    $this->closeConnection($this->getConnection(self::CONN_TEMP_READ));

    // finally, kick off this hellish piece of work
    if (agGlobal::getParam('import_auto_index')) {
      $this->dispatcher->notify(new sfEvent($this, 'import.do_reindex'));
    }
  }

  /**
   * Method to return an array pointing to
   * @return array An array containing the XLS data filename and path
   */
  public function getUnprocessedXLS()
  {
    return $this->unprocessedXLS;
  }

  /**
   * Method to construct an XLS file of unprocessed records returns the path of the XLS file if
   * successful and FALSE if not
   * @return boolean Whether or not the process was successful
   */
  protected function setUnprocessedXLS()
  {
    // return false if there are no records to process
    $totalUnprocessed = $this->iterData['unprocessed'] + $this->iterData['processedFailed'];
    if ($totalUnprocessed == 0 && $this->iterData['fetchCount'] > 0)
    {
      $eventMsg = 'All records were successfully processed.';
      $this->eh->logNotice($eventMsg);
      return FALSE;
    }

    // log a message and continue
    $eventMsg = 'All records could not be successfully processed.';
    $this->eh->logWarning($eventMsg);

    // first search our directory for existing unprocessed files and remove
    $pathBase = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . $this->unprocessedBaseName;
    foreach(glob(($pathBase . '*')) as $filename) {
      $eventMsg = 'Removing old export file ' . $filename . '.';
      $this->eh->logDebug($eventMsg);
      unlink($filename);
      $eventMsg = 'Successfully removed old export file ' . $filename . '.';
      $this->eh->logInfo($eventMsg);
    }

    // rinse and repeat
    $pathBase = sfConfig::get('sf_download_dir') . DIRECTORY_SEPARATOR . $this->unprocessedBaseName;
    foreach(glob(($pathBase . '*')) as $filename) {
      $eventMsg = 'Removing old export file ' . $filename . '.';
      $this->eh->logDebug($eventMsg);
      unlink($filename);
      $eventMsg = 'Successfully removed old export file ' . $filename . '.';
      $this->eh->logInfo($eventMsg);
    }

    // set some smart variables
    $date = date('Ymd_His');
    $downloadFile = $this->unprocessedBaseName . '_' . $date . '.zip';
    $zipPath = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . $downloadFile;
    $exportFiles = array();

    // Create zip file
    $zipFile = new ZipArchive();

    if ($zipFile->open($zipPath, ZIPARCHIVE::CREATE) !== TRUE) {
      $this->eh->err('Could not create the output zip file' .  $zipPath .
        '. Check your permissions to make sure you have write access.');
    } else {
      $this->eh->logDebug('Successfully created' .  $zipPath . '.');
    }
    
    // get our temporary table read connection
    $conn = $this->getConnection(self::CONN_TEMP_READ);
    $columnHeaders = array_keys($this->importSpec);
    $selectCols = 't.' . implode(', t.', $columnHeaders);

    // build our query statement
    $this->eh->logDebug('Establishing fetch from the database.');
    $q = 'SELECT ' . $selectCols . ' FROM ' . $this->tempTable . ' AS t WHERE t.' .
      $this->successColumn . ' != ? OR ' . $this->successColumn . ' IS NULL;';
    $pdo = $this->executePdoQuery($conn, $q, array(TRUE));
    $this->eh->logDebug('PDO object successfully created.');

    // set counters
    $i = 1;
    $fetchPosition = 0;

    // begin fetching from the database, starting with our first record
    while ($row = $pdo->fetch()) {
      // keeps up from potentially ending up in a neverending loop
      set_time_limit($this->maxBatchTime);
      
      // start by incrementing our fetch position
      $fetchPosition++;

      // reset our exportData array and add our first row to it
      $exportData = array();
      $exportData[] = $row;

      // continue fetching until we either run out of records or hit our batch limit
      while (($fetchPosition % $this->XlsMaxExportSize != 0) && ($row = $pdo->fetch())) {
        // always increment fetch position immediately
        $fetchPosition++;

        // add the row to our $rows array
        $exportData[] = $row;
      }

      // set our xlsname and pass it and our export data to the buildXls method
      $xlsName = $this->unprocessedBaseName . '_' . $date . '_' . $i . '.xls';
      $this->eh->logInfo('Fetched ' . count($row) . ' records from the database into ' .
        'batch ' . $i . '. Now adding records to ' . $xlsName . '.');
      $xlsPath = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . $xlsName;

      // check for successful creation of the xlsfile (both soft and hard)
      try {
        if (! $this->buildXls($xlsPath, $exportData)) {
          break;
        }
      } catch (Exception $e) {
        $this->eh->logErr($e->getMessage());
        break;
      }

      // if all went well, add it to the exportFiles array
      $exportFiles[$i] = array($xlsPath, $xlsName);

      // iterate our batch counter
      $i++;
    }

    // we do this to check if any records were processed at all
    if ($fetchPosition == 0 || count($exportFiles) == 0) {

      // if none were, log a warning
      $this->eh->logWarning('No unprocessed records could be retrieved. Could not create ' .
        'unprocessed records export.');

      // close out and remove our zipfile
      $zipFile->close();
      unlink($zipPath);

      // then return false
      return FALSE;
    }

    // otherwise, add our xls files to the zip
    foreach ($exportFiles as $xlsFileInfo) {
      $this->eh->logDebug('Adding ' . $xlsFileInfo[1] . ' to zip file.');
      $zipFile->addFile($xlsFileInfo[0], $xlsFileInfo[1]);
    }
    $this->eh->logInfo('Successfully added ' . count($exportFiles) .
      ' xls files to zip file.');

    // close the zip
    $zipFile->close();

    // remove the individual xls files
    foreach ($exportFiles as $xlsFileInfo) {
      $this->eh->logDebug('Removing ' . $xlsFileInfo[1] . ' from the temp directory.');
      unlink($xlsFileInfo[0]);
    }
    $this->eh->logInfo('Successfully removed xls files from the temp directory.');

    // finally, move the zip file to its final web-accessible location
    $this->eh->logDebug('Moving ' . $downloadFile . ' to user-accesible directory.');
    $downloadPath = sfConfig::get('sf_download_dir') . DIRECTORY_SEPARATOR . $downloadFile;
    if (! rename($zipPath, $downloadPath)) {
      $this->eh->logErr('Unable to move ' . $downloadFile . ' to the specified upload ' .
        'directory. Check your sf_upload_dir configuration to ensure you have write permissions.');
    }

    $eventMsg = "Successfully created export file of unprocessed records.";
    $this->eh->logNotice($eventMsg);


    return $downloadFile;

  }

  /**
   * Method to create an xls import file and return TRUE if successful
   * @param string $xlsPath Path of the export file to create
   * @param array $exportData An array of export data to write to the xls file
   * @return boolean Returns TRUE if successful
   */
  protected function buildXls($xlsPath, array $exportData)
  {
    // load the Excel writer object
    $sheet = new agExcel2003ExportHelper($this->unprocessedBaseName);

    // Write the column headers
    foreach (array_keys($exportData[0]) as $columnHeader) {
      $sheet->label($columnHeader);
      $sheet->right();
    }

    foreach ($exportData as $exportRowData) {
      $sheet->down();
      $sheet->home();

      // Write values
      foreach ($exportRowData as $value) {
        $sheet->label($value);
        $sheet->right();
      }

      // Capture peak memory
      $currentMemoryUsage = memory_get_usage();
      if ($currentMemoryUsage > $this->peakMemory) {
        $this->peakMemory = memory_get_usage();
      }
    }

    $sheet->save($xlsPath);
    return TRUE;
  }

  /**
   * Basically everything that can be wiped about this object
   * @param $resetEvents Boolean to determine whether or not events will also be reset
   */
  protected function clearImport($resetEvents = FALSE)
  {
    $this->resetImportData();
    $this->resetIterData();
    $this->clearConnections();
    if ($resetEvents) { $this->eh->resetEvents(); }
  }

  /**
   * Method to load and process a batch of records.
   * @return int The number of records left to process or -1 if a fatal error was encountered
   */
  public function processBatch()
  {
    // keeps us from potentially ending up in a neverending loop
    set_time_limit($this->maxBatchTime);
    
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
    $this->eh->logInfo("Memory: $memoryUsage");
    
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

    // increment our fetch counter
    $batchPosition = & $this->iterData['batchPosition'];
    $batchPosition++;
    $batchStart = $fetchPosition + 1;
    $this->iterData['batchStart'] = $batchStart;
    $batchEnd = ($fetchPosition + $batchSize);

    // get our PDO object
    $pdo = $this->_PDO[$this->tempToRawQueryName];

    // log this event
    $eventMsg = 'Loading batch starting at ' . $batchStart . ' from the temp table';
    $this->eh->logDebug($eventMsg);

    // fetch the data up until it ends or we hit our batchsize limit
    while (($fetchPosition < $batchEnd) && ($row = $pdo->fetch())) {
      // increment our fetch counter
      $fetchPosition++;

      // modify the record just a little
      $rowId = $row->id;

      // add it to import data array and iterate our counter
      $this->eh->logDebug('Fetching row ' . $rowId . ' from temp table into import data.');

      // use the import spec as our definitive columns list and add the obj properties magically
      foreach ($this->importSpec as $columnName => $columnSpec) {
        // checking for empty negates the neebasenamed for an explicit removal step
        if (!is_null($row->$columnName)) {
          $this->importData[$rowId]['_rawData'][$columnName] = $row->$columnName;
        }
      }
      $this->importData[$rowId]['primaryKeys'] = array();
    }

    $eventMsg = 'Successfully fetched batch ' . $batchPosition . ' (Records ' . $batchStart .
      ' to ' . $fetchPosition . ')';
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
    $this->iterData['unprocessed'] = $this->iterData['fetchCount'];

    // now caclulate the number of batches we'll need to process it all
    $this->iterData['batchCount'] = intval(ceil(($this->iterData['fetchCount'] / $this->iterData['batchSize'])));
    $this->eh->logNotice('Dataset comprised of ' . $this->iterData['fetchCount'] . ' records divided ' .
        'into ' . $this->iterData['batchCount'] . ' batches of ' . $this->iterData['batchSize'] .
        ' records per batch.');

    // now we can legitimately execute our real search
    $this->eh->logDebug('Starting initial fetch from temp.');
    $this->executePdoQuery($conn, $query, array(), Doctrine_Core::FETCH_OBJ,
                           $this->tempToRawQueryName);
    $this->eh->logInfo("Successfully established the PDO fetch iterator.");

    $this->eh->logNotice('Beginning insertion of import data from temporary table.');
  }

  /**
   * Method to normalize and insert raw data. Returns TRUE if successful or FALSE if unsucessful.
   * @return boolean Boolean value indicating whether the import was successful or unsucessful.
   */
  protected function normalizeData()
  {
    $err = NULL;
    $eventMsg = 'Normalizing and inserting batch data into database. (Batch ' .
      $this->iterData['batchPosition'] . '/' . $this->iterData['batchCount'] . ')';
    $this->eh->logInfo($eventMsg);

    $conn = $this->getImportWrite(TRUE);
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
        $errMsg = 'Import batch processing for batch ' . $this->iterData['batchPosition'] .
          ' failed during method: ' . $componentData['method'] . ' with message: ' .
          $e->getMessage();

        // let's capture this error, regardless of whether we'll throw
        $this->eh->logErr($errMsg, 0);

        $conn->rollback($savepoint);

        // if it's not an optional component, we do a bit more and stop execution entirely
        if ($componentData['throwOnError']) {
          $err = $e;
          break;
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
      $this->eh->logNotice('Successfully processed batch ' . $this->iterData['batchPosition'] .
        ' of ' .  $this->iterData['batchCount'] . '. (Records ' . $this->iterData['batchStart'] .
        ' through ' . $this->iterData['fetchPosition'] . ')');

      // you'll want to do your record keeping here (eg, counts or similar)
      // be sure to use class properties to store that info so additional loops can
      // reference it

      $this->importCount += count($this->importData);

      return TRUE;
    } else {
      // our rollback and error logging happen regardless of whether this is an optional component
      $errMsg = 'Batch ' . $this->iterData['batchPosition'] . ' of ' . 
        $this->iterData['batchCount'] . ' (Records ' . $this->iterData['batchStart'] . ' through ' .
        $this->iterData['fetchPosition'] . ') encountered a fatal error and could not continue.';

      $conn->rollback();

      // log our err count but postpone the threshold check until the end of the processBatch
      $this->eh->logErr($errMsg, count($this->importData), FALSE);

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
