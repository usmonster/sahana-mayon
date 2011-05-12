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
 * @deprecated
 */
class agStaffImportXLS extends agImportXLS
{

  function __construct()
  {

    // Declare class properties.
    $this->name = "agStaffImportXLS";
    $this->importSpec = array(
      'id' => array('type' => 'integer', 'autoincrement' => true, 'primary' => true),
      'entity_id' => array('type' => 'integer'),
      'first_name' => array('type' => "string", 'length' => 64),
      'middle_name' => array('type' => "string", 'length' => 64),
      'last_name' => array('type' => "string", 'length' => 64),
      'mobile_phone' => array('type' => "string", 'length' => 16),
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
  protected function extendsImportSpecHeaders(array $importFileHeaders)
  {
    $saveFileHeaders = $this->cleanColumnHeaders($importFileHeaders);
    $extendedHeaders = array_diff($saveFileHeaders, array_keys($this->importSpec));
    foreach($extendedHeaders as $extHeader)
    {
      $this->importSpec[$extHeader] = $this->customFieldType;
    }
  }

  protected function setImportSpec(){}
  protected function setDynamicFieldType(){}
  protected function cleanColumnName($columnName){}
  protected function addDynamicColumns(array $importHeaders){}

  /**
   * Validates import data for correct schema. Returns bool.
   *
   * @param $importFileHeaders
   * @param $sheetName
   */
  protected function validateColumnHeaders(array $importFileHeaders, $sheetName)
  {
    if (parent::validateColumnHeaders($importFileHeaders, $sheetName) === FALSE)
    {
      return FALSE;
    }
    return TRUE;
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
            unset($this->importSpec['success']);
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
    // this works!!! :
//    $testFile = $event->getSubject()->importPath . '.TESTING.LISTENER';
//    touch($testFile);
//
    // TODO: make this to the brunt of the work
    // ...

  }

}

/** require_once sfConfig::get('sf_app_dir') . '/lib/util/excel.php';

class agStaffImportXls
{

  // processStaffImport() ***********************
  public function processStaffImport($importFile)
  {
    $this->importFile = $importFile;

    //$test = stream_wrapper_register('xlsfile', 'xlsStream'); //worked or not? do i even need to, or is including enough?
    //the xlsStream reader has been initiated above, though no data is being read yet.
    $fileData = fopen('xlsfile:///' . $importFile, 'r');
    if (isset($fileData)) {

      if (!isset($this->importHeaders)) {
        $this->importHeaders = self::buildHeaders($objFile, $highestColumnIndex);
        // We should set up a few definitions, at least Krakatoa and Agasti, that can be picked by the user.
        // This is Krakatoa.
        $definitionHeaders =
            array(
              'person_id', 'agency', 'classification',
              'medical_classification', 'pms_id', 'firstname',
              'lastname', 'title', 'civil_service_title',
              'status', 'home_address', 'home_city',
              'home_state', 'home_zipcode', 'work_address',
              'work_city', 'work_state', 'work_zipcode',
              'pref_zip', 'home_phone', 'work_phone',
              'mobile_phone', 'pref_phone', 'work_email',
              'personal_email', 'pref_email', 'driver_license_class_1',
              'driver_license_class_2', 'driver_license_class_3',
              'staff_supervise', 'language_1', 'speak_1', 'read_1',
              'write_1', 'language_2', 'speak_2', 'read_2',
              'write_2', 'language_3', 'speak_3', 'read_3', 'write_3',
              'date_created'
        );
        // Test the incoming headers against the definition, creating $importStatus in the process
        $importStatus = self::testHeaders($importFile, $this->importHeaders, $definitionHeaders);


      }
      if ($this->importSuccess == true) {
        $importedStaff = self::buildImportedStaff($this->importHeaders, $objFile, $lRow, $hRow, $highestRow);
        // trying to free up some memory.
        unset($objReader);
        if ($importedStaff <> false) {
          $returned = $this->saveImportedStaff($importedStaff);
        } else {
          $this->stop = true;
          return;
        }
      } else {
        // Add some error reporting here.
        $errors = 1;
      }

      //        $this->importHeadings = $importHeaders;
      //      $this->importMatch = $importMatch;
      //      $this->importedStaff = $importedStaff;
      //    $returned[Staff] = $importedStaff;
      //    $returned[Errors] = $errors;
      //    $returned[Count] = $importCount;
      $this->rowSetIterator++;
      return $returned;
    }
    //} //end the foreach, chunking our file into tangible parts
  }

  // saveImportedStaff() ***********************
  public function saveImportedStaff($importedStaff, $i = 1)
  {
    if (!is_array($importedStaff)) {
      $importedStaff = unserialize(stripslashes($importedStaff));
    }
    $c = count($importedStaff);
    foreach ($importedStaff as $import) {
      //          if ($import['Custom']['Person ID'] <> null) {
      //
      //          }
      // Basic/Primary
      $entity = new agEntity();
      $entity->save();
      $person = new agPerson();
      $person->entity_id = $entity->id;
      $person->save();
      $staff = new agStaff();
      $staff->person_id = $person->id;
      $staff->staff_status_id = 1;
      $staff->save();

      foreach ($import as $key => $point) {
        // Names
        if (in_array($key, array('Given Name', 'Family Name')) && $point <> null) {
          switch ($key) {
            case "Given Name":
              self::importName($person, $point, 1);
              break;
            case "Family Name":
              self::importName($person, $point, 3);
              break;
          }
        }
        // Phone **************************************
        if (in_array($key, array('Home Phone', 'Work Phone', 'Mobile Phone')) && $point <> null) {
          switch ($key) {
            case "Home Phone":
              self::importPhone($person, $point, 2, 2);
              break;
            case "Work Phone":
              self::importPhone($person, $point, 1, 1);
              break;
            case "Mobile Phone":
              self::importPhone($person, $point, 3, 3);
              break;
          }
        }
        // Email **************************************
        if (in_array($key, array('Work Email', 'Personal Email')) && $point <> null) {
          switch ($key) {
            case "Work Email":
              self::importEmail($person, $point, 1, 1);
              break;
            case "Personal Email":
              self::importEmail($person, $point, 2, 2);
              break;
          }
        }
        //Language ************************************
        if ($key == 'Language' && $point <> null) {
          self::importLanguage($import, $person, $point);
        }
        // Address ************************************
        if (in_array($key, array('Home Address', 'Work Address')) && $point <> null) {
          switch ($key) {
            case "Home Address":
              self::importAddress($person, $point, 1, 1);
              break;
            case "Work Address":
              self::importAddress($person, $point, 2, 2);
              break;
          }
        }
        // Custom *************************************
        if ($key == 'Custom' && $point <> null) {
          self::importCustom($person, $point);
        }
        // Classification *****************************
        // Always give them a staff resource type, so don't check for null here. That happens in
        // importClassification, setting a default resource_type if it is null.
        if ($key == 'Classification') {
          self::importClassification($person, $point, $staff);
        }
      }
      $importCount++;
    }
    $returned['Import Count'] = $importCount;
    $returned['Staff'] = $importedStaff;
    $returned['Errors'] = $errors;
    $returned['Current Iteration'] = $i;
    $returned['Max Iteration'] = $c;

//      return serialize($returned);
    return $returned;
  }

  // importName() *******************
  private function importName($person, $point, $type)
  {
    $nameCheck =
            Doctrine::getTable('agPersonName')
            ->findByDql('person_name = ?', $point)
            ->getFirst();
    if (!($nameCheck instanceof agPersonName)) {
      $nameCheck = new agPersonName();
      $nameCheck->person_name = $point;
      $nameCheck->save();
    }
    $nameJoin = new agPersonMjAgPersonName();
    $nameJoin->person_id = $person->id;
    $nameJoin->person_name_id = $nameCheck->id;
    $nameJoin->person_name_type_id = $type;
    $nameJoin->priority = 1;
    $nameJoin->save();
  }

  // importPhone() ******************
  private function importPhone($person, $point, $type, $priority)
  {
    if (preg_match('/^(\d{10})(\d{1,4})?$/', $point)) {
      $point = preg_replace('/^(\d{10})(\d{1,4})$/', '\1x\2', $point);
      $phoneCheck =
              Doctrine::getTable('agPhoneContact')
              ->findByDql('phone_contact = ?', $point)
              ->getFirst();
      if (!($phoneCheck instanceof agPhoneContact)) {
        $phoneCheck = new agPhoneContact();
        $phoneCheck->phone_contact = $point;
        $phoneCheck->phone_format_id = (strpos($point, 'x') === false) ? 1 : 2;
        $phoneCheck->save();
      }
      $phoneJoin = new agEntityPhoneContact();
      $phoneJoin->entity_id = $person->entity_id;
      $phoneJoin->phone_contact_id = $phoneCheck->id;
      $phoneJoin->phone_contact_type_id = $type;
      $phoneJoin->priority = $priority;
      $phoneJoin->save();
    } else {
      // Add some error messaging stuff in here.
      $errors = 1;
    }
  }

  // importLanguage() ***************
  private function importLanguage($import, $person, $point)
  {
    foreach ($point as $k => $language) {
      if ($language <> null) {
        $languageCheck = null;
        $languageCheck =
                Doctrine::getTable('agLanguage')
                ->findByDql('language = ?', $language)
                ->getFirst();
        if (!($languageCheck instanceof agLanguage)) {
          $languageCheck = new agLanguage();
          $languageCheck->language = $language;
          $languageCheck->save();
        }
        $languageJoinCheck =
                Doctrine::getTable('agPersonMjAgLanguage')
                ->findByDql(
                    'person_id = ? AND language_id = ?',
                    array($person->id, $languageCheck->id)
                )
                ->getFirst();
        if (!($languageJoinCheck instanceof agPersonMjAgLanguage)) {
          $languageJoin = new agPersonMjAgLanguage();
          $languageJoin->person_id = $person->id;
          $languageJoin->language_id = $languageCheck->id;
          //First language listed gets first priority, etc.
          $languageJoin->priority = $k + 1;
          $languageJoin->save();
          // The next sections handle the read, write, and speak columns. These keys aren't checked, as the
          // $k value from the Language column will correspond to the Read, Write, and Speak values.
          // So, if $k = 1, $import['Read'][$k] will be the Read value that corresponds to $language[$k].
          if ($import['Read'][$k] <> null) {
            $compJoinR = new agPersonLanguageCompetency();
            $compJoinR->person_language_id = $languageJoin->id;
            $compJoinR->language_format_id = 1;
            $compJoinR->language_competency_id = 3;
            $compJoinR->save();
          }
          if ($import['Write'][$k] <> null) {
            $compJoinW = new agPersonLanguageCompetency();
            $compJoinW->person_language_id = $languageJoin->id;
            $compJoinW->language_format_id = 2;
            $compJoinW->language_competency_id = 3;
            $compJoinW->save();
          }
          if ($import['Speak'][$k] <> null) {
            $compJoinS = new agPersonLanguageCompetency();
            $compJoinS->person_language_id = $languageJoin->id;
            $compJoinS->language_format_id = 3;
            $compJoinS->language_competency_id = 3;
            $compJoinS->save();
          }
        }
      }
    }
  }

  // importClassification() *********
  private function importClassification($person, $point, $staff)
  {
    if ($point <> null) {
      $resourceTypeCheck =
              Doctrine::getTable('agStaffResourceType')
              ->findByDql(
                  'staff_resource_type = ?', ucwords($point)
              )
              ->getFirst();
      $staffResourceJoin = new agStaffResource();
      $staffResourceJoin->staff_id = $staff->id;
      if (!($resourceTypeCheck instanceof agStaffResourceType)) {
        $staffResourceJoin->staff_resource_type_id = 1;
      } else {
        $staffResourceJoin->staff_resource_type_id = $resourceTypeCheck->id;
      }
      $staffResourceJoin->save();
    } else {
      $staffResourceJoin = new agStaffResource();
      $staffResourceJoin->staff_id = $staff->id;
      $staffResourceJoin->staff_resource_type_id = 1;
      $staffResourceJoin->save();
    }
  }

  // importAddress() ****************
  private function importAddress($person, $point, $type, $priority)
  {
    $newAdd = new agAddress();
    $newAdd->address_standard_id = 1;
    $newAdd->save();
    foreach ($point as $k => $addValue) {
      if ($addValue <> null) {
        $valueCheck = null;
        // Set $elId to the right agAddressElement id value. 1 = line 1/street, 3 = City, 4 = State, 5 = zip.
        if ($k == 0) {
          $elId = 1;
        } else {
          $elId = $k + 2;
        }
        $valueCheck =
                Doctrine::getTable('agAddressValue')
                ->findByDql(
                    'value = ? AND address_element_id = ?', array($addValue, $elId)
                )
                ->getFirst();
        // Don't want to add new states, so only if $elId <> 4.
        if (!($valueCheck instanceof agAddressValue) && $elId <> 4) {
          $valueCheck = new agAddressValue();
          $valueCheck->value = $addValue;
          $valueCheck->address_element_id = $elId;
          $valueCheck->save();
        }
        // Should be an instance of agAddress value as long as it wasn't an invalid state.
        if ($valueCheck instanceof agAddressValue) {
          $addressJoin = new agAddressMjAgAddressValue();
          $addressJoin->address_id = $newAdd->id;
          $addressJoin->address_value_id = $valueCheck->id;
          $addressJoin->save();
        }
      }
    }
    $entAddress = new agEntityAddressContact();
    $entAddress->entity_id = $person->entity_id;
    $entAddress->address_id = $newAdd->id;
    $entAddress->address_contact_type_id = $type;
    $entAddress->priority = $priority;
    $entAddress->save();
  }

  // importCustom() *****************
  private function importCustom($person, $point)
  {
    foreach ($point as $k => $custom) {
      // Driver License has a sub-array that needs to be processsed differently.
      if ($k <> 'Driver License' && $custom <> null) {
        $customFieldTypeCheck =
                Doctrine::getTable('agCustomFieldType')
                ->findByDql(
                    'custom_field_type = ?', $k
                )
                ->getFirst();
        if (!($customFieldTypeCheck instanceof agCustomFieldType)) {
          $newCustomFieldType = new agCustomFieldType();
          $newCustomFieldType->custom_field_type = $k;
          $newCustomFieldType->save();
        } else {
          $newCustomFieldType = $customFieldTypeCheck;
        }
        $customFieldCheck =
                Doctrine::getTable('agPersonCustomField')
                ->findByDql(
                    'person_custom_field = ?', $k
                )
                ->getFirst();
        if (!($customFieldCheck instanceof agPersonCustomField)) {
          $newPersonCustomField = new agPersonCustomField();
          $newPersonCustomField->person_custom_field = $k;
          $newPersonCustomField->custom_field_type_id = $newCustomFieldType->id;
          $newPersonCustomField->save();
        } else {
          $newPersonCustomField = $customFieldCheck;
        }
        $personCustomCheck =
                Doctrine::getTable('agPersonCustomFieldValue')
                ->findByDql(
                    'person_id = ? AND person_custom_field_id = ?', array($person->id, $newPersonCustomField->id)
                )
                ->getFirst();
        if (!($personCustomCheck instanceof agPersonCustomFieldValue)) {
          $newPersonCustom = new agPersonCustomFieldValue();
          $newPersonCustom->person_id = $person->id;
          $newPersonCustom->person_custom_field_id = $newPersonCustomField->id;
          $newPersonCustom->value = $custom;
          $newPersonCustom->save();
        }
      } else {
        if ($k == 'Driver License' && $custom <> null) {
          $customFieldTypeCheck =
                  Doctrine::getTable('agCustomFieldType')
                  ->findByDql(
                      'custom_field_type = ?', $k
                  )
                  ->getFirst();
          if (!($customFieldTypeCheck instanceof agCustomFieldType)) {
            $newCustomFieldType = new agCustomFieldType();
            $newCustomFieldType->custom_field_type = $k;
            $newCustomFieldType->save();
          } else {
            $newCustomFieldType = $customFieldTypeCheck;
          }
          foreach ($custom as $sk => $licenseType) {
            if ($licenseType <> null) {
              $customFieldCheck =
                      Doctrine::getTable('agPersonCustomField')
                      ->findByDql(
                          'person_custom_field = ?', $sk
                      )
                      ->getFirst();
              if (!($customFieldCheck instanceof agPersonCustomField)) {
                $newPersonCustomField = new agPersonCustomField();
                $newPersonCustomField->person_custom_field = $sk;
                $newPersonCustomField->custom_field_type_id = $newCustomFieldType->id;
                $newPersonCustomField->save();
              } else {
                $newPersonCustomField = $customFieldCheck;
              }
              $personCustomCheck =
                      Doctrine::getTable('agPersonCustomFieldValue')
                      ->findByDql(
                          'person_id = ? AND person_custom_field_id = ?', array($person->id, $newPersonCustomField->id)
                      )
                      ->getFirst();
              if (!($personCustomCheck instanceof agPersonCustomFieldValue)) {
                $newPersonCustom = new agPersonCustomFieldValue();
                $newPersonCustom->person_id = $person->id;
                $newPersonCustom->person_custom_field_id = $newPersonCustomField->id;
                $newPersonCustom->value = $licenseType;
                $newPersonCustom->save();
              } else {
                $personCustomCheck->value = $licenseType;
                $personCustomCheck->save();
              }
            }
          }
        }
      }
    }
  }

  // importEmail() ******************
  private function importEmail($person, $point, $type, $priority)
  {
    if (preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i', $point)) {
      $emailCheck =
              Doctrine::getTable('agEmailContact')
              ->findByDql('email_contact = ?', $point)
              ->getFirst();
      if (!($emailCheck instanceof agEmailContact)) {
        $emailCheck = new agEmailContact();
        $emailCheck->email_contact = $point;
        $emailCheck->save();
      }
      $emailJoin = new agEntityEmailContact();
      $emailJoin->entity_id = $person->entity_id;
      $emailJoin->email_contact_id = $emailCheck->id;
      $emailJoin->email_contact_type_id = $type;
      $emailJoin->priority = $priority;
      $emailJoin->save();
    } else {
      $errors = 1;
    }
  }

  // buildHeaders() *****************
  private function buildHeaders(xlsStream $objFile, $highestColumnIndex)
  {
    for ($i = 0; $i < $highestColumnIndex; $i++) {
      $h =
          strtolower(
              str_replace(
                  " ", "_",
                  $objFile->stream_read(4000) //have to get the byte width of a row in this spreadsheet OR the line terminating character
              )
      );
      $importHeaders[] = $h;
    }
    return $importHeaders;
  }

  // testHeaders() ******************
  private function testHeaders($importFile, $importHeaders, $definitionHeaders)
  {
    if (array_diff($importHeaders, $definitionHeaders) == null) {
      $importStatus['Upload Message'] =
          $importFile . ' was successfully read and its data was imported into Agasti.';
      $importStatus['Upload Heading'] = 'Import Success';
      $importStatus['Success'] = true;
      $this->importSuccess = true;
    } else {
      $importStatus['Upload Message'] =
          $this->fileName . ' does not have the right headers to import into Agasti. Please check the column headers of ' . $this->fileName . ' against the header definitions in your manual to ensure the data is correct.';
      $importStatus['Upload Heading'] = 'Import Failure';
      $importStatus['Success'] = false;
      $this->importSuccess = false;
    }
    return $importStatus;
  }

  // buildImportedStaff() ***********************
  private function buildImportedStaff($importHeaders, $objReader, $lRow, $hRow, $highestRow)
  {
    // Row 1 was the headers...
    if ($highestRow < $hRow) {
      $hRow = $highestRow;
    }
    for ($iRow = $lRow; $iRow <= $highestRow; $iRow++) {
      $importMatch = array();
      $iColumn = 0;
      foreach ($importHeaders as $importHeader) {
        if ($importHeader == 'firstname') {
          $importMatch['Given Name'] =
              ucwords(strtolower($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue()));
        }
        if ($importHeader == 'lastname') {
          $importMatch['Family Name'] =
              ucwords(strtolower($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue()));
        }
        if ($importHeader == 'home_phone') {
          $importMatch['Home Phone'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'work_phone') {
          $importMatch['Work Phone'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'mobile_phone') {
          $importMatch['Mobile Phone'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'work_email') {
          $importMatch['Work Email'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'personal_email') {
          $importMatch['Personal Email'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'language_1' || $importHeader == 'language_2' || $importHeader == 'language_3') {
          $importMatch['Language'][] =
              ucwords($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue());
        }
        if ($importHeader == 'speak_1' || $importHeader == 'speak_2' || $importHeader == 'speak_3') {
          $importMatch['Speak'][] =
              ($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() <> 'N' ? 'fluent' : null);
        }
        if ($importHeader == 'read_1' || $importHeader == 'read_2' || $importHeader == 'read_3') {
          $importMatch['Read'][] =
              ($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() <> 'N' ? 'fluent' : null);
        }
        if ($importHeader == 'write_1' || $importHeader == 'write_2' || $importHeader == 'write_3') {
          $importMatch['Write'][] =
              ($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() <> 'N' ? 'fluent' : null);
        }
        if ($importHeader == 'home_address') {
          $importMatch['Home Address'][] =
              ($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() <> null ?
                  $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() : null);
        }
        if ($importHeader == 'home_city') {
          $importMatch['Home Address'][] =
              ($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() <> null ?
                  $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() : null);
        }
        if ($importHeader == 'home_state') {
          $importMatch['Home Address'][] =
              ($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() <> null ?
                  $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() : null);
        }
        if ($importHeader == 'home_zipcode') {
          $importMatch['Home Address'][] =
              ($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() <> null ?
                  $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() : null);
        }
        if ($importHeader == 'work_address') {
          $importMatch['Work Address'][] =
              ($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() <> null ?
                  $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() : null);
        }
        if ($importHeader == 'work_city') {
          $importMatch['Work Address'][] =
              ($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() <> null ?
                  $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() : null);
        }
        if ($importHeader == 'work_state') {
          $importMatch['Work Address'][] =
              ($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() <> null ?
                  $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() : null);
        }
        if ($importHeader == 'work_zipcode') {
          $importMatch['Work Address'][] =
              ($objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() <> null ?
                  $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue() : null);
        }
        if ($importHeader == 'classification') {
          $importMatch['Classification'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'agency') {
          $importMatch['Custom']['Agency'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'person_id') {
          $importMatch['Custom']['Import ID'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'medical_classification') {
          $importMatch['Custom']['Medical Classification'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'title') {
          $importMatch['Custom']['Title'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'civil_service_title') {
          $importMatch['Custom']['Civil Service Title'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'pms_id') {
          $importMatch['Custom']['PMS ID'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'driver_license_class_1') {
          $importMatch['Custom']['Driver License']['Class 1'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'driver_license_class_2') {
          $importMatch['Custom']['Driver License']['Class 2'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        if ($importHeader == 'driver_license_class_3') {
          $importMatch['Custom']['Driver License']['Class 3'] =
              $objReader->getActiveSheet()->getCellByColumnAndRow($iColumn, $iRow)->getValue();
        }
        $iColumn++;
      }
      $importedStaff[] = $importMatch;
    }
    if (isset($importedStaff)) {
      return $importedStaff;
    } else {
      return false;
    }
  }

}

?>
 *
 */