<?php
class agFacilityExportHelper
{
  public static function export()
  {
    // Define some variables and definitions.
    $primaryOnly = TRUE;
    $contactType = 'work';
    $addressStandard = 'us standard';

    // This is the start of the definition list for XLS headers.
    $exportHeaders = array('Facility Name', 'Facility Code', 'Facility Resource Type Abbr',
        'Facility Resource Status', 'Facility Capacity', 'Facility Activation Sequence',
        'Facility Allocation Status', 'Facility Group', 'Facility Group Type',
        'Facility Group Allocation Status', 'Faciltiy Group Activation Sequence',
        'Work Email', 'Work Phone');
    $staffResourceTypes = self::queryStaffResourceTypes();

    // Use the agFacilityHelper to gather information specific to facilities.
    $facilityGeneralInfo = agFacilityHelper::facilityGeneralInfo('Scenario');
    $facilityAddress = agFacilityHelper::facilityAddress($addressStandard, $primaryOnly, $contactType);
    $facilityGeo = agFacilityHelper::facilityGeo($primaryOnly, $contactType);
    $facilityEmail = agFacilityHelper::facilityEmail($primaryOnly, $contactType);
    $facilityPhone = agFacilityHelper::facilityPhone($primaryOnly, $contactType);
    $facilityStaffResource = agFacilityHelper::facilityStaffResource();

    $addressFormat = Doctrine_Query::create()
            ->select('ae.address_element')
            ->from('agAddressElement ae')
            ->innerJoin('ae.agAddressFormat af')
            ->innerJoin('af.agAddressStandard astd')
            ->where('astd.address_standard=?', $addressStandard)
            ->andWhere('ae.address_element<>?', 'zip+4')
            ->orderBy('af.line_sequence, af.inline_sequence')
            ->execute(array(), 'single_value_array');
    
    self::buildAddressHeaders($exportHeaders, $addressStandard, $addressFormat);
    self::buildGeoHeaders($exportHeaders);
    self::buildStaffTypeHeaders($exportHeaders, $staffResourceTypes);

    $lookUps = self::buildLookUpArray();
    $lookUpContent = self::gatherLookupValues($lookUps);

    $facilityExportRecords = self::buildExportRecords($facilityGeneralInfo, $facilityAddress, $facilityGeo, $facilityEmail, $facilityPhone, $facilityStaffResource, $contactType, $addressFormat, $staffResourceTypes);
    return $facilityExportRecords;
  }

