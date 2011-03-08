<?php

/**
 * Facility Import Class
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Clayton Kramer, CUNY SPS
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

  public $importFacilitySpec = array(
    'id' => array('type' => 'integer', 'autoincrement' => true, 'primary' => true),
    'facility_name' => array('type' => "string", 'length' => 64),
    'facility_resource_code' => array('type' => "string", 'length' => 10),
    'facility_resource_type_abbr' => array('type' => "string", 'length' => 10),
    'facility_resource_status' => array('type' => "string", 'length' => 40),
    'facility_capacity' => array('type' => "integer"),
    'facility_activation_sequence' => array('type' => "integer"),
    'facility_allocation_status' => array('type' => "string", 'length' => 30),
    'facility_group' => array('type' => "string", 'length' => 64),
    'facility_group_type' => array('type' => "string", 'length' => 30),
    'facility_group_allocation_status' => array('type' => "string", 'length' => 30),
    'facility_group_activation_sequence' => array('type' => "integer"),
    'work_email' => array('type' => "string", 'length' => 255),
    'work_phone' => array('type' => "string", 'length' => 32),
    'street_1' => array('type' => "string", 'length' => 255),
    'street_2' => array('type' => "string", 'length' => 255),
    'city' => array('type' => "string", 'length' => 255),
    'state' => array('type' => "string", 'length' => 255),
    'postal_code' => array('type' => "string", 'length' => 5),
    'borough' => array('type' => "string", 'length' => 30),
    'country' => array('type' => "string", 'length' => 64),
    'longitude' => array('type' => "decimal", 'length' => 12, 'scale' => 8),
    'latitude' => array('type' => "decimal", 'length' => 12, 'scale' => 8),
    'generalist_min' => array('type' => "integer"),
    'generalist_max' => array('type' => "integer"),
    'specialist_min' => array('type' => "integer"),
    'specialist_max' => array('type' => "integer"),
    'operator_min' => array('type' => "integer"),
    'operator_max' => array('type' => "integer"),
    'medical_nurse_min' => array('type' => "integer"),
    'medical_nurse_max' => array('type' => "integer"),
    'medical_other_min' => array('type' => "integer"),
    'medical_other_max' => array('type' => "integer")
  );
  // Public variables declared here
  public $events = array();
  public $numRecordsImported = 0;

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
      $importRow = 0;
      for ($sheet = 0; $sheet < $numSheets; $sheet++) {

        // Get the sheet name
        $sheetName = $xlsObj->boundsheets[$sheet]["name"];
        $this->events[] = array("type" => "INFO", "message" => "Parsing worksheet $sheetName");

        // We don't import sheets named "Lookup"
        if (strtolower($sheetName) <> 'Lookup') {
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
        } else {
          $this->events[] = array("type" => "INFO", "message" => "Ignoring $sheetName worksheet");
        }
      }

      $this->events[] = array("type" => "INFO", "message" => "Validating column headers of import file.");

      if ($this->validateColumnHeaders($importFileData)) {

        $this->events[] = array("type" => "OK", "message" => "Valid column headers found.");
        $this->events[] = array("type" => "INFO", "message" => "Inserting records into temp table.");
        $this->saveImportTemp($importFileData);
        $this->events[] = array("type" => "OK", "message" => "Done inserting temp records.");
      } else {

        $this->events[] = array("type" => "ERROR", "message" => "Unable to import file due to validation error.");
      }
    }
  }

  /**
   * validateColumnHeaders($importFileData)
   *
   * Validates import data for correct schema. Returns bool.
   *
   * @param $importFileData
   */
  private function validateColumnHeaders($importFileData)
  {

    // Cache the import header specification
    $importSpecHeaders = array_keys($this->importFacilitySpec);

    // Check first row for expected column header names
    $importFileHeaders = array_keys(array_shift($importFileData));

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
  private function saveImportTemp($importDataSet)
  {
    require_once(dirname(__FILE__) . '/../../../../config/ProjectConfiguration.class.php');
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
    $appConfig = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
    $dbManager = new sfDatabaseManager($appConfig);
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

          $query = "INSERT INTO temp_facilityImport (%s) \nVALUES (%s\n)";
          $cols = "";
          $vals = "";

          foreach (array_keys($this->importFacilitySpec) as $column) {

            // Ignore id key name because it is the auto
            if ($column != "id") {
              $col = sprintf("\n\t`%s`,", $column);
              $cols = $cols . $col;

              // Handle null values with sane defaults
              switch ($this->importFacilitySpec[$column]["type"]) {
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

          $query = sprintf($query, $cols, $vals);

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
      $conn->export->dropTable('temp_facilityImport');
    } catch (Doctrine_Exception $e) {
      
    }

    $options = array(
      'type' => 'MYISAM',
      'charset' => 'utf8'
    );

    // Create the table
    try {

      $conn->export->createTable('temp_facilityImport', $this->importFacilitySpec, $options);
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

  /**
   * dumpFacilities()
   *
   * Debugging tool for quickly viewing imported facility data
   *
   * @param
   * @todo eventually remove this function
   */
  public function dumpFacilities()
  {
    require_once(dirname(__FILE__) . '/../../../../config/ProjectConfiguration.class.php');
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
    $appConfig = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
    $dbManager = new sfDatabaseManager($appConfig);
    $db = $dbManager->getDatabase('doctrine');

    $facRes = agDoctrineQuery::create()
            ->select('f.facility_name, frt.facility_resource_type_abbr, frs.facility_resource_status, fr.capacity, fr.facility_resource_code,')
            ->addSelect('sfr.activation_sequence, fras.facility_resource_allocation_status')
            ->addSelect('sfg.scenario_facility_group, fgt.facility_group_type, fgas.facility_group_allocation_status, sfg.activation_sequence')
            ->addSelect('gc.longitude, gc.latitude')
            ->from('agFacility f')
            ->innerJoin('f.agSite s')
            ->innerJoin('s.agEntity e')
            ->innerJoin('f.agFacilityResource fr')
            ->innerJoin('fr.agFacilityResourceType frt')
            ->innerJoin('fr.agFacilityResourceStatus frs')
            ->innerJoin('fr.agScenarioFacilityResource sfr')
            ->innerJoin('sfr.agFacilityResourceAllocationStatus fras')
            ->innerJoin('sfr.agScenarioFacilityGroup sfg')
            ->innerJoin('sfg.agFacilityGroupType fgt')
            ->innerJoin('sfg.agFacilityGroupAllocationStatus fgas')
            ->innerJoin('e.agEntityAddressContact eac')
            ->innerJoin('eac.agAddressContactType act ON act.address_contact_type=?', 'work')
            ->innerJoin('eac.agAddress a')
            ->innerJoin('a.agAddressGeo ag')
            ->innerJoin('ag.agGeo g')
            ->innerJoin('g.agGeoSource gs')
            ->innerJoin('g.agGeoFeature gf')
            ->innerJoin('gf.agGeoCoordinate gc')
            ->where('sfg.scenario_id=?', '1')
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $foo = "";
    foreach ($facRes as $row) {
      foreach ($row as $val) {
        print("$val,");
      }
      print("\n");
    }
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
