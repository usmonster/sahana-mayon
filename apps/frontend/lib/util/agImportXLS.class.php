<?php

/**
 * Facility Import Class
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Clayton Kramer, CUNY SPS
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 * @todo Make this class more universal and capable of importing staff and other data
 * @todo Improve class construct and destruct functions
 * @todo Utilize agDatabaseHelper.class.php to warn of MySQL dependency
 * @todo Need better exception handling for file IO
 * @todo Re-factor validateColumnHeaers() so it uses the Excel object.
 *  This will allow it to be used on each worksheet.
 * @todo Remove the debug dump functions once temp_table >> Doctrine_database
 *  functions are created.
 * @todo Re-factor event messages by creating specific function for adding
 *  array elements to $this->events
 * @todo Add debug levels feature
 * @todo Improve Doctrine MySQL PDO exception reporting
 */
class AgImportXLS
{
  public $importSpec;
  public $staffRequirementFieldType;// = array('type' => "integer");
  // Public variables declared here
  public $events = array();
  public $numRecordsImported = 0;
  public $tempTable;

  function __construct()
  {

    $this->name = "agImportXLS";
  }

  function __destruct()
  {

    $file = $this->fileInfo["dirname"] . '/' . $this->fileInfo["basename"];
    if (!@unlink($file)) {
      $this->events[] = array("type" => "ERROR", "message" => $php_errormsg);
    } else {
      $this->events[] = array('type' => 'OK', "message" => "Deleted {$this->fileInfo['basename']} upload file.");
    }
  }

  /**
   * Method to clean column headers, removing leading and trailing spaces and replacing between-word
   * spaces with an underscore.
   * 
   * @param mix $value The value of the array.
   * @param mix $key The key of the array.
   */
  protected function cleanColumnHeaders($columnHeaders)
  {
    foreach ($columnHeaders as $index => $column)
    {
      $columnHeaders[$index] = str_replace(' ', '_', trim(strtolower($column)));
    }
    return $columnHeaders;
  }

  /**
   * processFacilityImport()
   *
   * Reads contents of the Excel import file into temp table
   *
   * @param $importFile
   */
  public function processImport($importFile)
  {

    require_once(dirname(__FILE__) . '/excel_reader2.php');

    // Validate the uploaded files Excel 2003 extention
    $this->fileInfo = pathinfo($importFile);
    if (strtolower($this->fileInfo["extension"]) <> 'xls') {
      $this->events[] = array("type" => "ERROR", "message" => "{$this->fileInfo['basename']} is not Microsoft Excel 2003 \".xls\" workbook.");
    } else {
      $this->events[] = array("type" => "INFO", "message" => "Opening import file for reading.");
      $xlsObj = new Spreadsheet_Excel_Reader($importFile, false);

      // Get some info about the workbook's composition
      $numSheets = count($xlsObj->sheets);
      $this->events[] = array("type" => "INFO", "message" => "Number of worksheets found: $numSheets");

      $numRows = $xlsObj->rowcount($sheet_index = 0);
      $numCols = $xlsObj->colcount($sheet_index = 0);

      // Create a simplified array from the worksheets
      for ($sheet = 0; $sheet < $numSheets; $sheet++) {
        $importRow = 0;
        $importFileData = array();
        
        // Get the sheet name
        $sheetName = $xlsObj->boundsheets[$sheet]["name"];
        $this->events[] = array("type" => "INFO", "message" => "Parsing worksheet $sheetName");

        // We don't import sheets named "Lookup"
        if (strtolower($sheetName) <> 'lookup') {
          // Grab column headers at the beginning of each sheet.
          $currentSheetHeaders = array_values($xlsObj->sheets[$sheet]['cells'][1]);
          $currentSheetHeaders = $this->cleanColumnHeaders($currentSheetHeaders);
          
          // Check for consistant column header in all data worksheets.  Use the column header from
          // the first worksheet as the import column header for all data worksheets.  
          if ($sheet == 0) {
            // Extend import spec headers with dynamic staff resource requirement columns from xls file.
            $this->extendsImportSpecHeaders($currentSheetHeaders);
            $this->createTempTable();
          }

          $this->events[] = array("type" => "INFO", "message" => "Validating column headers of import file.");

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
        } else {
          $this->events[] = array("type" => "INFO", "message" => "Ignoring $sheetName worksheet");
        }
      }
      $this->events[] = array("type" => "OK", "message" => "Done inserting temp records.");
      return true;
    }
  }

