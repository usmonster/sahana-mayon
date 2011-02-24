<?php

class agFacilityExportHelper {

  public function __construct() {
    $this->primaryOnly = TRUE;
    $this->contactType = 'work';
    $this->addressStandard = 'us standard';
    $this->exportHeaders = array('Facility Name', 'Facility Code', 'Facility Resource Type Abbr',
        'Facility Resource Status', 'Facility Capacity', 'Facility Activation Sequence',
        'Facility Allocation Status', 'Facility Group', 'Facility Group Type',
        'Facility Group Allocation Status', 'Facility Group Activation Sequence',
        'Work Email', 'Work Phone');
    $this->facilityGeneralInfo = agFacilityHelper::facilityGeneralInfo('Scenario');
    $this->facilityAddress = agFacilityHelper::facilityAddress($this->addressStandard, $this->primaryOnly, $this->contactType);
    $this->facilityGeo = agFacilityHelper::facilityGeo($this->primaryOnly, $this->contactType);
    $this->facilityEmail = agFacilityHelper::facilityEmail($this->primaryOnly, $this->contactType);
    $this->facilityPhone = agFacilityHelper::facilityPhone($this->primaryOnly, $this->contactType);
    $this->facilityStaffResource = agFacilityHelper::facilityStaffResource();
    $this->addressFormat = Doctrine_Query::create()
                    ->select('ae.address_element')
                    ->from('agAddressElement ae')
                    ->innerJoin('ae.agAddressFormat af')
                    ->innerJoin('af.agAddressStandard astd')
                    ->where('astd.address_standard=?', $this->addressStandard)
                    ->andWhere('ae.address_element<>?', 'zip+4')
                    ->orderBy('af.line_sequence, af.inline_sequence')
                    ->execute(array(), 'single_value_array');
    $this->staffResourceTypes = $this->queryStaffResourceTypes();
  }

  public function export() {
    $this->buildAddressHeaders();
    $this->buildGeoHeaders();
    $this->buildStaffTypeHeaders();

    $lookUps = $this->buildLookUpArray();
    $lookUpContent = $this->gatherLookupValues($lookUps);

    $facilityExportRecords = $this->buildExportRecords();

    $exportResponse = $this->buildXls($facilityExportRecords, $lookUpContent);
    return $exportResponse;
  }

