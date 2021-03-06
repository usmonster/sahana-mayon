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
 */
abstract class agImportHelper extends agPdoHelper
{
  const CONN_TEMP_READ = 'import_temp_read';
  const CONN_TEMP_WRITE = 'import_temp_write';

  protected $fileInfo,
  $tempTable,
  $tempTableOptions = array(),
  $importSpec = array(),
  $specStrLengths = array(),
  $requiredImportColumns = array(),
  $successColumn = '_import_success',
  $idColumn = 'id',
  $excelImportBatchSize = 2500,
  $dynamicFieldType,
  $importHeaderStrictValidation = FALSE,
  $iterData,
  $maxBatchTime;

  /**
   * @var agEventHandler An agEventHandler instance
   */
  protected $eh;

  abstract protected function setImportSpec();

  abstract protected function setDynamicFieldType();

  abstract protected function cleanColumnName($columnName);

  abstract protected function addDynamicColumns(array $importHeaders);

  /**
   * Method to initialize this class
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   */
  protected function __init($tempTable, $logEventLevel)
  {
    if (agGlobal::getParam('clear_cache_on_import') == 1) {
      apc_clear_cache();
      apc_clear_cache('user');
    }

    $this->maxBatchTime = agGlobal::getParam('bulk_operation_max_batch_time');

    $this->eh = new agEventHandler($logEventLevel, agEventHandler::EVENT_NOTICE,
            sfContext::getInstance());

    // get our error threshold
    $this->errThreshold = intval(agGlobal::getParam('import_error_threshold'));

    // sets our temp table and builds our import specification
    $this->tempTable = $tempTable;
    $this->processImportSpec();
    $this->setDynamicFieldType();

    // reset our iterators
    $this->resetIterData();

    // memory usage
    $this->peakMemory = 0;
  }