  /**
   * Method to extend import spec headers with dynamic staff requirement columns.
   *
   * @param array $importFileHeaders An array of column headers from import file.
   */
  protected function extendsImportSpecHeaders($importFileHeaders)
  {

    /** @todo this function is to be overloaded by it's children classes agFacilityImportXLS and
     *        agStaffImportXLS
     */
  }

  /**
   * validateColumnHeaders($importFileData)
   *
   * Validates import data for correct schema. Returns bool.
   *
   * @param $importFileHeaders
   */
  protected function validateColumnHeaders($importFileHeaders, $sheetName)
  {
    // Check if import file header is null
    if (empty($importFileHeaders))
    {
      $this->events[] = array("type" => "ERROR", "message" => "Worksheet \"$sheetName\" is missing column headers.");
      return false;
    }

    // Check min/max set columns.  These two columns must come in a set.  Cannot add one column and
    // not the other.
    $setHeaders = preg_grep('/_(min|max)$/i', $importFileHeaders);
    foreach($setHeaders as $key => $column) {
      $setHeaders[$key] = rtrim(rtrim(strtolower($column), '_min'), '_max');
    }
    $setHeaders = array_unique($setHeaders);
    foreach($setHeaders as $key => $header) {
      if ( !in_array($header.'_min', $importFileHeaders)
           || !in_array($header.'_max', $importFileHeaders))
      {
        $this->events[] = array("type" => "ERROR", "message" => "Incomplete $header min/max set columns.");
        return false;
      }
    }

    // Cache the import header specification
    $importSpecHeaders = array_keys($this->importSpec);

    // The import spec will start with an ID column. Shift off of it.
    $idColumn = array_shift($importSpecHeaders);
    $importSpecDiff = array_diff($importSpecHeaders, $importFileHeaders);

    if (empty($importSpecDiff)) {
      return true;
    } else {
      $this->events[] = array("type" => "ERROR", "message" => "Missing required columns.");

      foreach ($importSpecDiff as $missing) {
        $this->events[] = array("type" => "ERROR", "message" => "Column header \"$missing\" missing.");
      }
      return false;
    }
  }

