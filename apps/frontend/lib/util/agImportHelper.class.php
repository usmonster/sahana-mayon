<?php
/**
 * Abstract class to provide import helper methods
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUNY SPS
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
abstract class agImportHelper extends agEventHandler
{
  const       CONN_TEMP_READ = 'import_temp_read';
  const       CONN_TEMP_WRITE = 'import_temp_write';

  protected   $errThreshold,
              $_conn,
              $fileInfo,
              $tempTable,
              $tempTableOptions = array(),
              $importSpec = array(),
              $successColumn = '_import_success',
              $dynamicFieldType,
              $importHeaderStrictValidation = FALSE,
              $_PDO = array(),
              $defaultFetchMode = Doctrine_Core::FETCH_ASSOC,
              $iterData;

  abstract protected function setImportSpec();
  abstract protected function setDynamicFieldType();
  abstract protected function cleanColumnName($columnName);
  abstract protected function addDynamicColumns(array $importHeaders);

  /**
   * This class's constructor.
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   */
  public function __construct($tempTable, $logEventLevel = NULL)
  {
    parent::__construct($logEventLevel);

    // get our error threshold
    $this->errThreshold = intval(agGlobal::getParam('import_error_threshold'));

    // sets our temp table and builds our import specification
    $this->tempTable = $tempTable;
    $this->processImportSpec();
    $this->setDynamicFieldType();

    // Sets a new connection.
    $this->setConnections();

    // reset our iterators
    $this->resetIterData();
  }

  /**
   * Method to process and refine an import specification.
   */
  protected function processImportSpec()
  {
    // first get the basics from our helper (this must be set in the child classes)
    $this->setImportSpec();

    // now add some records-keeping fields we'll need across usages
    $this->importSpec['id'] = array('type' => 'integer', 'autoincrement' => true, 'primary' => true);
    $this->importSpec[$this->successColumn] = array('type' => "boolean", 'default' => TRUE);

    // we can't trust that they got it right so we're going to clean import spec columns
    foreach ($this->importSpec as $column => $value)
    {
      $cleanColumn = $this->cleanColumnName($column);
      if ($column != $cleanColumn)
      {
        unset($this->importSpec[$column]);
        $this->importSpec[$cleanColumn] = $value;
       
        $eventMsg = 'Import spec column name {' . $column . '} was not clean and was ' .
        'automatically renamed to {' . $cleanColumn . '}. It is recommended you correct this in' .
        'your import spec declaration.';
        $this->logWarn($eventMsg);
      }
    }
  }

  /**
   * Method to reset ALL iter data.
   */
  protected function resetIterData()
  {
    $this->iterData = array();
    $this->resetTempIterData();
  }

  /**
   * Method to reset the temp iterator data
   */
  protected function resetTempIterData()
  {
    $this->iterData['tempPosition'] = 0;
    $this->iterData['tempCount'] = 0;
  }

  /**
   * Method to get (and lazy load) a doctrine connection object
   * @return Doctrine_Connection A doctrine connection object
   */
  protected function getConnection($conn)
  {
    // Lazy load and return pdo connection.
    if (!isset($this->_conn)) { $this->setConnections(); }
    return $this->_conn[$conn];
  }
  
  /*
   * Method to set the import connection object property
   */
  protected function setConnections()
  {
    $this->_conn = array();
    $this->_conn[self::CONN_TEMP_READ] = Doctrine_Manager::connection(NULL, self::CONN_TEMP_READ);
    $this->_conn[self::CONN_TEMP_WRITE] = Doctrine_Manager::connection(NULL, self::CONN_TEMP_WRITE);
  }

  /**
   * Method to check against our error threshold and update whether we should continue or not
   */
  protected function checkErrThreshold()
  {
    // continue only if our error count is below our error threshold
    if ($this->getErrCount() > $this->errThreshold)
    {
      $errMsg = 'Import error threshold ({' . $this->errThreshold . '}) has been exceeded.';
      $this->logFatal($errMsg);
    }
  }

  /**
   * Method to execute a PDO query and optionally bind it to the class parameter.
   * @param <type> $conn A doctrine connection object
   * @param string $query A SQL query string
   * @param array $params An optional array of query parameters
   * @param string $fetchMode The PDO fetch mode to be used. Defaults to class property default.
   * @param <type> $pdoName An optional name for this query. If provided, it will save this object
   * in the _PDO collection.
   * @return Doctrine_Connection A PDO object after execution of the query.
   */
  protected function executePdoQuery( Doctrine_Connection $conn,
                                      $query,
                                      $params = array(),
                                      $fetchMode = NULL,
                                      $pdoName = NULL)
  {
    // first prepare the sql statement
    $qStatement = $conn->prepare($query);

    // then execute the query
    $pdo = $qStatement->execute($params);

    // set fetch mode the the one we are passed or our default
    if (is_null($fetchMode)) { $fetchMode = $this->defaultFetchMode; }
    $pdo->setFetchMode($fetchMode);

    // only save those pdo queries we have decided to name in the _PDO array
    // set this by reference so we can expire the $pdo variable and persist the object / connection
    if (! is_null($pdoName))
    {
      $this->_PDO[$pdoName] =& $pdo;
    }

    return $pdo ;
  }

  /**
   * Method to lazily load and return the pathinfo for $importFile
   *
   * @param string $importFile The path for the import file.
   * @return array Returns pathinfo for importFile
   * @todo Determine the type of importFile
   */
  protected function getFileInfo($importFile)
  {
    if (! isset($this->fileInfo))
    {
      $this->fileInfo = pathinfo($importFile);
    }
    return $this->fileInfo;
  }

  protected function processXlsImportFile($importFile)
  {
    // Validate the uploaded files Excel 2003 extention
    $this->getFileInfo($importFile);
    if (strtolower($this->fileInfo['extension']) <> 'xls')
    {
      $errMsg = '{' . $this->fileInfo['basename'] .
        '} is not a Microsoft Excel 2003 ".xls" workbook.';
      $this->logFatal($errMsg);
    }

    // open the import file
    $this->logInfo('Opening the import file.');
    $xlsObj = new spreadsheetExcelReader($importFile, FALSE);
    $this->logInfo('Successfully opened the import file.');

    // Get some info about the workbook's composition
    $numSheets = count($xlsObj->sheets);
    $this->logInfo('Number of worksheets found: {' . $numSheets . '}');

    $numRows = $xlsObj->rowcount($sheet_index = 0);
    $numCols = $xlsObj->colcount($sheet_index = 0);

    // Create a simplified array from the worksheets
    for ($sheet = 0; $sheet < $numSheets; $sheet++) {
      $importRow = 0;
      $importFileData = array();

      // Get the sheet name
      $this->logInfo('Opening worksheet {' . $sheetName . '}');
      $sheetName = $xlsObj->boundsheets[$sheet]['name'];

      // We don't import sheets named "Lookup" so we'll skip the remainder of this loop
      if (strtolower($sheetName) == 'lookup')
      {
        $this->logInfo('Ignoring {' . $sheetName . '} worksheet');
        continue;
      }

      // Grab column headers at the beginning of each sheet.
      $currentSheetHeaders = array_values($xlsObj->sheets[$sheet]['cells'][1]);

      // clean the column headers to ensure consistency
      $this->logInfo('Cleaning worksheet headers');
      foreach ($currentSheetHeaders as $index => &$header)
      {
        $header = $this->cleanColumnName($header);
      }

      // Check for consistant column header in all data worksheets.  Use the column header from
      // the first worksheet as the import column header for all data worksheets.
      if ($sheet == 0)
      {
        // Extend import spec headers with dynamic staff resource requirement columns from xls file.
        $this->addDynamicColumns($currentSheetHeaders);
        $this->logInfo('Creating temporary import table {' . $this->tempTable . '}');
        $this->createTempTable();

        // @todo Remove this line if truly unnecessary
        // unset($this->importSpec['success']);
      }

      $this->logInfo('Validating column headers of import file.');
      if ($this->validateColumnHeaders($currentSheetHeaders, $sheetName)) {
        $this->events[] = array("type" => "OK", "message" => "Valid column headers found.");
      } else {
        $this->events[] = array("type" => "ERROR", "message" => "Unable to import file due to validation error.");
        return false;
      }

      for ($row = 2; $row <= $numRows; $row++) {

        for ($col = 1; $col <= $numCols; $col++) {

          $colName = str_replace(' ', '_', strtolower($xlsObj->val(1, $col, $sheet)));

          $val = $xlsObj->raw($row, $col, $sheet);
          if (!($val)) {
            $val = $xlsObj->val($row, $col, $sheet);
          }
          $importFileData[$importRow][$colName] = trim($val);
        }
        // Increment import array row
        $importRow++;
      }

      $this->events[] = array("type" => "INFO", "message" => "Inserting records into temp table.");
      $this->saveImportTemp($importFileData);
    }

    $this->events[] = array("type" => "OK", "message" => "Done inserting temp records.");
    return true;
 
  }

  /**
   * Method to drop temporary table
   * @todo Replace the unweildy handling of the exceptions properly check for existence
   */
  protected function dropTempTable()
  {
    // get a connection
    $conn = $this->getConnection(self::CONN_TEMP_WRITE);

    // drop the table
    try
    {
      // uses the Doctrine_Export methods see Doctrine_Export api for more details
      $conn->export->dropTable($this->tempTable);

      // log this info event
      $eventMsg = 'Dropped temporary table {' . $this->tempTable . '}';
      $this->logOk($eventMsg);
    }
    catch(Doctrine_Connection_Exception $e)
    {
      // we only want to silence 'no such table' errors
      if ($e->getPortableCode() !== Doctrine_Core::ERR_NOSUCHTABLE)
      {
        $this->logFatal('Failed to drop temp table {' . $this->tempTable . '}');
      }
      else
      {
        $this->logInfo('Temp table {' . $this->tempTable . '} does not exist. Skipping drop.');
      }
    }
  }

  /**
   * Method to create an import temp table.
   */
  protected function createTempTable()
  {
    // get a connection
    $conn = $this->getConnection(self::CONN_TEMP_WRITE);

    // Drop temp if it exists
    $this->dropTempTable();

    // Create the table
    try
    {
      // uses the Doctrine_Export methods see Doctrine_Export api for more details
      $conn->export->createTable($this->tempTable, $this->importSpec, $this->tempTableOptions);
      $this->logOk('Successfully created temp table {' . $this->tempTable .'}.');
    }
    catch (Doctrine_Exception $e)
    {
      $this->logFatal('Error creating temp table ({' . $this->tempTable . '} for import.');
    }
  }

  /**
   * This method provides simple validation of import file column headers. It is intended to be
   * extended by its child classes which may provide more advanced validation.
   * 
   * @param array $importFileHeaders An array of import file headers.
   * @param string $sheetName The name of the sheet being validated.
   * @return boolean A boolean indicating un/successful validation of column headers.
   */
  abstract protected function validateColumnHeaders(array $importFileHeaders, $sheetName)
  {
    // Check if import file header is empty
    if (empty($importFileHeaders))
    {
      $this->logErr($errMsg)
      $this->events[] = array("type" => "ERROR",
        "message" => "Worksheet \"$sheetName\" is missing column headers.");
      return FALSE;
    }

    // Cache the import header specification
    $importSpecHeaders = array_keys($this->importSpec);

    // The import spec will start with an ID column. Shift off of it.
    $idColumn = array_shift($importSpecHeaders);
    $importSpecDiff = array_diff($importSpecHeaders, $importFileHeaders);

    if (empty($importSpecDiff)) {
      return TRUE;
    } else {
      $this->events[] = array("type" => "ERROR", "message" => "Missing required columns.");

      foreach ($importSpecDiff as $missing) {
        $this->events[] = array("type" => "ERROR", "message" => "Column header \"$missing\" missing.");
      }
      return FALSE;
    }
  }

}