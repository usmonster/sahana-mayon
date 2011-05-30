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

  protected   $fileInfo,
              $tempTable,
              $tempTableOptions = array(),
              $importSpec = array(),
              $requiredImportColumns = array(),
              $successColumn = '_import_success',
              $idColumn = 'id',
              $excelImportBatchSize = 2500,
              $dynamicFieldType,
              $importHeaderStrictValidation = FALSE,
              $iterData;

  /**
   * @var agEventHandler An agEventHandler instance
   */
  protected   $eh;

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

    $this->eh = new agEventHandler($logEventLevel);

    // get our error threshold
    $this->errThreshold = intval(agGlobal::getParam('import_error_threshold'));

    // sets our temp table and builds our import specification
    $this->tempTable = $tempTable;
    $this->processImportSpec();
    $this->setDynamicFieldType();

    // reset our iterators
    $this->resetIterData();
  }

  /**
   * Method to set the import connection object property
   */
  protected function setConnections()
  {
    $this->_conn = array();

    $adapter = Doctrine_Manager::connection()->getDbh();
    $this->_conn[self::CONN_TEMP_READ] = Doctrine_Manager::connection($adapter, self::CONN_TEMP_READ);
    $this->_conn[self::CONN_TEMP_WRITE] = Doctrine_Manager::connection($adapter, self::CONN_TEMP_WRITE);
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
   * Method to process and refine an import specification.
   */
  protected function processImportSpec()
  {
    // first get the basics from our helper (this must be set in the child classes)
    $this->setImportSpec();

    // now add some records-keeping fields we'll need across usages
    $this->requiredImportColumns[$this->idColumn] = array('type' => 'integer',
      'autoincrement' => true, 'primary' => true);
    $this->requiredImportColumns[$this->successColumn] = array('type' => "boolean",
      'default' => TRUE);

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

  public function processXlsImportFile($importFile)
  {
    // ignores php warnings generated by old, crufty lib (Spreadsheet_Excel_Reader)
    $errorlevel = error_reporting();
//    error_reporting($errorlevel & ~E_NOTICE & ~E_DEPRECATED);
//    require_once(sfConfig::get('sf_app_lib_dir') . '/util/excel_reader2.php');
//    // restores original PHP error level
//    error_reporting($errorlevel);
    // Validates the uploaded files Excel 2003 extention
    $this->getFileInfo($importFile);
    if (strtolower($this->fileInfo['extension']) <> 'xls') {
      $errMsg = '{' . $this->fileInfo['basename'] .
        '} is not a Microsoft Excel 2003 ".xls" workbook.';
      $this->eh->logEmerg($errMsg);
    }

    // opens the import file
    $this->eh->logInfo('Opening the import file (' . $this->fileInfo['basename'] . ').');

    // ignores php warnings generated by old, crufty lib (Spreadsheet_Excel_Reader)
    error_reporting($errorlevel & ~E_NOTICE & ~E_DEPRECATED);

    $xlsObj = new SpreadsheetExcelReader($importFile, FALSE);

    // restores original PHP error level
    error_reporting($errorlevel);

    $this->eh->logInfo('Successfully opened the import file (' . $this->fileInfo['basename'] . ').');

    // Get some info about the workbook's composition
    $numSheets = count($xlsObj->sheets);
    $this->eh->logInfo('Number of worksheets found: {' . $numSheets . '}');

    // Create a simplified array from the worksheets
    for ($sheet = 0; $sheet < $numSheets; $sheet++) {
      // Get the sheet name
      $this->eh->logInfo('Opening worksheet {' . $sheet . '}');
      $sheetName = $xlsObj->boundsheets[$sheet]['name'];

      // We don't import sheets named "Lookup" so we'll skip the remainder of this loop
      if (strtolower($sheetName) == 'lookup')
      {
        $this->eh->logInfo('Ignoring {' . $sheetName . '} worksheet');
        continue;
      }

      // Grab column headers at the beginning of each sheet.
      $currentSheetHeaders = $xlsObj->sheets[$sheet]['cells'][1];

      // clean the column headers to ensure consistency
      $this->eh->logInfo('Cleaning worksheet headers');
      foreach ($currentSheetHeaders as $index => &$header)
      {
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

      $this->eh->logInfo('Validating column headers for sheet {' . $sheetName .'}.');
      if ($this->validateColumnHeaders($currentSheetHeaders, $sheetName))
      {
        $this->eh->logInfo('Valid column headers found!');
      }
      else
      {
        if ($this->importHeaderStrictValidation)
        {
          $errMsg = 'Unable to import file due to failed validation of import headers. (Strict ' .
            'header validation is currently enforced!)';
          $this->eh->logEmerg($errMsg);
        }
        else
        {
          $errMsg = 'Import sheet {' . $sheetName . '} failed column header validation. Skipping ' .
            'import of this sheet. (Strict header validation is not currently enforced!)' ;
          $this->eh->logErr($errMsg);
          continue;
        }
      }

      // prepare our insert query statement
      $query = $this->prepareImportTemp();

      // Declare our initial batch data
      $batches = intval(ceil(($numRows / $this->excelImportBatchSize)));
      $batchStart = 2;
      $batchEnd = $this->excelImportBatchSize;
      if ($batchEnd > $numRows) {
        $batchEnd = $numRows;
      }

      // start by looping our batches
      for ($batch = 1; $batch <= $batches; $batch++)
      {
        $this->eh->logInfo('Processing batch {' . $batch . '} of {' . $batches . '}.');
        // each batch clears the import data array
        $importFileData = array();

        // begin adding rows and continue to the end for this batch
        for ($row = $batchStart; $row <= $batchEnd; $row++)
        {
          $this->eh->logDebug('Reading row {' . $row . '} into import data array.');

          // used to tell if the row is empty
          $notNull = FALSE;

          // helpful little declarations
          $importRow = array();

          // then loop the columns
          for ($col = 1; $col <= $numCols; $col++) {
            // try to grab the raw value
            $val = $xlsObj->raw($row, $col, $sheet);
            if (!($val)) {
              // failing that, grab its formatted value
              $val = $xlsObj->val($row, $col, $sheet);
            }

            // clean the data a little, null out long values and zero length strings
            $val = trim($val);
            if ($val == '' || is_null($val)) {
              $val = NULL;
            } elseif (strlen(strval($val)) > $this->importSpec[$currentSheetHeaders[$col]]['length']) {
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
          }

          // check for empty rows early to prevent
          if (! $notNull)
          {
            $this->eh->logWarning('Empty row found at sheet {' . $sheet . '} row {' . $row . '}. Skipping.');
          }
          else
          {
            $importFileData[$row] = $importRow;
          }
        }

        // process this batch
        $this->eh->logInfo('Successfully loaded batch {' . $batch . '} from file, now inserting into ' .
          'temp table {' . $this->tempTable . '}');

        $inserted = $this->saveImportTempIter($importFileData, $query);
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

    return ($this->eh->getErrCount() == 0) ? TRUE : FALSE ;
  }

  /**
   * Method to prepare the import/temp query
   * @return Doctrine_Connection_Statement A prepared query statement.
   * @todo Make this capable of using positionals instead
   */
  private function prepareImportTemp()
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
    $conn = $this->getConnection(self::CONN_TEMP_WRITE);
    $query = $conn->prepare($sql);

    // return the prepared query
    return $query;
  }

  /**
   * Method to iteratively save import data to a temporary table. Note: this method is PDO-safe and
   * should work across database engines but is noticeably slower than MySQL multi-inserts.
   * @param array $importDataSet An array of import data keyed by rowId and by column name.
   * @param PDOStatement $insertQuery A prepared PDO insert statement
   * @return int The number of records successfully inserted into the temporary table.
   */
  private function saveImportTempIter(array $importDataSet,
                                      Doctrine_Connection_Statement $insertQuery)
  {
    // first check to see if it's even worth running
    if (empty($importDataSet))
    {
      $this->eh->logWarning('Cannot save empty dataset to temp table.');
     return 0;
    }

    // beginning a transaction should improve performance
    $conn = $this->getConnection(self::CONN_TEMP_WRITE);
    $conn->beginTransaction();

    // set up some initial vars
    $err = FALSE;
    $inserted = 0;

    // loop through the import data set and execute the prepared pdo query for each
    foreach ($importDataSet as $row) {
      try {
        $insertQuery->execute($row);
        $inserted++;
      } catch (Exception $e) {
        // in the event of an insert error, don't continue
        $errMsg = 'Failed to insert to temp table with data (' . implode(',', $row) . '). ' .
          "\n\n" . $e->getMessage();
        $this->eh->logErr($errMsg, count($importDataSet));
        $err = TRUE;
        break;
      }
    }

    // if no problems occurred so far, try to commit
    if (!$err) {
      try {
        $conn->commit();
      } catch (Exception $e) {
        $errMsg = 'Committing temporary table import failed.' . "\n" . $e->getMessage();
        $this->eh->logCrit($errMsg);
        $err = TRUE;
      }

      // success! log it and return the number of transactions performed
      $this->eh->logInfo('Successfully committed ' . $inserted . ' new records to the temp table.');
      return $inserted;
    }

    // in the event that we had any failures, rollback and return the zero change count
    if ($err) {
      $conn->rollback();
      return 0;
    }
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
      $this->eh->logNotice($eventMsg);
    }
    catch(Doctrine_Connection_Exception $e)
    {
      // we only want to silence 'no such table' errors
      if ($e->getPortableCode() !== Doctrine_Core::ERR_NOSUCHTABLE)
      {
        $this->eh->logEmerg('Failed to drop temp table {' . $this->tempTable . '}');
      }
      else
      {
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
      $this->eh->logNotice('Successfully created temp table {' . $this->tempTable .'}.');
    }
    catch (Doctrine_Exception $e)
    {
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
  private function validateColumnHeaders(array $importFileHeaders, $sheetName)
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
    }
    else
    {
      $this->eh->logErr('Error processing sheet headers: Missing required columns.');
      
      foreach ($importSpecDiff as $missing)
      {
        $this->eh->logWarning('Column header {' . $missing . '} is missing.');
      }
      return FALSE;
    }
  }

}