  public static function buildExportRecords($facilityGeneralInfo, $facilityAddress, $facilityGeo, $facilityEmail, $facilityPhone, $facilityStaffResource, $contactType, $addressFormat, $staffResourceTypes)
  {
    $facilityExportRecords = array();
    foreach ($facilityGeneralInfo as $fac)
    {
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
      if (array_key_exists($fac['f_id'], $facilityEmail))
      {
        $priorityNumber = key($facilityEmail[$fac['f_id']][$contactType]);
        $entry['Work Email'] = $facilityEmail[$fac['f_id']][$contactType][$priorityNumber];
      } else {
        $entry['Work Email'] = null;
      }

      // Facility phone numbers
      if (array_key_exists($fac['f_id'], $facilityPhone))
      {
        $priorityNumber = key($facilityPhone[$fac['f_id']][$contactType]);
        $entry['Work Phone'] = $facilityPhone[$fac['f_id']][$contactType][$priorityNumber];
      } else {
        $entry['Work Phone'] = null;
      }

      // Facility address
      $addressId = null;
      if (array_key_exists($fac['f_id'], $facilityAddress))
      {
        $priorityNumber = key($facilityAddress[ $fac['f_id'] ][$contactType]);
        $addressId = $facilityAddress[ $fac['f_id'] ][$contactType][$priorityNumber]['address_id'];

        foreach($addressFormat as $key => $addr)
        {
          switch ($addr)
          {
            case 'line 1':
              $exp_index = 'Street 1';
              break;
            case 'line 2':
              $exp_index = 'Street 2';
              break;
            case 'zip5':
              $exp_index = 'Postal Code';
            default:
              $exp_index = ucwords($addr);
           }


          if (array_key_exists($addr, $facilityAddress[ $fac['f_id'] ][$contactType][$priorityNumber]))
          {
            $entry[ $exp_index ] = $facilityAddress[ $fac['f_id'] ][$contactType][$priorityNumber][$addr];
          } else {
            $entry[ $exp_index ] = null;
          }
        }
      } else {
        $entry = $entry + array_combine($addressHeaders, array_fill(count($entry), count($addressHeaders), NULL));
      }

      // facility geo.
      if (array_key_exists($fac['f_id'], $facilityGeo))
      {
        if (isset($addressId))
        {
          if (array_key_exists($addressId, $facilityGeo[ $fac['f_id'] ]))
          {
            $entry['Longitude'] = $facilityGeo[ $fac['f_id'] ][$addressId]['longitude'];
            $entry['Latitude'] = $facilityGeo[ $fac['f_id'] ][$addressId]['latitude'];
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
      foreach($staffResourceTypes as $stfResType)
      {
        if (strtolower($stfResType) == 'staff')
        {
          $exp_index = 'generalist';
        } else {
          $exp_index = strtolower(str_replace(' ', '_', $stfResType));
        }

        if (array_key_exists($fac['sfr_id'], $facilityStaffResource))
        {
          if(array_key_exists($stfResType, $facilityStaffResource[ $fac['sfr_id'] ]))
          {
            $entry[$exp_index . '_min'] = $facilityStaffResource[ $fac['sfr_id'] ][$stfResType]['minimum staff'];
            $entry[$exp_index . '_max'] = $facilityStaffResource[ $fac['sfr_id'] ][$stfResType]['maximum staff'];
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
  
  public static function buildAddressHeaders($exportHeaders, $addressStandard, $addressFormat)
  {
    // Construct header values for address fields.
    $addressHeaders = array();
    foreach ($addressFormat as $add)
    {
      switch ($add)
      {
        case 'line 1':
          $addressHeaders[] = 'Street 1';
          break;
        case 'line 2':
          $addressHeaders[] = 'Street 2';
          break;
        case 'zip5':
          $addressHeaders[] = 'Postal Code';
        default:
          $addressHeaders[] = ucwords($add);
       }
    }
    // Add the address headers to the list already defined, then add the geo headers.
    $exportHeaders = array_merge($exportHeaders,$addressHeaders);
    return $exportHeaders;
  }

  public static function buildGeoHeaders($exportHeaders)
  {
    array_push($exportHeaders, "longitude", "latitude");
    return $exportHeaders;
  }

  public static function buildStaffTypeHeaders($exportHeaders, $staffResourceTypes)
  {
    $stfHeaders = array();
    foreach($staffResourceTypes as $stfResType)
    {
      if (strtolower($stfResType) == 'staff')
      {
        $stfResType = 'generalist';
      } else {
        $stfResType = strtolower(str_replace(' ', '_', $stfResType));
      }
      $stfHeaders[] = $stfResType . '_min';
      $stfHeaders[] = $stfResType . '_max';
    }
    $exportHeaders = array_merge($exportHeaders, $stfHeaders);
    return $exportHeaders;
  }

  public static function queryStaffResourceTypes()
  {
    $staffResourceTypes = Doctrine_Query::create()
            ->select('srt.staff_resource_type, srt.id')
            ->from('agStaffResourceType srt')
            ->execute(array(), 'single_value_array');
    return $staffResourceTypes;
  }

  public static function buildLookUpArray()
  {
    $lookUps = array(
        'Facility Resource Status' => array(
            'selectTable'  => 'agFacilityResourceStatus',
            'selectColumn' => 'facility_resource_status',
            'whereColumn'  => null,
            'whereValue' => null
        ),
        'Facility Resource Type Abbr' => array(
            'selectTable'  => 'agFacilityResourceType',
            'selectColumn' => 'facility_resource_type_abbr',
            'whereColumn'  => null,
            'whereValue' => null
        ),
        'Facility Allocation Status' => array(
            'selectTable'  => 'agFacilityResourceAllocationStatus',
            'selectColumn' => 'facility_resource_allocation_status',
            'whereColumn'  => null,
            'whereValue' => null
        ),
        'Facility Group Allocation Status' => array(
            'selectTable'  => 'agFacilityGroupAllocationStatus',
            'selectColumn' => 'facility_group_allocation_status',
            'whereColumn'  => null,
            'whereValue' => null
        ),
        'Burrough' => array(
            'selectTable'  => 'agAddressValue',
            'selectColumn' => 'value',
            'whereColumn'  => 'address_element_id',
            'whereValue' => 8
        ),
        'State' => array(
            'selectTable'  => 'agAddressValue',
            'selectColumn' => 'value',
            'whereColumn'  => 'address_element_id',
            'whereValue' => 4
        ),
        'Facility Group Type' => array(
            'selectTable'  => 'agFacilityGroupType',
            'selectColumn' => 'facility_group_type',
            'whereColumn'  => null,
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
  public static function gatherLookupValues($lookUps = null)
  {
    if(!isset($lookUps) || !is_array($lookUps)) {
      return null;
    }
    foreach($lookUps as $key => $lookUp) {
      $lookUpQuery = Doctrine_Query::create()
        ->select($lookUp['selectColumn'])
        ->from($lookUp['selectTable']);
      if(isset($lookUp['whereColumn']) && isset($lookUp['whereValue'])) {
        $lookUpQuery->where($lookUp['whereColumn'] . " = ?,' " . $lookUp['whereValue']);
        $lookUpQuery->where('address_element_id = ?', 8);
      }
      $returnedLookups[$key] = $lookUpQuery->execute(null,'single_value_array');
    }
    return $returnedLookups;
  }
}