  /**
   * saveImportTemp()
   *
   * Writes import array to temp table
   *
   * @param $importDataSet
   */
  protected function saveImportTemp($importDataSet)
  {
    require_once(dirname(__FILE__) . '/../../../../config/ProjectConfiguration.class.php');
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
    $dbManager = new sfDatabaseManager($configuration);
//    $appConfig = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
//    $dbManager = new sfDatabaseManager($appConfig);
    $db = $dbManager->getDatabase('doctrine');

    $conn = Doctrine_Manager::connection();

    if (empty($importDataSet)) {
      $this->events[] = array("type" => "ERROR", "message" => "Cannot save empty dataset to temp table.");
    } else {

      // Create an INSERT query for each facility
      foreach ($importDataSet as $row) {

        // Some Excel workbooks create empty rows. Let's ignore those
        $rowString = implode($row);
        if (empty($rowString)) {
          $this->events[] = array("type" => "WARN", "message" => "Ignoring empty row.");
        } else {

          $query = "INSERT INTO %s (%s) \nVALUES (%s\n)";
          $cols = "";
          $vals = "";

          foreach (array_keys($this->importSpec) as $column) {

            // Ignore id key name because it is the auto
            if ($column != "id") {
              $col = sprintf("\n\t`%s`,", $column);
              $cols = $cols . $col;

              // Handle null values with sane defaults
              switch ($this->importSpec[$column]["type"]) {
                case "integer":
                  if ($row[$column] == "") {
                    $val = 0;
                  } else {
                    $val = $row[$column];
                  };
                  break;
                case "decimal":
                  if ($row[$column] == "") {
                    $val = 0.0;
                  } else {
                    $val = $row[$column];
                  }
                  break;
                default:
                  $val = trim($row[$column]);
              }

              // Use the Doctrine_Manager::connection() PDO to safe up single quotes
              $val = $conn->quote($val);

              $val = sprintf("\n\t%s,", $val);
              $vals = $vals . "$val";
            }
          }
          // Chop off the trailing comma
          $cols = substr($cols, 0, -1);
          $vals = substr($vals, 0, -1);

          $query = sprintf($query, $this->tempTable, $cols, $vals);

          // Insert records into temp table
          try {

            //$this->events[] = array("type" => "INFO", "message" => $query);
            $pdo = $conn->execute($query);
            $results += $pdo->rowCount();
          } catch (Doctrine_Exception $e) {

            //todo find a more descriptive way of displaying MySQL error messages.
            //  The Doctrine exceptions are not very helpful.
            $this->events[] = array("type" => "ERROR", "message" => $query);
            $this->events[] = array("type" => "ERROR", "message" => $e->errorMessage());
          }
          $this->numRecordsImported = $results;
        }
      }
    }
  }

  /**
   * createTempTable()
   *
   * Creates temp table
   *
   * @param
   */
  public function createTempTable()
  {
    // Access Symfony...we'll only need these lines if we need to go the shell_exec route. Might need IReadFilter.php in any case though.
    require_once(dirname(__FILE__) . '/../../../../config/ProjectConfiguration.class.php');
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);

    // Same here
    $appConfig = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
    $dbManager = new sfDatabaseManager($appConfig);
    $db = $dbManager->getDatabase('doctrine');

    $conn = Doctrine_Manager::connection();

    // Drop temp if it exists
    try {
      $conn->export->dropTable($this->tempTable);
    } catch (Doctrine_Exception $e) {
      
    }

    $options = array(
      'type' => 'MYISAM',
      'charset' => 'utf8'
    );

    // Create the table
    try {

      $conn->export->createTable($this->tempTable, $this->importSpec, $options);
      $this->events[] = array("type" => "OK", "message" => "Successfully created temp table.");
    } catch (Doctrine_Exception $e) {

      //todo find a more descriptive way of displaying MySQL error messages.
      //  The Doctrine exceptions are not very helpful.
      $this->events[] = array("type" => "ERROR", "message" => "Error creating temp table for import.");
      $this->events[] = array("type" => "ERROR", "message" => $e->errorMessage());
    }
  }

  /**
   * dumpImportFile()
   *
   * Dumps the contents of the Excel import file
   *
   * @param $importFile
   * @todo eventually remove this function
   */
  public function dumpImportFile($importFile)
  {

    require_once(dirname(__FILE__) . '/excel_reader2.php');

    $xlsObj = new Spreadsheet_Excel_Reader($importFile);
    $numRows = $xlsObj->rowcount($sheet_index = 0);
    $numCols = $xlsObj->colcount($sheet_index = 0);

    $foo = "";

    for ($row = 1; $row <= $numRows; $row++) {

      for ($col = 1; $col <= $numCols; $col++) {
        $foo .= $xlsObj->val($row, $col) . ", ";
      }
      $foo .= "\n";
    }
    return $foo;
  }


  public static function processFile(sfEvent $event)
  {
    // this works!!! :
//    $testFile = $event->getSubject()->importPath . '.TESTING.LISTENER';
//    touch($testFile);
//
    // TODO: make this to the brunt of the work
    // ...

  }

}