  public function buildExportRecords() {
    $facilityExportRecords = array();
    foreach ($this->facilityGeneralInfo as $fac) {
      $entry = array();
      $entry['Facility Name'] = $fac['f_facility_name'];
      $entry['Facility Code'] = $fac['f_facility_code'];
      $entry['Facility Resource Type Abbr'] = $fac['frt_facility_resource_type_abbr'];
      $entry['Facility Resource Status'] = $fac['frs_facility_resource_status'];
      $entry['Facility Capacity'] = $fac['fr_capacity'];
      $entry['Facility Activation Sequence'] = $fac['sfr_activation_sequence'];
      $entry['Facility Allocation Status'] = $fac['fras_facility_resource_allocation_status'];
      $entry['Facility Group'] = $fac['sfg_scenario_facility_group'];
      $entry['Facility Group Type'] = $fac['fgt_facility_group_type'];
      $entry['Facility Group Allocation Status'] = $fac['fgas_facility_group_allocation_status'];
      $entry['Facility Group Activation Sequence'] = $fac['sfg_activation_sequence'];

      // Facility email
      if (array_key_exists($fac['f_id'], $this->facilityEmail)) {
        $priorityNumber = key($this->facilityEmail[$fac['f_id']][$this->contactType]);
        $entry['Work Email'] = $this->facilityEmail[$fac['f_id']][$this->contactType][$priorityNumber];
      } else {
        $entry['Work Email'] = null;
      }

      // Facility phone numbers
      if (array_key_exists($fac['f_id'], $this->facilityPhone)) {
        $priorityNumber = key($this->facilityPhone[$fac['f_id']][$this->contactType]);
        $entry['Work Phone'] = $this->facilityPhone[$fac['f_id']][$this->contactType][$priorityNumber];
      } else {
        $entry['Work Phone'] = null;
      }

      // Facility address
      $addressId = null;
      if (array_key_exists($fac['f_id'], $this->facilityAddress)) {
        $priorityNumber = key($this->facilityAddress[$fac['f_id']][$this->contactType]);
        $addressId = $this->facilityAddress[$fac['f_id']][$this->contactType][$priorityNumber]['address_id'];

        foreach ($this->addressFormat as $key => $addr) {
          switch ($addr) {
            case 'line 1':
              $exp_index = 'Street 1';
              break;
            case 'line 2':
              $exp_index = 'Street 2';
              break;
            case 'zip5':
              $exp_index = 'Postal Code';
              break;
            default:
              $exp_index = ucwords($addr);
          }


          if (array_key_exists($addr, $this->facilityAddress[$fac['f_id']][$this->contactType][$priorityNumber])) {
            $entry[$exp_index] = $this->facilityAddress[$fac['f_id']][$this->contactType][$priorityNumber][$addr];
          } else {
            $entry[$exp_index] = null;
          }
        }
      } else {
        $entry = $entry + array_combine($addressHeaders, array_fill(count($entry), count($addressHeaders), NULL));
      }

      // facility geo.
      if (array_key_exists($fac['f_id'], $this->facilityGeo)) {
        if (isset($addressId)) {
          if (array_key_exists($addressId, $this->facilityGeo[$fac['f_id']])) {
            $entry['Longitude'] = $this->facilityGeo[$fac['f_id']][$addressId]['longitude'];
            $entry['Latitude'] = $this->facilityGeo[$fac['f_id']][$addressId]['latitude'];
          } else {
            $entry = $entry + array_combine(array('Longitude', 'Latitude'), array_fill(count($entry), 2, NULL));
          }
        } else {
          $entry = $entry + array_combine(array('Longitude', 'Latitude'), array_fill(count($entry), 2, NULL));
        }
      } else {
        $entry = $entry + array_combine(array('Longitude', 'Latitude'), array_fill(count($entry), 2, NULL));
      }

      // Use the staff resource types returned from the query above to get the actual
      // staff type minimums and maximums.
      foreach ($this->staffResourceTypes as $stfResType) {
        if (strtolower($stfResType) == 'staff') {
          $exp_index = 'generalist';
        } else {
          $exp_index = strtolower(str_replace(' ', '_', $stfResType));
        }

        if (array_key_exists($fac['sfr_id'], $this->facilityStaffResource)) {
          if (array_key_exists($stfResType, $this->facilityStaffResource[$fac['sfr_id']])) {
            $entry[$exp_index . '_min'] = $this->facilityStaffResource[$fac['sfr_id']][$stfResType]['minimum staff'];
            $entry[$exp_index . '_max'] = $this->facilityStaffResource[$fac['sfr_id']][$stfResType]['maximum staff'];
          } else {
            $entry = $entry + array_combine(array($exp_index . '_min', $exp_index . '_max'), array_fill(count($entry), 2, NULL));
          }
        } else {
          $entry = $entry + array_combine(array($exp_index . '_min', $exp_index . '_max'), array_fill(count($entry), 2, NULL));
        }
      }

      // Append the array just built to the total list of records.
      $facilityExportRecords[] = $entry;
    }
    return $facilityExportRecords;
  }

