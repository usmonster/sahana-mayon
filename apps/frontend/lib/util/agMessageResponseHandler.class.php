<?php

/**
 * A Staff data import class. Standard (minimal) usage of this class would be to call
 * <code>
 * $this->importStaffFrom*();
 * $this->processBatch(); // in a loop
 * $this->concludeImport();
 * <code>
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agMessageResponseHandler extends agImportNormalization
{
  public $agEventAvailableStaffStatus;
  public $agEventUnAvailableStaffStatus;
  /**
   * This class's constructor.
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   */
  public function __construct($tempTable = NULL, $logEventLevel = NULL)
  {
    if (is_null($tempTable)) { $tempTable = 'temp_message_import'; }

    // DO NOT REMOVE
    parent::__construct($tempTable, $logEventLevel);

    // set the import components array as a class property
    $this->agAvailbleEventStaffStatus = agEventStaffHelper::returnAvailableEventStaffStatus();
    $this->agEventUnAvailableStaffStatus = agEventStaffHelper::returnUnAvailableEventStaffStatus();
    
    
    $this->setImportComponents();
    $this->tempTableOptions = array('type' => 'MYISAM', 'charset' => 'utf8');
    $this->importHeaderStrictValidation = TRUE;

    $this->errThreshold = 100;
  }

  public function consumeResponses(sfEvent $event = null, $reset = 0)
  {
    $this->importResponsesFromExcel($event['importFile']);
    
    if (isset($event)) {
      $action = $event->getSubject();
    }
    if (isset($action)) {
      $context = $action->getContext();
      $models = $action->getSearchedModels();
    } elseif (sfContext::hasInstance()) {
      $context = sfContext::getInstance();
    } else {
      return;
    }
   
    do {

      $status = $this->processResponses();
      $context->set('job_statuses', array('message_response_process' => $status));
      
    } while($status > 0);
    
  }
  
  /**
   * Method to import staff from an excel file.
   */
  private function importResponsesFromExcel($importFile)
  {
    // process the excel file and create a temporary table
    $this->processXlsImportFile($importFile);

    // start our iterator and initialize our select query
    $this->tempToRaw($this->buildTempSelectQuery());
  }

  
  /**
   * Method to set the classes' import specification.
   * Note: This intentionally excludes non-data fields (such as id, or success indicators); these
   * are set at a later point in the process.
   */
  protected function setImportSpec()
  {
    $importSpec['unique_id'] = array('type' => 'integer');
    $importSpec['last_name'] = array('type' => "string", 'length' => 64);
    $importSpec['first_name'] = array('type' => "string", 'length' => 64);
    $importSpec['label'] = array('type' => "string", 'length' => 32);
    $importSpec['address'] = array('type' => "string", 'length' => 255);
    $importSpec['status'] = array('type' => "string", 'length' => 128);
    $importSpec['time_stamp'] = array('type' => "string", 'length' => 24);
    $importSpec['response'] = array('type' => "string", 'length' => 16);
    
    // set the class property to the newly created 
    $this->importSpec = $importSpec;
  }

  /**
   * Method to clean a column name, removing leading and trailing spaces, special characters,
   * and replacing between-word spaces with an underscore. Will also throw if a zls is produced.
   * Note: This method is intentionally kept in the child class to allow customization if necesary
   *
   * @param string $columnName A string value representing a column name
   * @return string A properly formatted column name.
   */
  protected function cleanColumnName($columnName)
  {
    // keep this in case we need to throw an error
    $oldColumnName = $columnName;

    // trim once, for good measure, then replace spaces with underscores
    $columnName = trim(strtolower($columnName));
    $columnName = str_replace(' ', '_', trim(strtolower($columnName)));

    // many db's complain about numbers prepending column names
    $columnName = preg_replace('/^\d+/', '', $columnName);

    // filter out all special characters
    $columnName = preg_replace('/[\W]/', '', $columnName);

    // reduce any duplicate underscore pairs we may have created
    $columnName = preg_replace('/__+/', '_', $columnName);

    // lastly, in case this method created an unusable empty string, we throw (eg, fatal)
    if (strlen($columnName) == 0)
    {
      $errMsg = "Column name {$oldColumnName} could not be parsed.";
      $this->logCrit($errMsg, 1);
      throw new Exception($errMsg);
    }
    return $columnName;
  }

  /**
   * This method is an extension of the parent validate column headers method allowing
   * domain-specific header validation.
   * @param array $importFileHeaders An array of import file headers.
   * @param string $sheetName The name of the sheet being validated.
   * @return boolean A boolean indicating un/successful validation of column headers.
   */
  protected function validateColumnHeaders(array $importFileHeaders, $sheetName)
  {
    // DO NOT REMOVE THIS LINE UNLESS YOU KNOW WHAT YOU ARE DOING
    $validated = parent::validateColumnHeaders($importFileHeaders, $sheetName);

    return $validated;
  }

  /**
   * Method to dynamically build a (mostly) static tempSelectQuery
   * @return string Returns a string query
   */
  protected function buildTempSelectQuery()
  {
    $query = 'SELECT t.* ' .
         'FROM ' . $this->tempTable . ' AS t ' .
         'ORDER BY t.unique_id ASC, t.time_stamp ASC';

    return $query;
  }
  
  /**
   * @todo This data should belong in a configuration file (eg, YML)
   */
  protected function setImportComponents()
  {
    
    $this->importComponents[] = array( 'component' => 'message_response', 'throwOnError' => TRUE, 'method' => 'setEventStaffStatus');
  }


  /*
   * Method to set the event staff's allocation status
   */
  protected function setEventStaffStatus($throwOnError, Doctrine_Connection $conn)
  {
    // always start with any data maps we'll need so they're explicit
    $responses = array();

    // loop through our raw data and build our person language data
    $this->logInfo('Looping throw the message reponses and retrieving the most recent.');
    foreach ($this->importData as $rowId => $rowData)
    {
      $rd = $rowData['_rawData'];
      if (array_key_exists('resonse', $rd)) {
        $rVals = array(self::mapResponse($rd['response']), strtotime($rd['time_stamp']));
        $responses[$rd['unique_id']] = $rVals;
      }
    }

    // this step is necessary to avoid index constraints
    $coll = agDoctrineQuery::create()
      ->select('ess.*')
        ->from('agEventStaffStatus ess')
        ->whereIn(array_keys($responses))
          ->andWhere('EXISTS (SELECT s.id ' .
              'FROM agEventStaffStatus AS s ' .
              'WHERE s.event_staff_id = ess.event_staff_id ' .
              'HAVING MAX(s.time_stamp) = ess.time_stamp)')
        ->execute();

    // loop through all of our event staff and insert for those who already have a status
    foreach ($coll as $collId => &$rec)
    {
      // grab that particular response record
      $rVals = $responses[$rec['event_staff_id']];

      // @todo Check the timestamp output... this might need to be strtotime'd
      if ($rec['time_stamp'] == $rVals[1])
      {
        // if the timestamps were the same, update the response
        $eventMsg = 'Updating existing response for event staff id {' . $rec['event_staff_id'] .
          '}.';
        $this->logDebug($eventMsg);
        $rec['staff_allocation_status_id'] = $rVals[0];
      }
      else if ($rec['time_stamp'] < $rVals[1])
      {
        $eventMsg = 'Creating new status record for event staff id {' . $rec['event_staff_id'] .
          '}.';
        $this->logDebug($eventMsg);

        // if the db timestamp is older than the import one, make a new record and add it
        $nRec = new agEventStaffStatus();
        $nRec['event_staff_id'] = $rec['event_staff_id'];
        $nRec['time_stamp'] = $rVals[1];
        $nRec['staff_allocation_status_id'] = $rVals[0];
        $coll->add($nRec);
      }
      else
      {
        $eventMsg = 'Import timestamp {' . $rVals[1] . '} is older than the current timestamp in' .
          'the database {' . $rec['time_stamp'] . '}. Skipping insertion of older timestamp.';
        $this->logWarn($eventMsg);
      }

      // either way, we can be safely done this response
      unset($responses[$rec['event_staff_id']]);
    }

    // as a safety measure, always unset a referenced array value as soon as the loop is over
    unset($rec);
   
    // If we still have members in $responses, they were not event staff as of this execution
    // NOTE: Theoretically, an event staff person could have an event staff record but no
    // event staff status record (the source for the collection), however, operationally, this
    // case should not occur since all newly generated staff pool members are given a default
    // status.

    // Either way, we should warn the user that these records will not be updated
    $eventMsg = 'Event staff with IDs {' . implode(',', array_keys($responses)) . '} are no ' .
      'longer valid members of this event. Skipping response updates.';
    $this->logWarn($eventMsg);

    // here's the big to-do; let's save!
    $coll->save();
  }

  /**
   * Method to map a message response to a database-understood status type.
   * @param string $response
   */
  protected static function mapResponse( $response )
  {
    //@todo make this do something
    return $response;
  }
}