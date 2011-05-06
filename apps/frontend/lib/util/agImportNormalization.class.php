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
  const     CONN_NORMALIZE_WRITE = 'import_normalize_write';

  protected $tempToRawQueryName = 'import_temp_to_raw',
            $helperObjects = array(),

            // array( [order] => array(componentName => component name, helperName => Name of the helper object, throwOnError => boolean, methodName => method name) )
            $importComponents = array(),

           //array( [importRowId] => array( _rawData => array(fetched data), primaryKeys => array(keyName => keyValue)) 
            $importData = array();

  /**
   * This class's constructor.
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   */
  public function __construct($tempTable, $logEventLevel = NULL)
  {
    // DO NOT REMOVE
    parent::__construct($tempTable, $logEventLevel);
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
   * Method to set connection objects. (Includes both parent connections and normalization
   * connections.
   */
  protected function setConnections()
  {
    parent::setConnections();
    $this->_conn[self::CONN_NORMALIZE_WRITE] = Doctrine_Manager::connection(NULL,
      self::CONN_NORMALIZE_WRITE);
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
    $this->logInfo("Updating temp with batch success data");

    // grab our connection object
    $conn = $this->getConnection('temp_write');

    // create our query statement
    $q = sprintf('UPDATE %s SET %s=? WHERE id IN(?);', $this->tempTable, $this->successColumn);
    $this->logOk("Temp table successfully updated with batch success data");

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
    $conn = $this->getConnection(self::CONN_TEMP_WRITE);

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
   * @return The number of records left to process or -1 if a fatal error was encountered
   */
  public function processBatch()
  {
    // preempt this method with a check on our error threshold and stop if we shouldn't continue
    try
    {
      // check it once before we start anything and once after
      $this->checkErrThreshold();

      // load up a batch into the helper
      $this->fetchNextBatch();

      // clean our rawData to make it free of zero length strings and related
      $this->clearNullRawData();

      // normalize and insert our data
      $normalizeSuccess = $this->normalizeData();

      // update our temp table
      $this->updateTempSuccess($normalizeSuccess);

      // probably best to check this one more time
      $this->checkErrThreshold();
    }
    catch (Exception $e)
    {
      return -1;
    }

    // since everything's hunky-dory, let's count what's left and return that
    $remaining = ($this->iterData['fetchCount'] - $this->iterData['fetchPosition']);
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
    $batchStart = ($batchPosition + 1);

    // get our PDO object
    $pdo = $this->_PDO[$this->tempToRawQueryName];

    // log this event
    $eventMsg = "Loading batch starting at {$fetchPosition} from the temp table";
    $this->logInfo($eventMsg);

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

    $eventMsg = "Successfully fetched batch {$batchPosition} (Records {$batchStart} to {$fetchPosition})";
    $this->logOk($eventMsg);
  }

  /**
   * Method to initiate the import query from temp
   * @param $query A SQL query string
   */
  protected function tempToRaw($query)
  {
    $conn = $this->getConnection(self::CONN_TEMP_READ);

    // first get a count of what we need from temp
    $ctQuery = sprintf('SELECT COUNT(*) FROM (%s) AS t;', $query);
    $ctResults = $this->executePdoQuery($conn, $ctQuery);
    $this->iterData['fetchCount'] = $ctResults::fetchColumn();
    
    // now caclulate the number of batches we'll need to process it all
    $this->iterData['batchCount'] = ceil(($this->iterData['fetchCount'] / $this->iterData['batchSize']));

    // now we can legitimately execute our real search
    $this->executePdoQuery($conn, $query, NULL, NULL, $this->tempToRawQueryName);
    $this->logInfo("Successfully established the PDO fetch iterator.");
  }

  /**
   * Method to normalize and insert raw data. Returns TRUE if successful or FALSE if unsucessful.
   * @return boolean Boolean value indicating whether the import was successful or unsucessful.
   */
  protected function normalizeData()
  {
    $err = NULL ;
    $this->logInfo("Normalizing and inserting batch data into database.");


    // get our connection object and start an outer transaction for the batch
    $conn = $this->getConnection(self::CONN_NORMALIZE_WRITE);
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

      // log an event so we can follow what portion of the insert was begun
      $eventMsg = $this->logInfo("Calling batch processing method {$method}");

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
        $errMsg = sprintf('Import batch processing for batch %s failed during method: %s.',
          $this->iterData['batchPosition'], $componentData['method']);

        // let's capture this error, regardless of whether we'll throw
        $this->logErr($errMsg, count($this->importData));

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
        $errMsg = sprintf('Failed to execute final commit for batch #%d of %d (batch_size: %d)',
          $this->iterData['batchPosition'], $this->iterData['batchCount'],
          $this->iterData['batchSize']);
      }
    }

    if (is_null($err))
    {
      $this->logOk("Successfully inserted and normalized batch data");
      return TRUE;
    }
    else
    {
      // our rollback and error logging happen regardless of whether this is an optional component
      $this->logErr($errMsg, count($this->importData)) ;
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
    $conn = $this->getConnection(self::CONN_NORMALIZE_WRITE);

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
    $this->logInfo("Removing null data from batch.");
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
        if (empty(trim($val)))
        {
          unset($rowData['_rawData'][$key]);
        }
     }
    }

    unset($rowData);
  }
}