  public function buildXls($facilityExportRecords, $lookUpContent)
  {
    require_once 'PHPExcel/Cell/AdvancedValueBinder.php';
    PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

    $objPHPExcel = new sfPhpExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle("Sheet 1");

    $objPHPExcel->getProperties()->setCreator("Agasti 2.0");
    $objPHPExcel->getProperties()->setLastModifiedBy("Agasti 2.0");
    $objPHPExcel->getProperties()->setTitle("Facility List");

    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(12);


    $this->buildSheetHeaders($objPHPExcel);

    // Create the lookup/definition sheet. Function?
    $lookUpSheet = new PHPExcel_Worksheet($objPHPExcel, 'Lookup');
    // Populate the lookup sheet.
    $c = 0;
    foreach($lookUpContent as $key => $column) {
      $lookUpSheet->getCellByColumnAndRow($c, 1)->setValue($key);
      foreach($column as $k => $value) {
        $lookUpSheet->getCellByColumnAndRow($c, ($k + 2))->setValue($value);
      }
      $c++;
    }
    
    $highestColumn = $lookUpSheet->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    for ($i = $highestColumnIndex; $i >= 0; $i--) {
      $lookUpSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(true);
    }

    $row = 2;
    foreach ($facilityExportRecords as $rKey => $facilityExportRecord) {
      if($rKey <> 0 && (($rKey) % 4 == 0)) {
        // check if the row limit has been reached (up this to 64,000 later)
        // if we get in here, set the cell sizes on the sheet that was just finished.
        // Then make a new sheet, set it to active, build its headers, and reset
        // row to 2.
        $this->sizeCells($objPHPExcel);
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex($objPHPExcel->getActiveSheetIndex() + 1);
        $objPHPExcel->getActiveSheet()->setTitle("Sheet " . ($objPHPExcel->getActiveSheetIndex() + 1));
        $this->buildSheetHeaders($objPHPExcel);
        $row = 2;
      }
      foreach ($this->exportHeaders as $hKey => $heading) {
        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($hKey, $row)->setValue($facilityExportRecord[$heading]);
        if(array_key_exists($heading, $lookUpContent)) {
          $columnNumber = array_search($heading, array_keys($lookUpContent));
          $columnLetter = base_convert(($columnNumber +10), 10, 36);
          $topRow = count($lookUpContent[$heading]) + 1;
          $objValidation = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($hKey, $row)->getDataValidation();
          $objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
          $objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
          $objValidation->setAllowBlank(true);
          $objValidation->setShowInputMessage(true);
          $objValidation->setShowErrorMessage(true);
          $objValidation->setShowDropDown(true);
          $objValidation->setErrorTitle('Input error');
          $objValidation->setError('Value is not in list.');
          $objValidation->setPromptTitle('Pick from list');
          $objValidation->setPrompt('Please pick a value from the drop-down list.');
          $objValidation->setFormula1('Lookup!$' . $columnLetter . '$2:$'. $columnLetter . '$' . $topRow);
        }
      }
      $row++;
    }
    $this->sizeCells($objPHPExcel);

    // Add the lookup sheet. The null argument makes it the last sheet.
    $objPHPExcel->addSheet($lookUpSheet, null);

    $objPHPExcel->setActiveSheetIndex(0);
    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
    $todaydate = date("d-m-y");
    $todaydate = $todaydate . '-' . date("H-i-s");
    $fileName = 'Facilities';
    $fileName = $fileName . '-' . $todaydate;
    $fileName = $fileName . '.xls';
    $filePath = realpath(sys_get_temp_dir()) . '/' . $fileName;
    $objWriter->save($filePath);
    return array('fileName' => $fileName, 'filePath' => $filePath);
  }

  public function sizeCells($objPHPExcel)
  {
    $highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    for ($i = $highestColumnIndex; $i >= 0; $i--) {
      $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(true);
    }
  }
  public function buildSheetHeaders($objPHPExcel)
  {
    foreach ($this->exportHeaders as $hKey => $heading) {
      $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($hKey, 1)->setValue($heading);
    }
  }
  public function buildAddressHeaders() {
    // Construct header values for address fields.
    $addressHeaders = array();
    foreach ($this->addressFormat as $add) {
      switch ($add) {
        case 'line 1':
          $addressHeaders[] = 'Street 1';
          break;
        case 'line 2':
          $addressHeaders[] = 'Street 2';
          break;
        case 'zip5':
          $addressHeaders[] = 'Postal Code';
          break;
        default:
          $addressHeaders[] = ucwords($add);
      }
    }
    // Add the address headers to the list already defined, then add the geo headers.
    $this->exportHeaders = array_merge($this->exportHeaders, $addressHeaders);
  }

  public function buildGeoHeaders() {
    array_push($this->exportHeaders, "Longitude", "Latitude");
  }

  public function buildStaffTypeHeaders() {
    $stfHeaders = array();
    foreach ($this->staffResourceTypes as $stfResType) {
      if (strtolower($stfResType) == 'staff') {
        $stfResType = 'generalist';
      } else {
        $stfResType = strtolower(str_replace(' ', '_', $stfResType));
      }
      $stfHeaders[] = $stfResType . '_min';
      $stfHeaders[] = $stfResType . '_max';
    }
    $this->exportHeaders = array_merge($this->exportHeaders, $stfHeaders);
  }