  /**
   * Method to set the import connection object property
   */
  protected function setConnections()
  {
    $dm = Doctrine_Manager::getInstance();

    // always re-parent, then 'copy' the connection
    $dm->setCurrentConnection('doctrine');
    $adapter = Doctrine_Manager::connection()->getDbh();
    $this->_conn[self::CONN_TEMP_READ] = Doctrine_Manager::connection($adapter, self::CONN_TEMP_READ);

    // always re-parent, then 'copy' the connection
    $dm->setCurrentConnection('doctrine');
    $adapter = $dm->getCurrentConnection()->getDbh();
    $conn = Doctrine_Manager::connection($adapter, self::CONN_TEMP_WRITE);
    $conn->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, TRUE);
    $conn->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_AUTOCOMMIT, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_LOAD_REFERENCES, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_VALIDATE, FALSE);
    $conn->setAttribute(Doctrine_Core::ATTR_CASCADE_SAVES, FALSE);
    $this->_conn[self::CONN_TEMP_WRITE] = $conn;

    // reset our default connection
    $dm->setCurrentConnection('doctrine');
  }

  /**
   * This classes' destructor.
   */
  public function __destruct()
  {
    // the parent's destructor will rollback any oustanding open transactions and close conns
    parent::__destruct();

    // removes the temporary file
    $file = $this->fileInfo['dirname'] . DIRECTORY_SEPARATOR . $this->fileInfo['basename'];
    if (!@unlink($file)) {
      $this->eh->logAlert('Failed to delete the {' . $this->fileInfo['basename'] . '} import file.');
    } else {
      $this->eh->logInfo('Successfully deleted the {' . $this->fileInfo['basename'] . '} import file.');
    }
  }

  /**
   *
   * @param array $columnDefinition An array of column definition information
   *   array('type' => 'integer', 'length' => 6)
   *
   * Note that lengths for integers are calculated using Doctrine lengths (eg, 6 = BIGINT)
   * @return integer The string length for the expected value
   */
  protected static function getSpecificationStrLen(array $columnDefinition) {
    // string length / type comparison auto-magic
    switch($columnDefinition['type']) {
      case 'integer':
      case 'int':
        return strlen(pow(256, $columnDefinition['length'])) + 1;
        break;
      case 'decimal':
      case 'dec':
        return $columnDefinition['length'] + 2;
        break;
      default:
        return $columnDefinition['length'];
        break;
    }
  }

  /**
   * Method to process and refine an import specification.
   */
  protected function processImportSpec()
  {
    // first get the basics from our helper (this must be set in the child classes)
    $this->setImportSpec();

    // now add some records-keeping fields we'll need across usages
    $this->requiredImportColumns[$this->idColumn] = array('type' => 'integer', 'length' => 20,
      'autoincrement' => true, 'primary' => true);
    $this->requiredImportColumns[$this->successColumn] = array('type' => "boolean");

    // we can't trust that they got it right so we're going to clean import spec columns
    foreach ($this->importSpec as $column => $value) {
      $cleanColumn = $this->cleanColumnName($column);
      if ($column != $cleanColumn) {
        unset($this->importSpec[$column]);
        $this->importSpec[$cleanColumn] = $value;

        $eventMsg = 'Import spec column name {' . $column . '} was not clean and was ' .
            'automatically renamed to {' . $cleanColumn . '}. It is recommended you correct this in' .
            'your import spec declaration.';
        $this->eh->logWarning($eventMsg);
      }

      $this->specStrLengths[$cleanColumn] = self::getSpecificationStrLen($value);
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
    $this->iterData['tempErrCt'] = 0;
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
    if (!isset($this->fileInfo)) {
      $this->fileInfo = pathinfo($importFile);
    }
    return $this->fileInfo;
  }

  /**
   * Method to get the iterData array
   * @return array The iterdata array (containing import statistics)
   */
  public function getIterData()
  {
    return $this->iterData;
  }

  /**
   * Returns the peak memory
   *
   * @return type int
   */
  public function getPeakMemoryUsage()
  {

    // Take some peak memory metrics
    $memoryUsage = memory_get_usage();
    if ($memoryUsage > $this->peakMemory) {
      $this->peakMemory = $memoryUsage;
    }

    return $this->peakMemory;
  }

  public function processXlsImportFile($importFile)
  {
    // Validates the uploaded files Excel 2003 extention
    $this->getFileInfo($importFile);
    if (strtolower($this->fileInfo['extension']) <> 'xls') {
      $errMsg = '{' . $this->fileInfo['basename'] .
          '} is not a Microsoft Excel 2003 ".xls" workbook.';
      $this->eh->logEmerg($errMsg);
    }

    // opens the import file
    $this->eh->logInfo('Opening the import file (' . $this->fileInfo['basename'] . ').');

    // Get the current PHP error level
    $errorlevel = error_reporting();

    // ignores php warnings generated by old, crufty lib (Spreadsheet_Excel_Reader)
    error_reporting($errorlevel & ~E_NOTICE & ~E_DEPRECATED);

    $xlsObj = new spreadsheetExcelReader($importFile, FALSE);

    // restores original PHP error level
    error_reporting($errorlevel);

    $this->eh->logInfo('Successfully opened the import file (' . $this->fileInfo['basename'] . ').');

    // Get some info about the workbook's composition
    $numSheets = count($xlsObj->sheets);
    $this->eh->logInfo('Number of worksheets found: {' . $numSheets . '}');

    // Create a simplified array from the worksheets
    for ($sheet = 0; $sheet < $numSheets; $sheet++) {
      set_time_limit($this->maxBatchTime);

      // Get the sheet name
      $this->eh->logInfo('Opening worksheet {' . $sheet . '}');
      $sheetName = $xlsObj->boundsheets[$sheet]['name'];

      // We don't import sheets named "Lookup" so we'll skip the remainder of this loop
      if (strtolower($sheetName) == 'lookup') {
        $this->eh->logInfo('Ignoring {' . $sheetName . '} worksheet');
        continue;
      }

      // Check whether the sheet has a header row.
      if (isset($xlsObj->sheets[$sheet]['cells'][1]))
      {
        // Grab column headers at the beginning of each sheet.
        $currentSheetHeaders = $xlsObj->sheets[$sheet]['cells'][1];
      }
      else
      {
        $eventMsg = "This worksheet " . $sheetName . " does not have a valid column headers for data import.";
        $errCount = 1;
        $this->eh->logEmerg($eventMsg, $errCount);
      }

      // clean the column headers to ensure consistency
      $this->eh->logInfo('Cleaning worksheet headers');
      foreach ($currentSheetHeaders as $index => &$header) {
        $header = $this->cleanColumnName($header);
      }
      unset($header);

      // count our total rows (specific to each sheet)
      $numRows = $xlsObj->rowcount($sheet);

      // Check for consistant column header in all data worksheets.  Use the column header from
      // the first worksheet as the import column header for all data worksheets.
      if ($sheet == 0) {
        // count our columns (only needed once since first sheet is treated as template)
        $numCols = $xlsObj->colcount($sheet);

        // Extend import spec headers with dynamic staff resource requirement columns from xls file.
        $this->addDynamicColumns($currentSheetHeaders);

        // Might seem weird, but we do this to ensure that column orders are consistent in import
        // spec AND the sheet headers (makes some things faster)
        $importSpec = array();
        foreach ($currentSheetHeaders as $header) {
          $importSpec[$header] = $this->importSpec[$header];
        }
        $this->importSpec = $importSpec;
        unset($header);
        unset($importSpec);

        // create the temp table
        $this->eh->logInfo('Creating temporary import table {' . $this->tempTable . '}');
        $this->createTempTable();
      }

      $this->eh->logInfo('Validating column headers for sheet {' . $sheetName . '}.');
      if ($this->validateColumnHeaders($currentSheetHeaders, $sheetName)) {
        $this->eh->logInfo('Valid column headers found!');
      } else {
        if ($this->importHeaderStrictValidation) {
          $errMsg = 'Unable to import file due to failed validation of import headers. (Strict ' .
              'header validation is currently enforced!)';
          $this->eh->logEmerg($errMsg);
        } else {
          $errMsg = 'Import sheet {' . $sheetName . '} failed column header validation. Skipping ' .
              'import of this sheet. (Strict header validation is not currently enforced!)';
          $this->eh->logErr($errMsg);
          continue;
        }
      }

      // Declare our initial batch data
      $batches = intval(ceil(($numRows / $this->excelImportBatchSize)));
      $batchStart = 2;
      $batchEnd = $this->excelImportBatchSize;
      if ($batchEnd > $numRows) {
        $batchEnd = $numRows;
      }

      // start by looping our batches
      for ($batch = 1; $batch <= $batches; $batch++) {
        $this->eh->logInfo('Processing batch {' . $batch . '} of {' . $batches . '}.');
        // each batch clears the import data array
        $importFileData = array();

        // begin adding rows and continue to the end for this batch
        for ($row = $batchStart; $row <= $batchEnd; $row++) {
          $this->eh->logDebug('Reading row {' . $row . '} into import data array.');

          // used to tell if the row is empty
          $notNull = FALSE;

          // helpful little declarations
          $importRow = array();

          // then loop the columns
          for ($col = 1; $col <= $numCols; $col++) {
            // try to grab the raw value
            $val = $xlsObj->raw($row, $col, $sheet);
            if (!$val) {
              // failing that, grab its formatted value
              $val = $xlsObj->val($row, $col, $sheet);
            }

            // clean the data a little, null out long values and zero length strings
            $val = trim($val);
            if ($val == '' || is_null($val)) {
              $val = NULL;
            } elseif (strlen(strval($val)) > $this->specStrLengths[$currentSheetHeaders[$col]]) {
              $eventMsg = 'Value in sheet {' . $sheet . '} row {' . $row . '} column {' .
                  $currentSheetHeaders[$col] . '} is too long and was set to NULL.';
              $this->eh->logWarning($eventMsg);
              $val = NULL;
            } else {
              $notNull = TRUE;
            }

            // add the data, either way, to our importRow variable using the column name we picked up
            // off the first sheet
            $importRow[$currentSheetHeaders[$col]] = $val;
            unset($val);
          }

          // check for empty rows early to prevent
          if (!$notNull) {
            $this->eh->logWarning('Empty row found at sheet {' . $sheet . '} row {' . $row . '}. Skipping.');
          } else {
            $importFileData[$row] = $importRow;
          }
        }

        // process this batch
        $this->eh->logInfo('Successfully loaded batch {' . $batch . '} from file, now inserting into ' .
            'temp table {' . $this->tempTable . '}');

        $inserted = $this->saveImportTempIter($importFileData);
        $this->iterData['tempCount'] += $inserted;

        // up our batch counters
        $batchStart = $batchEnd + 1;
        $batchEnd = $batchStart + $this->excelImportBatchSize;
        if ($batchEnd > $numRows) {
          $batchEnd = $numRows;
        }
      }
    }

    // Log our success and return T/F based on whether or not any non-fatal errors occurred
    $okMsg = 'Completed insertion of ' . $this->iterData['tempCount'] . ' rows from the import ' .
        'file to the temporary table.';
    $this->eh->logNotice($okMsg);

    return ($this->eh->getErrCount() == 0) ? TRUE : FALSE;
  }

  /**
   * Method to prepare the import/temp query
   * @param Doctrine_Connection A doctrine connection object
   * @return Doctrine_Connection_Statement A prepared query statement.
   * @todo Make this capable of using positionals instead
   */
  private function prepareImportTemp(Doctrine_Connection $conn)
  {
    // loop through the import spec and build our columns / values blocks
    $cols = '';
    $vals = '';
    foreach ($this->importSpec as $column => $spec) {
      $cols = $cols . '`' . $column . '`, ';
      $vals = $vals . ':' . $column . ', ';
    }

    // this removews a trailing comma from the last statement
    $cols = substr($cols, 0, -2);
    $vals = substr($vals, 0, -2);

    // build our prepared sql statement
    $sql = 'INSERT INTO ' . $this->tempTable . ' (' . $cols . ') VALUES (' . $vals . ');';

    // grab our connection and prepare the query
    $query = $conn->prepare($sql);

    // return the prepared query
    return $query;
  }

  /**
   * Method to iteratively save import data to a temporary table. Note: this method is PDO-safe and
   * should work across database engines but is noticeably slower than MySQL multi-inserts.
   * @param array $importDataSet An array of import data keyed by rowId and by column name.
   * @return int The number of records successfully inserted into the temporary table.
   */
  private function saveImportTempIter(array $importDataSet)
  {
    // first check to see if it's even worth running
    if (empty($importDataSet)) {
      $this->eh->logWarning('Cannot save empty dataset to temp table.');
      return 0;
    }

    // prepare our insert query statement
    $conn = $this->getConnection(self::CONN_TEMP_WRITE);
    $conn->beginTransaction();
    $query = $this->prepareImportTemp($conn);


    // set up some initial vars
    $inserted = 0;
    $errCt = 0;

    // loop through the import data set and execute the prepared pdo query for each
    foreach ($importDataSet as $rowId => $row) {
      try {
        $query->execute($row);
        $inserted++;
      } catch (Exception $e) {
        // in the event of an insert error, don't continue
        $errMsg = 'Failed to insert data in row ' . $rowId . ' to temp table.';
        $this->eh->logErr($errMsg, 1, FALSE);
        $errCt++;
      }
    }

    try {
      $conn->commit();
      $this->eh->logInfo('Successfully committed ' . $inserted . ' new records to the temp table.');
    } catch (Exception $e) {
      $errMsg = 'Failed to commit records in batch ending with row ' . $rowId;
      $errCt = count($importDataSet);
      $this->eh->logErr($errMsg, $errCt, FALSE);
      $inserted = 0;
    }

    $this->iterData['tempErrCt'] += $errCt;

    // if no problems occurred so far, try to commit
    return $inserted;
  }

  /**
   * Method to drop temporary table
   * @todo Replace the unwieldy handling of the exceptions properly check for existence
   */
  protected function dropTempTable()
  {
    // get a connection
    $conn = $this->getConnection(self::CONN_TEMP_WRITE);

    // drop the table
    try {
      // uses the Doctrine_Export methods see Doctrine_Export api for more details
      $conn->export->dropTable($this->tempTable);

      // log this info event
      $eventMsg = 'Dropped temporary table {' . $this->tempTable . '}';
      $this->eh->logInfo($eventMsg);
    } catch (Doctrine_Connection_Exception $e) {
      // we only want to silence 'no such table' errors
      if ($e->getPortableCode() !== Doctrine_Core::ERR_NOSUCHTABLE) {
        $this->eh->logEmerg('Failed to drop temp table {' . $this->tempTable . '}');
      } else {
        $this->eh->logInfo('Temp table {' . $this->tempTable . '} does not exist. Skipping drop.');
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

    // add our required columns
    $importSpec = $this->importSpec + $this->requiredImportColumns;

    // Create the table
    try {
      // uses the Doctrine_Export methods see Doctrine_Export api for more details
      $conn->export->createTable($this->tempTable, $importSpec, $this->tempTableOptions);
      $this->eh->logInfo('Successfully created temp table {' . $this->tempTable . '}.');
    } catch (Doctrine_Exception $e) {
      $this->eh->logEmerg('Error creating temp table ({' . $this->tempTable . '} for import.');
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
  protected function validateColumnHeaders(array $importFileHeaders, $sheetName)
  {
    // Check if import file header is empty
    if (empty($importFileHeaders)) {
      $errMsg = 'Worksheet {' . $sheetName . '} is missing column headers.';
      $this->eh->logErr($errMsg);
      return FALSE;
    }

    // just grab the headers
    $importSpecHeaders = array_keys($this->importSpec);

    // process a diff of the file headers and spec headers
    $importSpecDiff = array_diff($importSpecHeaders, $importFileHeaders);

    // return true / false and return info as appropriate
    if (empty($importSpecDiff)) {
      return TRUE;
    } else {
      $this->eh->logErr('Error processing sheet headers: Missing required columns.');

      foreach ($importSpecDiff as $missing) {
        $this->eh->logWarning('Column header {' . $missing . '} is missing.');
      }
      return FALSE;
    }
  }

}
