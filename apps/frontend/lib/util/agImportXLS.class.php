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
 * @author     Charles Wisniewski, CUNY SPS
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
abstract class agImportXLS extends agImportHelper
{

  // Public variables declared here
  public $importSpec;
  // ??
  public $events = array();
  public $numRecordsImported = 0;

  /**
   * @todo remove this constructor when agImportHelper is ready
   */
  function __construct()
  {
    //parent::__construct();
    // get our error threshold
    $this->errThreshold = intval(agGlobal::getParam('import_error_threshold'));

    // sets our temp table and builds our import specification
    $this->setDynamicFieldType();

    // Sets a new connection.
    $this->setConnections();

    // reset our iterators
    $this->resetIterData();

  }

  function __destruct()
  {
    $file = $this->fileInfo["dirname"] . DIRECTORY_SEPARATOR . $this->fileInfo["basename"];
    
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
   * @param array $columnHeaders A single value array of column headers.
   */
  protected function cleanColumnHeaders(array $columnHeaders)
  {
    foreach ($columnHeaders as $index => $column) {
      $columnHeaders[$index] = str_replace(' ', '_', trim(strtolower($column)));
    }
    return $columnHeaders;
  }

  /**
   * Method to extend import spec headers with dynamic or import type specific columns.
   *
   * @param array $importFileHeaders An array of column headers from import file.
   */
  abstract protected function extendsImportSpecHeaders(array $importFileHeaders);

  /**
   * Validates import data for correct schema. Returns bool.
   *
   * @param $importFileHeaders
   * @param $importSpecHeaders
   * @param $sheetName
   */
  protected function validateColumnHeaders(array $importFileHeaders, $sheetName)
  {
    // Check if import file header is null
    if (empty($importFileHeaders)) {
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

    /**
     * @TODO This function is to be extended by its children classes agFacilityImportXLS and
     * agStaffImportXLS
     */
  }

  /**
   * saveImportTemp()
   *
   * Writes import array to temp table
   *
   * @param array $importDataSet
   */
  protected function saveImportTemp(array $importDataSet)
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
    $dbManager = new sfDatabaseManager($configuration);
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
    // @todo Is this necessary?
    // Access Symfony...we'll only need these lines if we need to go the shell_exec route. Might need IReadFilter.php in any case though.
    require_once(sfConfig::get('sf_config_dir') . '/ProjectConfiguration.class.php');
    
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

    // @todo Is this *really* necessary?

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
