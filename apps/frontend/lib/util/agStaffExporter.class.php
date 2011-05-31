<?php
/* 
 * Class to export all staffs and their latest resource types.
 *
 * It is called by the staff action class.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

class agStaffExporter
{
  function __construct() {
    // The array index specify the order of the header in the export file.
    $this->header = array(
      'Entity ID',
      'First Name',
      'Middle Name',
      'Last Name',
      'Mobile Phone',
      'Home Phone',
      'Home Email',
      'Work Phone',
      'Work Email',
      'Home Address Line 1',
      'Home Address line 2',
      'Home Address City',
      'Home Address State',
      'Home Address Zip',
      'Home Address Country',
      'Home Latitude',
      'Home Longitude',
      'Work Address Line 1',
      'Work Address Line 2',
      'Work Address City',
      'Work Address State',
      'Work Address State',
      'Work Address Zip',
      'Work Address Country',
      'Work Latitude',
      'Work Longitude',
      'Organization',
      'Resource Type',
      'Resource Status',
      'Language 1',
      'L1 Speak',
      'L1 Read',
      'L1 Write',
      'Language 2',
      'L2 Speak',
      'L2 Read',
      'L2 Write',
      'Drivers License Class',
      'PMS ID',
      'Civil Service Title'
    );

    $this->lookUps = array(
        'state' => array(
            'selectTable' => 'agAddressValue',
            'selectColumn' => 'value',
            'whereColumn' => 'address_element_id',
            'whereValue' => 4
        ),
        'resourcetype' => array(
            'selectTable' => 'agStaffResourceType',
            'selectColumn' => 'staff_resource_type',
            'whereColumn' => null,
            'whereValue' => null
        ),
        'resourcestatus' => array(
            'selectTable' => 'agStaffResourceStatus',
            'selectColumn' => 'staff_resource_status',
            'whereColumn' => null,
            'whereValue' => null
        ),
        'language' => array(
            'selectTable' => 'agLanguage',
            'selectColumn' => 'language',
            'whereColumn' => null,
            'whereValue' => null
        ),
        'competency' => array(
            'selectTable' => 'agLanguageCompetency',
            'selectColumn' => 'language_competency',
            'whereColumn' => null,
            'whereValue' => null
        ),
        'organization' => array(
            'selectTable' => 'agOrganization',
            'selectColumn' => 'organization',
            'whereColumn' => null,
            'whereValue' => null
        )
    );
  }

  public function getPersonCustomFields(array $personCustomFields = NULL)
  {
    $q = agDoctrineQuery::create()
           ->select('pcf.person_custom_field')
               ->addSelect('pcf.id')
           ->from('agPersonCustomField AS pcf');

    if (!is_null($personCustomFields))
    {
      $q->whereIn('pcf.person_custom_field', $personCustomFields);
    }

    $results = $q->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
    return $results;
  }

  public function getStaffResourceGeneralInfo()
  {
    $staffInfo = array();

    $q = agDoctrineQuery::create()
      ->select('sr.id')
          ->addSelect('s.id')
          ->addSelect('p.id')
          ->addSelect('e.id')
          ->addSelect('srt.staff_resource_type')
          ->addSelect('o.id')
          ->addSelect('o.organization')
          ->addSelect('srs.id')
          ->addSelect('srs.staff_resource_status')
//          ->addSelect('pcf1.person_custom_field')
          ->addSelect('pcfv1.value')
//          ->addSelect('pcf2.person_custom_field')
          ->addSelect('pcfv2.value')
//          ->addSelect('pcf3.person_custom_field')
          ->addSelect('pcfv3.value')
        ->from('agStaffResource AS sr')
          ->innerJoin('sr.agStaff AS s')
          ->innerJoin('s.agPerson AS p')
          ->innerJoin('p.agEntity AS e')
          ->innerJoin('sr.agStaffResourceType AS srt')
          ->innerJoin('sr.agStaffResourceStatus AS srs')
          ->innerJoin('sr.agOrganization AS o');

    // Query for person_custom_field and their ids.
    $personCustomFields = array('Drivers License Class', 
                                'PMS ID', 
                                'Civil Service Title');
    $personCustomFieldIds = $this->getPersonCustomFields($personCustomFields);

    // Add person custom fields to query.
    $cnt = 1;
    foreach ($personCustomFields AS $customField)
    {
      $tblAlias = 'pcfv' . $cnt++;
      $q->leftJoin('p.agPersonCustomFieldValue AS ' . $tblAlias .
                   ' WITH ' . $tblAlias . '.person_custom_field_id = ' .
                   $personCustomFieldIds[$customField]);
    }

    $results = $q->execute(array(), DOCTRINE_CORE::HYDRATE_SCALAR);

    // Construct the array of the staff's custom fields.
    foreach ($results AS $row)
    {
      $cnt = 1;
      foreach ($personCustomFields AS $customField)
      {
        $tblAlias = 'pcfv' . $cnt++;
        $staffCustomFields[$row['sr_id']][$customField] = $row[$tblAlias . '_value'];
      }
    }

    // Construct arrays for later use from the query results
    foreach ($results AS $index => $staffResource)
    {
      $staffResourceId = array_shift($staffResource);
      $staffInfo[$staffResourceId] = $staffResource;
    }

    return $staffInfo;
  }

  public function export()
  {
    $staffInfo = $this->getStaffResourceGeneralInfo();

    // Collect email info
    // Collect phone info
    // Collect Address & Geo info
    // Build xls export files.
  }

  private function gatherLookupValues($lookUps = null)
  {
    foreach ($lookUps as $key => $lookUp) {
      $lookUpQuery = agDoctrineQuery::create()
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