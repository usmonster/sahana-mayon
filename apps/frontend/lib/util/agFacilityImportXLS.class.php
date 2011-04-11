<?php

/**
 * Facility Import XLS Class which extends the Import XLS class
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
class AgFacilityImportXLS extends AgImportXLS
{

  function __construct()
  {

    $this->name = "agFacilityImportXLS";
      $this->importSpec = array(
    'id' => array('type' => 'integer', 'autoincrement' => true, 'primary' => true),
    'facility_name' => array('type' => "string", 'length' => 64),
    'facility_code' => array('type' => "string", 'length' => 10),
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
    'latitude' => array('type' => "decimal", 'length' => 12, 'scale' => 8)
  );
  $this->staffRequirementFieldType = array('type' => "integer");
  // Public variables declared here
  $this->tempTable = 'temp_facilityImport';
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
//  public function processImport($importFile)
//  {
//    require_once(dirname(__FILE__) . '/excel_reader2.php');
//
//    // Validate the uploaded files Excel 2003 extention
//    $this->fileInfo = pathinfo($importFile);
//    if (strtolower($this->fileInfo["extension"]) <> 'xls') {
//      $this->events[] = array("type" => "ERROR", "message" => "{$this->fileInfo['basename']} is not Microsoft Excel 2003 \".xls\" workbook.");
//    } else {
//      $this->events[] = array("type" => "INFO", "message" => "Opening import file for reading.");
//      $xlsObj = new Spreadsheet_Excel_Reader($importFile, false);
//
//      // Get some info about the workbook's composition
//      $numSheets = count($xlsObj->sheets);
//      $this->events[] = array("type" => "INFO", "message" => "Number of worksheets found: $numSheets");
//
//      $numRows = $xlsObj->rowcount($sheet_index = 0);
//      $numCols = $xlsObj->colcount($sheet_index = 0);
//
//      // Create a simplified array from the worksheets
//      for ($sheet = 0; $sheet < $numSheets; $sheet++) {
//        $importRow = 0;
//        $importFileData = array();
//
//        // Get the sheet name
//        $sheetName = $xlsObj->boundsheets[$sheet]["name"];
//        $this->events[] = array("type" => "INFO", "message" => "Parsing worksheet $sheetName");
//
//        // We don't import sheets named "Lookup"
//        if (strtolower($sheetName) <> 'lookup') {
//          // Grab column headers at the beginning of each sheet.
//          $currentSheetHeaders = array_values($xlsObj->sheets[$sheet]['cells'][1]);
//          $currentSheetHeaders = $this->cleanColumnHeaders($currentSheetHeaders);
//
//          // Check for consistant column header in all data worksheets.  Use the column header from
//          // the first worksheet as the import column header for all data worksheets.
//          if ($sheet == 0) {
//            // Extend import spec headers with dynamic staff resource requirement columns from xls file.
//            $this->extendsImportSpecHeaders($currentSheetHeaders);
//            //this is the only difference between the parent class and itself
//            $this->createTempTable();
//          }
//
//          $this->events[] = array("type" => "INFO", "message" => "Validating column headers of import file.");
//
//          if ($this->validateColumnHeaders($currentSheetHeaders, $sheetName)) {
//            $this->events[] = array("type" => "OK", "message" => "Valid column headers found.");
//          } else {
//            $this->events[] = array("type" => "ERROR", "message" => "Unable to import file due to validation error.");
//            return false;
//          }
//
//          for ($row = 2; $row <= $numRows; $row++) {
//
//            for ($col = 1; $col <= $numCols; $col++) {
//
//              $colName = str_replace(' ', '_', strtolower($xlsObj->val(1, $col, $sheet)));
//
//              $val = $xlsObj->raw($row, $col, $sheet);
//              if (!($val)) {
//                $val = $xlsObj->val($row, $col, $sheet);
//              }
//              $importFileData[$importRow][$colName] = trim($val);
//            }
//            // Increment import array row
//            $importRow++;
//          }
//
//          $this->events[] = array("type" => "INFO", "message" => "Inserting records into temp table.");
//          $this->saveImportTemp($importFileData);
//        } else {
//          $this->events[] = array("type" => "INFO", "message" => "Ignoring $sheetName worksheet");
//        }
//      }
//      $this->events[] = array("type" => "OK", "message" => "Done inserting temp records.");
//      return true;
//    }
//  }

  /**
   * Method to extend import spec headers with dynamic staff requirement columns.
   *
   * @param array $importFileHeaders An array of column headers from import file.
   */
  protected function extendsImportSpecHeaders($importFileHeaders)
  {
    foreach($importFileHeaders as $index => $column) {
      if (preg_match('/_(min|max)$/', $column)) {
        $this->importSpec[$column] = $this->staffRequirementFieldType;
      }
    }

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
            ->select('f.facility_name, f.facility_code, frt.facility_resource_type_abbr, frs.facility_resource_status, fr.capacity')
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
