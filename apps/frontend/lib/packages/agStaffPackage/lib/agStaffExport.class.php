<?php

/**
 * Class to provide staff export management for import functionality
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agStaffExport extends agExportHelper {

  protected   $organizationId,
              $numLanguages = 2,
              $agEntityAddressHelper;
  
  protected   $phoneContactTypes = array(),
              $addressContactTypes = array(),
              $emailContactTypes = array(),
              $personNameTypes = array(),
              $personCustomFields = array(),
              $queryHeaders = array(),
              $addressHeaders = array(),
              $emailHeaders = array(),
              $phoneHeaders = array(),
              $nameHeaders = array();

  /**
   * Method to return an instance of this class
   * @param integer $eventId An event ID
   * @param string The export basename (as it would appear in an export)
   * @return agStaffExport An instance of this class
   */
  public static function getInstance($exportBaseName)
  {
    $self = new self();
    $self->_init($exportBaseName);
    return $self;
  }

  /**
   * A method to act like this classes' own constructor
   * @param integer $eventId An event ID
   * @param string The export basename (as it would appear in an export)
   */
  protected function _init($exportBaseName)
  {
    parent::__init($exportBaseName);

    $this->agEntityAddressHelper = new agEntityAddressHelper();

    $this->nameHeaders['given'] = 'First Name';
    $this->nameHeaders['family'] = 'Last Name';
    $this->nameHeaders['middle'] = 'Middle Name';

    $this->nameSort = array('family', 'given', 'middle');

    $this->phoneHeaders['mobile'] = 'Mobile Phone';
    $this->phoneHeaders['home'] = 'Mobile Phone';
    $this->phoneHeaders['work'] = 'Work Phone';

    $this->emailHeaders['personal'] = 'Home Email';
    $this->emailHeaders['work'] = 'Work Email';


    $this->addressHeaders['line 1'] = 'Address Line 1';
    $this->addressHeaders['line 2'] = 'Address Line 2';
    $this->addressHeaders['city'] = 'Address City';
    $this->addressHeaders['state'] = 'Address State';
    $this->addressHeaders['zip5'] = 'Address Zip';
    $this->addressHeaders['country'] = 'Address Country';
    $this->addressHeaders['latitude'] = 'Latitude';
    $this->addressHeaders['longitude'] = 'Longitude';

    $this->addressContactTypes = agDoctrineQuery::create()
      ->select('act.address_contact_type')
          ->addSelect('act.id')
        ->from('agAddressContactType act')
        ->whereIn('act.address_contact_type', array('home', 'work'))
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    $this->phoneContactTypes = agDoctrineQuery::create()
      ->select('pct.phone_contact_type')
          ->addSelect('pct.id')
        ->from('agPhoneContactType pct')
        ->whereIn('pct.phone_contact_type', array_keys($this->phoneHeaders))
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    $this->emailContactTypes = agDoctrineQuery::create()
      ->select('ect.email_contact_type')
          ->addSelect('ect.id')
        ->from('agEmailContactType ect')
        ->whereIn('ect.email_contact_type', array_keys($this->emailHeaders))
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    $this->personNameTypes = agDoctrineQuery::create()
      ->select('pnt.person_name_type')
          ->addSelect('pnt.id')
        ->from('agPersonNameType pnt')
        ->whereIn('pnt.person_name_type', array_keys($this->nameHeaders))
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    $this->personCustomFields = agDoctrineQuery::create()
      ->select('pcf.person_custom_field')
          ->addSelect('pcf.id')
        ->from('agPersonCustomField pcf')
        ->whereIn('pcf.person_custom_field', array('Drivers License Class', 'PMS ID', 'Civil Service Title'))
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to set the (optional) lookup sheet data (implement as empty if a lookup sheet is not desired)
   */
  protected function setLookupSheet()
  {
    // first get the raw data
    $states = array();
    $states[] = 'AL';
    $states[] = 'AK';
    $states[] = 'AZ';
    $states[] = 'CA';
    $states[] = 'CO';
    $states[] = 'CT';
    $states[] = 'DE';
    $states[] = 'FL';
    $states[] = 'GA';
    $states[] = 'HI';
    $states[] = 'ID';
    $states[] = 'IL';
    $states[] = 'IN';
    $states[] = 'IA';
    $states[] = 'KS';
    $states[] = 'KY';
    $states[] = 'LA';
    $states[] = 'ME';
    $states[] = 'MD';
    $states[] = 'MA';
    $states[] = 'MI';
    $states[] = 'MN';
    $states[] = 'MS';
    $states[] = 'MO';
    $states[] = 'MT';
    $states[] = 'NE';
    $states[] = 'NV';
    $states[] = 'NH';
    $states[] = 'NJ';
    $states[] = 'NM';
    $states[] = 'NY';
    $states[] = 'NC';
    $states[] = 'ND';
    $states[] = 'OH';
    $states[] = 'OK';
    $states[] = 'OR';
    $states[] = 'PA';
    $states[] = 'RI';
    $states[] = 'SC';
    $states[] = 'SD';
    $states[] = 'TN';
    $states[] = 'TX';
    $states[] = 'UT';
    $states[] = 'VT';
    $states[] = 'VA';
    $states[] = 'WA';
    $states[] = 'WV';
    $states[] = 'WI';
    $states[] = 'WY';
    
    $resourcetypes = agDoctrineQuery::create()
      ->select('srt.staff_resource_type')
        ->from('agStaffResourceType srt')
      ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    $resourcestatuses = agDoctrineQuery::create()
      ->select('srs.staff_resource_status')
        ->from('agStaffResourceStatus srs')
      ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    $languages = agDoctrineQuery::create()
      ->select('l.language')
        ->from('agLanguage l')
        ->where('l.app_display = ?', TRUE)
      ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    $competencies = agDoctrineQuery::create()
      ->select('lc.language_competency')
        ->from('agLanguageCompetency lc')
      ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    $organizations = agDoctrineQuery::create()
      ->select('o.organization')
        ->from('agOrganization o')
      ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    $fields = array(2 => 'states', 4 => 'resourcetypes', 5 => 'resourcestatuses', 6 => 'languages',
      7 => 'competencies', 8 => 'organizations');

    // just find our max row count
    $maxCt = 0;
    foreach ($fields as $field) {
      $ctVar = $field . 'Ct';
      $$ctVar = count(${$field});
      $maxCt = ($maxCt < ${$ctVar}) ? ${$ctVar} : $maxCt;
    }

    // now add our two blank columns
    for($i = 0; $i <= $maxCt; $i++) {
      $this->lookupSheet[($i+2)][1] = '';
      $this->lookupSheet[($i+2)][3] = '';
    }

    // iterate each field type then iterate columns adding values or blanks
    foreach ($fields as $col => $field) {
      $this->lookupSheet[2][$col] = $field;

      for($i = 0; $i < $maxCt; $i++) {

        if (isset(${$field}[$i])) {
          $this->lookupSheet[($i+3)][$col] = ${$field}[$i];
        } else {
          $this->lookupSheet[($i+3)][$col] = '';
        }
      }
    }

    // do some necessary sorting
    ksort($this->lookupSheet);
    foreach ($this->lookupSheet as &$arr) {
      ksort($arr);
    }
    unset($arr);
  }

  /**
   * Method to set an export spec
   */
  protected function setExportSpec()
  {
    // define a few variables
    $exportSpec = array();
    $exportSpec['Entity ID'] = array('type' => 'integer', 'length' => 20);
    $exportSpec['First Name'] = array('type' => 'string', 'length' => 64,);
    $exportSpec['Middle Name'] = array('type' => 'string', 'length' => 64);
    $exportSpec['Last Name'] = array('type' => 'string', 'length' => 64);
    $exportSpec['Mobile Phone'] = array('type' => 'string', 'length' => 16);
    $exportSpec['Home Phone'] = array('type' => 'string', 'length' => 16);
    $exportSpec['Home Email'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Work Phone'] = array('type' => 'string', 'length' => 16);
    $exportSpec['Work Email'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Home Address Line 1'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Home Address Line 2'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Home Address City'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Home Address State'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Home Address Zip'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Home Address Country'] = array('type' => 'string', 'length' => 128);
    $exportSpec['Home Latitude'] = array('type' => 'string', 'length' => 13);
    $exportSpec['Home Longitude'] = array('type' => 'string', 'length' => 13);
    $exportSpec['Work Address Line 1'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Work Address Line 2'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Work Address City'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Work Address State'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Work Address Zip'] = array('type' => 'string', 'length' => 255);
    $exportSpec['Work Address Country'] = array('type' => 'string', 'length' => 128);
    $exportSpec['Work Latitude'] = array('type' => 'string', 'length' => 13);
    $exportSpec['Work Longitude'] = array('type' => 'string', 'length' => 13);
    $exportSpec['Organization'] = array('type' => "string", 'length' => 128);
    $exportSpec['Resource Type'] = array('type' => "string", 'length' => 64);
    $exportSpec['Resource Status'] = array('type' => "string", 'length' => 30);
    $exportSpec['Language 1'] = array('type' => "string", 'length' => 128);
    $exportSpec['L1 Speak'] = array('type' => "string", 'length' => 64);
    $exportSpec['L1 Read'] = array('type' => "string", 'length' => 64);
    $exportSpec['L1 Write'] = array('type' => "string", 'length' => 64);
    $exportSpec['Language 2'] = array('type' => "string", 'length' => 128);
    $exportSpec['L2 Speak'] = array('type' => "string", 'length' => 64);
    $exportSpec['L2 Read'] = array('type' => "string", 'length' => 64);
    $exportSpec['L2 Write'] = array('type' => "string", 'length' => 64);
    $exportSpec['Drivers License Class'] = array('type' => "string", 'length' => 255);
    $exportSpec['PMS ID'] = array('type' => "string", 'length' => 255);
    $exportSpec['Civil Service Title'] = array('type' => "string", 'length' => 255);

    // Set the exportSpec and components
    $this->exportSpec = $exportSpec;
  }

  /**
   * Method to set export components
   */
  protected function setExportComponents()
  {
    // define a few variables
    $exportComponents = array();
    $exportComponents[] = array('method' => 'setBasics');
    $exportComponents[] = array('method' => 'setLanguages');
    $exportComponents[] = array('method' => 'setAddresses');

    // Set the exportComponents and components
    $this->exportComponents = $exportComponents;
  }

  /**
   * Method to get the base doctrine query object used in export
   * @return agDoctrineQuery A doctrine query object
   */
  protected function getDoctrineQuery()
  {
    // build our basic query (that doesn't need fancy stuff)
    $q = agDoctrineQuery::create()
      ->select('sr.id')
      ->addSelect('s.id')
      ->addSelect('p.id')
      ->addSelect('e.id')
      ->addSelect('srt.id')
      ->addSelect('srt.staff_resource_type')
      ->addSelect('srs.id')
      ->addSelect('srs.staff_resource_status')
      ->addSelect('o.organization')
      ->from('agStaffResource sr') //a1
      ->innerJoin('sr.agStaff AS s') //a2
      ->innerJoin('s.agPerson AS p') //a3
      ->innerJoin('p.agEntity AS e') //a4
      ->innerJoin('sr.agStaffResourceType srt') //a5
      ->innerJoin('sr.agStaffResourceStatus srs') //a6
      ->innerJoin('sr.agOrganization o') //a7
      ->where('srs.is_available = ?', TRUE);

    $this->queryHeaders['a4__id'] = 'Entity ID';
    $this->queryHeaders['a5__staff_resource_type'] = 'Resource Type';
    $this->queryHeaders['a6__staff_resource_status'] = 'Resource Status';
    $this->queryHeaders['a7__organization'] = 'Organization';

    // we'll use this from this point on
    $tableCounter = 7;

    $rawNameSortHeader = array();
    // loop through each of the name types
    foreach ($this->personNameTypes as $nc => $ncId) {


      // build the clause strings
      $selectId = 'pmpn' . $ncId . '.id';
      $column = 'pn' . $ncId . '.person_name';
      $pmpnJoin = 'p.agPersonMjAgPersonName AS pmpn' . $ncId . ' WITH pmpn' . $ncId .
          '.person_name_type_id = ' . $ncId;
      $pnJoin = 'pmpn' . $ncId . '.agPersonName AS pn' . $ncId;

      $where = '(' .
          '(EXISTS (' .
          'SELECT sub' . $ncId . '.id ' .
          'FROM agPersonMjAgPersonName AS sub' . $ncId . ' ' .
          'WHERE sub' . $ncId . '.person_name_type_id = ? ' .
          'AND sub' . $ncId . '.person_id = pmpn' . $ncId . '.person_id ' .
          'HAVING MIN(sub' . $ncId . '.priority) = pmpn' . $ncId . '.priority' .
          ')) ' .
          'OR (pmpn' . $ncId . '.id IS NULL)' .
          ')';

      // add the clauses to the query
      $q->addSelect($selectId)
          ->addSelect($column)
          ->leftJoin($pmpnJoin)
          ->leftJoin($pnJoin)
          ->andWhere($where, $ncId);

      $tableCounter += 2;
      $header = 'a' . $tableCounter . '__person_name';
      $this->queryHeaders[$header] = $this->nameHeaders[$nc];
      $rawNameSortHeader[$nc] = $column;
    }

    // get our name headers
    foreach ($this->nameSort as $index => $nc) {
      $sortColumns[$index] = $rawNameSortHeader[$nc];
    }

    // make our orderBy clause
    $orderBy = implode(", ", $sortColumns);
    $orderBy = 'o.organization, ' . $orderBy;
    $q->addOrderBy($orderBy);


    // loop through each of the email types
    foreach ($this->emailContactTypes as $val => $id) {
      // build the clause strings
      $selectId1 = 'eec' . $id . '.id';
      $selectId2 = 'ec' . $id . '.id';
      $column = 'ec' . $id . '.email_contact';
      $entityJoin = 'e.agEntityEmailContact AS eec' . $id . ' WITH eec' . $id .
          '.email_contact_type_id = ' . $id;
      $valueJoin = 'eec' . $id . '.agEmailContact AS ec' . $id;

      $where = '(' .
          '(EXISTS (' .
          'SELECT subE' . $id . '.id ' .
          'FROM agEntityEmailContact AS subE' . $id . ' ' .
          'WHERE subE' . $id . '.email_contact_type_id = ? ' .
          'AND subE' . $id . '.entity_id = eec' . $id . '.entity_id ' .
          'HAVING MIN(subE' . $id . '.priority) = eec' . $id . '.priority' .
          ')) ' .
          'OR (eec' . $id . '.id IS NULL)' .
          ')';

      // add the clauses to the query
      $q->addSelect($selectId1)
        ->addSelect($selectId2)
          ->addSelect($column)
          ->leftJoin($entityJoin)
          ->leftJoin($valueJoin)
          ->andWhere($where, $id);

      $tableCounter += 2;
      $header = 'a' . $tableCounter . '__email_contact';
      $this->queryHeaders[$header] = $this->emailHeaders[$val];
    }

    // loop through each of the email types
    foreach ($this->phoneContactTypes as $val => $id) {
      // build the clause strings
      $selectId1 = 'epc' . $id . '.id';
      $selectId2 = 'pc' . $id . '.id';
      $column = 'pc' . $id . '.phone_contact';
      $entityJoin = 'e.agEntityPhoneContact AS epc' . $id . ' WITH epc' . $id .
          '.phone_contact_type_id = ' . $id;
      $valueJoin = 'epc' . $id . '.agPhoneContact AS pc' . $id;

      $where = '(' .
          '(EXISTS (' .
          'SELECT subP' . $id . '.id ' .
          'FROM agEntityPhoneContact AS subP' . $id . ' ' .
          'WHERE subP' . $id . '.phone_contact_type_id = ? ' .
          'AND subP' . $id . '.entity_id = epc' . $id . '.entity_id ' .
          'HAVING MIN(subP' . $id . '.priority) = epc' . $id . '.priority' .
          ')) ' .
          'OR (epc' . $id . '.id IS NULL)' .
          ')';

      // add the clauses to the query
      $q->addSelect($selectId1)
        ->addSelect($selectId2)
          ->addSelect($column)
          ->leftJoin($entityJoin)
          ->leftJoin($valueJoin)
          ->andWhere($where, $id);

      $tableCounter += 2;
      $header = 'a' . $tableCounter . '__phone_contact';
      $this->queryHeaders[$header] = $this->phoneHeaders[$val];
    }

    // custom fields are much simpler
    foreach ($this->personCustomFields as $val => $id) {
      $alias = 'pcfv' . $id;
      $selectId = $alias . '.id';
      $column = $alias . '.value';
      $join = 'p.agPersonCustomFieldValue AS ' . $alias . ' WITH ' . $alias .
        '.person_custom_field_id = ' . $id;

      $q->addSelect($selectId)
        ->addSelect($column)
        ->leftJoin($join);

      $tableCounter++;
      $header = 'a' . $tableCounter . '__value';
      $this->queryHeaders[$header] = $val;
    }

    return $q;
  }

  /**
   * Method to set the basic query-returned data
   */
  protected function setBasics()
  {
    foreach ($this->exportRawData as $rowId => $rawData) {
      foreach($this->queryHeaders as $header => $exportColumn) {
        if (!is_null($rawData->$header)) {
        $this->exportData[$rowId][$exportColumn] = substr($rawData->$header, 0,
            $this->exportSpec[$exportColumn]['length']);
        }
      }
    }
  }

  /**
   * Method to set language exportData
   */
  protected function setLanguages()
  {
    $col = array_search('Entity ID', $this->queryHeaders);
    $entityIds = array();
    $results = array();
    $tempResults = array();

    foreach ($this->exportRawData as $rowId => $rowData) {
      $entityIds[$rowData->$col] = $rowId;
    }

    $q = agDoctrineQuery::create()
      ->select('p.entity_id')
        ->addSelect('l.language')
        ->addSelect('lf.language_format')
        ->addSelect('lc.language_competency')
      ->from('agPerson p')
        ->innerJoin('p.agPersonMjAgLanguage pmal')
        ->innerJoin('pmal.agLanguage l')
        ->innerJoin('pmal.agPersonLanguageCompetency plc')
        ->innerJoin('plc.agLanguageFormat lf')
        ->innerJoin('plc.agLanguageCompetency lc')
      ->whereIn('p.entity_id', array_keys($entityIds))
      ->orderBy('p.entity_id ASC, pmal.priority ASC');

    $tempResults = $q->execute(array(), Doctrine_Core::HYDRATE_NONE);

    foreach ($tempResults as $values) {
      $results[$values[0]][$values[1]][$values[2]] = $values[3];
    }
    unset($tempResults);

    foreach ($results as $entityId => $languages) {
      $rowId = $entityIds[$entityId];

      $i = 0;
      foreach ($languages as $language => $formats) {
        $i++;
        $lHeader ='Language ' . $i;
        $this->exportData[$rowId][$lHeader] = substr($language, 0,
          $this->exportSpec[$lHeader]['length']);

        foreach ($formats as $format => $competency) {
          $fHeader = 'L' . $i . ' ' . ucwords($format);
          $this->exportData[$rowId][$fHeader] = substr($competency, 0,
            $this->exportSpec[$fHeader]['length']);
        }

        if ($i >= $this->numLanguages) {
          break;
        }
      }
    }
  }

  protected function setAddresses()
  {
    $entityIds = array();
    $eah = $this->agEntityAddressHelper;

    $col = array_search('Entity ID', $this->queryHeaders);
    foreach ($this->exportRawData as $rowId => $rowData) {
      $entityIds[$rowData->$col] = $rowId;
    }

    $entityAddresses = $eah->getEntityAddressByType(array_keys($entityIds), TRUE, TRUE,
      agAddressHelper::ADDR_GET_TYPE, array(TRUE));

    foreach ($entityAddresses as $entityId => $addresses) {
      $rowId = $entityIds[$entityId];
      
      foreach($addresses as $contactType => $address) {
        if (isset($this->addressContactTypes[$contactType]) && isset($address[0][0])) {
          $contactType = ucwords($contactType);

          foreach ($address[0][0] as $element => $value) {
            if(isset($this->addressHeaders[$element])) {
              $header = $contactType . ' ' . $this->addressHeaders[$element];
              $this->exportData[$rowId][$header] = $value;
            }
          }
        }
      }
    }
  }

}