  public function queryStaffResourceTypes() {
    $staffResourceTypes = Doctrine_Query::create()
                    ->select('srt.staff_resource_type, srt.id')
                    ->from('agStaffResourceType srt')
                    ->execute(array(), 'single_value_array');
    return $staffResourceTypes;
  }

  public function buildLookUpArray() {
    $lookUps = array(
        'Facility Resource Status' => array(
            'selectTable' => 'agFacilityResourceStatus',
            'selectColumn' => 'facility_resource_status',
            'whereColumn' => null,
            'whereValue' => null
        ),
        'Facility Resource Type Abbr' => array(
            'selectTable' => 'agFacilityResourceType',
            'selectColumn' => 'facility_resource_type_abbr',
            'whereColumn' => null,
            'whereValue' => null
        ),
        'Facility Allocation Status' => array(
            'selectTable' => 'agFacilityResourceAllocationStatus',
            'selectColumn' => 'facility_resource_allocation_status',
            'whereColumn' => null,
            'whereValue' => null
        ),
        'Facility Group Allocation Status' => array(
            'selectTable' => 'agFacilityGroupAllocationStatus',
            'selectColumn' => 'facility_group_allocation_status',
            'whereColumn' => null,
            'whereValue' => null
        ),
        'Borough' => array(
            'selectTable' => 'agAddressValue',
            'selectColumn' => 'value',
            'whereColumn' => 'address_element_id',
            'whereValue' => 8
        ),
        'State' => array(
            'selectTable' => 'agAddressValue',
            'selectColumn' => 'value',
            'whereColumn' => 'address_element_id',
            'whereValue' => 4
        ),
        'Facility Group Type' => array(
            'selectTable' => 'agFacilityGroupType',
            'selectColumn' => 'facility_group_type',
            'whereColumn' => null,
            'whereValue' => null
        )
    );
    return $lookUps;
  }

  /*
   * This function constructs a Doctrine Query based on the values of the parameter passed in.
   *
   * The query will return the values from a single column of a table, with the possiblity to
   * add a where clause to the query.
   *
   * @param $lookups array()  gatherLookupValues expects $lookups to be a two-dimensional array.
   *                          Keys of the outer level are expected to be column headers for a
   *                          lookup column, or some other kind of organized data list. However,
   *                          submitting a non-associative array will not cause any errors.
   *
   *                          The expected structure of the array is something like this:
   *
   *                          $lookUps = array(
   *                                       'Facility Resource Status' => array(
   *                                           'selectTable'  => 'agFacilityResourceStatus',
   *                                           'selectColumn' => 'facility_resource_status',
   *                                           'whereColumn'  => null,
   *                                           'whereValue' => null
   *                                       ),
   *                                       'Facility Resource Status' => array(
   *                                           'selectTable'  => 'agFacilityResourceStatus',
   *                                           'selectColumn' => 'facility_resource_status',
   *                                           'whereColumn'  => null,
   *                                           'whereValue' => null
   *                                       )
   *                          );
   *
   *                          Additional values of the $lookUps array can also be included.
   *                          The keys of the inner array musy be set to selectTable, selectColumn,
   *                          whereColumn, and whereValue.
   */

  public function gatherLookupValues($lookUps = null) {
    if (!isset($lookUps) || !is_array($lookUps)) {
      return null;
    }
    foreach ($lookUps as $key => $lookUp) {
      $lookUpQuery = Doctrine_Query::create()
                      ->select($lookUp['selectColumn'])
                      ->from($lookUp['selectTable']);
      if (isset($lookUp['whereColumn']) && isset($lookUp['whereValue'])) {
        //$lookUpQuery->where("'" . $lookUp['whereColumn'] . " = ?', " . $lookUp['whereValue']);
        $lookUpQuery->where($lookUp['whereColumn'] . " = " . $lookUp['whereValue']);
      }
      $returnedLookups[$key] = $lookUpQuery->execute(null, 'single_value_array');
    }
    return $returnedLookups;
  }

}