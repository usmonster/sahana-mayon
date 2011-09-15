<?php

/**
 * Class to provide message export management for messaging functionality
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
abstract class agSendWordNowExport extends agExportHelper {

  protected $eventId,
            $agEntityAddressHelper,
            $agEntityPhoneHelper;

  protected $phoneContactTypes = array(),
            $addressContactTypes = array(),
            $emailContactTypes = array(),
            $personNameTypes = array(),
            $queryHeaders = array(),
            $emailHeaders = array(),
            $phoneHeaders = array(),
            $nameHeaders = array();

  /**
   * A method to act like this classes' own constructor
   * @param integer $eventId An event ID
   * @param string The export basename (as it would appear in an export)
   */
  protected function _init($eventId, $exportBaseName)
  {
    // grab some global defaults and/or set new vars
    $eventName = agDoctrineQuery::create()
      ->select('e.event_name')
        ->from('agEvent e')
        ->where('e.id = ?', $eventId)
        ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    $exportBaseName = $exportBaseName . '_' . str_replace(' ', '_', strtolower($eventName));

    parent::__init($exportBaseName);

    $this->eventId = $eventId;
    $this->eventStaffDeployedStatusId = agStaffAllocationStatus::getEventStaffDeployedStatusId();


    $this->agEntityAddressHelper = new agEntityAddressHelper();
    $this->agEntityPhoneHelper = new agEntityPhoneHelper();

    $this->nameHeaders['given'] = 'FIRST NAME';
    $this->nameHeaders['family'] = 'LAST NAME';
    $this->nameHeaders['middle'] = 'MIDDLE NAME';

    $this->nameSort = array('family', 'given', 'middle');

    $this->emailHeaders['work'] = 'EMAIL 1';
    $this->emailHeaders['personal'] = 'EMAIL 2';

    $this->addressContactTypes = agDoctrineQuery::create()
      ->select('act.address_contact_type')
          ->addSelect('act.id')
        ->from('agAddressContactType act')
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    $this->phoneContactTypes = agDoctrineQuery::create()
      ->select('pct.phone_contact_type')
          ->addSelect('pct.id')
        ->from('agPhoneContactType pct')
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
  }

  /**
   * Method to set an export spec
   */
  protected function setExportSpec()
  {
    // define a few variables
    $exportSpec = array();
    $exportSpec['UNIQUE ID'] = array('type' => 'integer', 'length' => 20);
    $exportSpec['LAST NAME'] = array('type' => 'string', 'length' => 30, 'mapsTo' => 'given');
    $exportSpec['FIRST NAME'] = array('type' => 'string', 'length' => 30, 'mapsTo' => 'family');
    $exportSpec['MIDDLE INITIAL'] = array('type' => 'string', 'length' => 1, 'mapsTo' => 'middle');
    $exportSpec['PIN Code'] = array('type' => 'integer');
    $exportSpec['GROUP ID'] = array('type' => 'integer', 'length' => 5);
    $exportSpec['GROUP DESCRIPTION'] = array('type' => 'string', 'length' => 128);
    $exportSpec['ADDRESS 1'] = array('type' => 'string', 'length' => 60, 'mapsTo' => 'line 1');
    $exportSpec['ADDRESS 2'] = array('type' => 'string', 'length' => 60, 'mapsTo' => 'line 2');
    $exportSpec['CITY'] = array('type' => 'string', 'length' => 30, 'mapsTo' => 'city');
    $exportSpec['STATE/PROVINCE'] = array('type' => 'string', 'length' => 80, 'mapsTo' => 'state');
    $exportSpec['ZIP/POSTAL CODE'] = array('type' => 'string', 'length' => 10, 'mapsTo' => 'zip5');
    $exportSpec['COUNTRY'] = array('type' => 'string', 'length' => 128, 'mapsTo' => 'country');
    $exportSpec['TIME ZONE'] = array();
    $exportSpec['CUSTOM LABEL'] = array();
    $exportSpec['CUSTOM VALUE'] = array();
    $exportSpec['PHONE LABEL 1'] = array('type' => 'string', 'length' => 20, 'mapsTo' => array('contact type'));
    $exportSpec['PHONE COUNTRY CODE 1'] = array('type' => 'string', 'length' => 5, 'mapsTo' => array('country code'));
    $exportSpec['PHONE 1'] = array('type' => 'string', 'length' => 14, 'mapsTo' => array('area code', 'phone'));
    $exportSpec['PHONE EXTENSION 1'] = array('type' => 'string', 'length' => 8, 'mapsTo' => array('extension'));
    $exportSpec['CASCADE 1'] = array();
    $exportSpec['PHONE LABEL 2'] = array('type' => 'string', 'length' => 20, 'mapsTo' => array('contact type'));
    $exportSpec['PHONE COUNTRY CODE 2'] = array('type' => 'string', 'length' => 5, 'mapsTo' => array('country code'));
    $exportSpec['PHONE 2'] = array('type' => 'string', 'length' => 14, 'mapsTo' => array('area code', 'phone'));
    $exportSpec['PHONE EXTENSION 2'] = array('type' => 'string', 'length' => 14, 'mapsTo' => array('extension'));
    $exportSpec['CASCADE 2'] = array();
    $exportSpec['EMAIL LABEL 1'] = array('type' => 'string', 'length' => 20, 'mapsTo' => 'contact type');
    $exportSpec['EMAIL 1'] = array('type' => 'string', 'length' => 60, 'mapsTo' => 'email');
    $exportSpec['EMAIL LABEL 2'] = array('type' => 'string', 'length' => 20, 'mapsTo' => 'contact type');
    $exportSpec['EMAIL 2'] = array('type' => 'string', 'length' => 60, 'mapsTo' => 'email');
    $exportSpec['SMS LABEL 1'] = array();
    $exportSpec['SMS 1'] = array();
    $exportSpec['BB PIN LABEL 1'] = array();
    $exportSpec['BB PIN 1'] = array();

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
    $exportComponents[] = array('method' => 'setAddress');
    $exportComponents[] = array('method' => 'setPhones');

    // Set the exportComponents and components
    $this->exportComponents = $exportComponents;
  }

  /**
   * Method to set the (optional) lookup sheet data.
   */
  protected function setLookupSheet()
  {

  }

  /**
   * Method to return a staff contact query used in the generation of contact messaging
   * @return agDoctrineQuery An agDoctrineQuery Object
   */
  protected function getEventStaffContactQuery()
  {
    // get our basic query construction
    $q = agEventStaff::getActiveEventStaffQuery($this->eventId);

    // add some more necessary bits
    $q->select('evs.id')
        ->addSelect('sr.id')
        ->addSelect('s.id')
        ->addSelect('p.id')
        ->addSelect('e.id')
        ->addSelect('o.id')
        ->addSelect('o.organization')
          ->innerJoin('sr.agStaff s')
          ->innerJoin('s.agPerson p')
          ->innerJoin('p.agEntity e') // a8
          ->innerJoin('sr.agOrganization o') // a9
        ->orderBy('o.organization');

    $this->queryHeaders['a4__id'] = 'UNIQUE ID';
    $this->queryHeaders['a9__id'] = 'GROUP ID';
    $this->queryHeaders['a9__organization'] = 'GROUP DESCRIPTION';

    // we'll use this from this point on
    $tableCounter = 9;

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
   * Method to set address data
   */
  protected function setAddress()
  {
    $eah = $this->agEntityAddressHelper;

    $addressFields = array('ADDRESS 1', 'ADDRESS 2', 'CITY', 'STATE/PROVINCE', 'ZIP/POSTAL CODE',
      'COUNTRY');

    foreach ($this->exportRawData as $rowId => $rowData) {
      $addresses = $eah->getEntityAddress(array($rowData->a8__id), TRUE, TRUE, agAddressHelper::ADDR_GET_TYPE);

      // skip this row if no addresses exist
      if (isset($addresses[$rowData->a8__id][1])) {
        $address = $addresses[$rowData->a8__id][1];

        foreach ($addressFields as $addressField) {
          $spec = $this->exportSpec[$addressField];
          $component = $spec['mapsTo'];
          if (array_key_exists($component, $address)) {
            $this->exportData[$rowId][$addressField] = substr($address[$component], 0, $spec['length']);
          }
        }
      }
    }
  }

  /**
   * Method to set phone done
   */
  protected function setPhones()
  {
    $eph = $this->agEntityPhoneHelper;

    $phoneFields = array('PHONE LABEL', 'PHONE COUNTRY CODE', 'PHONE', 'PHONE EXTENSION');
    $suffixes = array(1, 2);

    foreach ($this->exportRawData as $rowId => $rowData) {

      $phones = $eph->getEntityPhone(array($rowData->a8__id), TRUE, FALSE, agPhoneHelper::PHN_GET_COMPONENT_SEGMENTS);

      // skip this row if no phone exists
      if (isset($phones[$rowData->a8__id])) {
        $phones = $phones[$rowData->a8__id];

        foreach ($suffixes as $suffix) {
          $sKey = $suffix - 1;
          if (array_key_exists($sKey, $phones)) {
            $phone = $phones[$sKey][1];
            $phone['contact type'] = $phones[$sKey][0];

            foreach ($phoneFields as $phoneField) {
              $phoneField = $phoneField . ' ' . $suffix;
              $spec = $this->exportSpec[$phoneField];
              $componentVal = '';
              foreach ($spec['mapsTo'] as $component) {
                  if (array_key_exists($component, $phone)) {
                    $componentVal = $componentVal . $phone[$component];
                }
              }

              if (!empty($componentVal)) {
                $this->exportData[$rowId][$phoneField] = substr($componentVal, 0, $spec['length']);
              }
            }
          }
        }
      }
    }
  }
}
