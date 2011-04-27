<?php

/**
 * Staff Import XLS Class which extends the Import XLS class
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Charles Wisniewski, CUNY SPS
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
class agStaffImportXLS extends agImportXLS
{

  function __construct()
  {

    $this->importSpec = array(
      'id' => array('type' => 'integer', 'autoincrement' => true, 'primary' => true),
      'entity_id' => array('type' => 'integer'),
      'first_name' => array('type' => "string", 'length' => 64),
      'middle_name' => array('type' => "string", 'length' => 64),
      'last_name' => array('type' => "string", 'length' => 64),
      'home_phone' => array('type' => "string", 'length' => 16),
      'home_email' => array('type' => "string", 'length' => 255),
      'work_phone' => array('type' => "string", 'length' => 16),
      'work_email' => array('type' => "string", 'length' => 255),
      'home_address_line_1' => array('type' => "string", 'length' => 255),
      'home_address_line_2' => array('type' => "string", 'length' => 255),
      'home_address_city' => array('type' => "string", 'length' => 255),
      'home_address_state' => array('type' => "string", 'length' => 255),
      'home_address_zip' => array('type' => "string", 'length' => 5),
      //'borough' => array('type' => "string", 'length' => 30),
      'home_address_country' => array('type' => "string", 'length' => 128),
      'home_latitude' => array('type' => "decimal", 'length' => 12, 'scale' => 8),
      'home_longitude' => array('type' => "decimal", 'length' => 12, 'scale' => 8),
      'work_address_line_1' => array('type' => "string", 'length' => 255),
      'work_address_line_2' => array('type' => "string", 'length' => 255),
      'work_address_city' => array('type' => "string", 'length' => 255),
      'work_address_state' => array('type' => "string", 'length' => 255),
      'work_address_zip' => array('type' => "string", 'length' => 5),
      //'borough' => array('type' => "string", 'length' => 30),
      'work_address_country' => array('type' => "string", 'length' => 128),
      'work_latitude' => array('type' => "decimal", 'length' => 12, 'scale' => 8),
      'work_longitude' => array('type' => "decimal", 'length' => 12, 'scale' => 8),
      'organization' => array('type' => "string", 'length' => 128),
      'resource_type' => array('type' => "string", 'length' => 64),
      'resource_status' => array('type' => "string", 'length' => 30),
      'language_1' => array('type' => "string", 'length' => 128), //ag_person_mj_ag_language
      'l1_speak' => array('type' => "string", 'length' => 64),    //ag_person_language_competency
      'l1_read' => array('type' => "string", 'length' => 64),
      'l1_write' => array('type' => "string", 'length' => 64),
      'language_2' => array('type' => "string", 'length' => 128), //ag_person_mj_ag_language
      'l2_speak' => array('type' => "string", 'length' => 64),    //ag_person_language_competency
      'l2_read' => array('type' => "string", 'length' => 64),
      'l2_write' => array('type' => "string", 'length' => 64),
      '_import_success' => array('type' => "boolean", 'default' => TRUE)
    );
    $this->defaultHeaderSize = sizeof($this->importSpec);

    $this->customFieldType = array('type' => "string", 'length' => 255);

    $this->tempTable = 'temp_staffImport';
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
   * Method to extend import spec headers with dynamic language proficiency columns.
   *
   * @param array $importFileHeaders An array of column headers from import file.
   */
  protected function extendsImportSpecHeaders($importFileHeaders)
  {
    $saveFileHeaders = $this->cleanColumnHeaders($importFileHeaders);
    $extendedHeaders = array_diff($saveFileHeaders, array_keys($this->importSpec));
    foreach($extendedHeaders as $extHeader)
    {
      $this->importSpec[$extHeader] = $this->customFieldType;
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
            unset($this->importSpec['_import_success']);
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

  public static function processFile(sfEvent $event)
  {
//    $testFile = $event->getSubject()->importPath . '.TESTING.LISTENER';
//    touch($testFile);
//
    // @todo make this to the brunt of the work
    // ...
  }

}