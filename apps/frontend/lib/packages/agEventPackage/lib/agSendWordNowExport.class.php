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

  protected $eventId;

  /**
   * A method to act like this classes' own constructor
   * @param integer $eventId An event ID
   * @param string The export basename (as it would appear in an export)
   */
  protected function _init($eventId, $exportBaseName)
  {
    parent::__init($exportBaseName);

    // grab some global defaults and/or set new vars
    $this->eventId = $eventId;
    $this->eventStaffDeployedStatusId = agStaffAllocationStatus::getEventStaffDeployedStatusId();
  }

  /**
   * Method to set an export spec
   */
  protected function setExportSpec()
  {
    // define a few variables
    $exportSpec = array();
    $exportSpec['UNIQUE ID'] = array('type' => 'integer', 'mapsTo' => 'id');
    $exportSpec['LAST NAME'] = array('type' => 'string', 'length' => 30);
    $exportSpec['FIRST NAME'] = array('type' => 'string', 'length' => 30);
    $exportSpec['MIDDLE INITIAL'] = array('type' => 'string', 'length' => 1);
    $exportSpec['PIN Code'] = array();
    $exportSpec['GROUP ID'] = array();
    $exportSpec['GROUP DESCRIPTION'] = array();
    $exportSpec['ADDRESS 1'] = array('type' => 'string', 'length' => 60);
    $exportSpec['ADDRESS 2'] = array('type' => 'string', 'length' => 60);
    $exportSpec['CITY'] = array('type' => 'string', 'length' => 30);
    $exportSpec['STATE/PROVINCE'] = array('type' => 'string', 'length' => 80);
    $exportSpec['ZIP/POSTAL CODE'] = array('type' => 'string', 'length' => 10);
    $exportSpec['COUNTRY'] = array('type' => 'string', 'length' => 128);
    $exportSpec['TIME ZONE'] = array();
    $exportSpec['CUSTOM LABEL'] = array();
    $exportSpec['CUSTOM VALUE'] = array();
    $exportSpec['PHONE LABEL 1'] = array('type' => 'string', 'length' => 20);
    $exportSpec['PHONE COUNTRY CODE 1'] = array('type' => 'string', 'length' => 5);
    $exportSpec['PHONE 1'] = array('type' => 'string', 'length' => 14);
    $exportSpec['PHONE EXTENSION 1'] = array('type' => 'string', 'length' => 8);
    $exportSpec['CASCADE 1'] = array();
    $exportSpec['PHONE LABEL 2'] = array('type' => 'string', 'length' => 20);
    $exportSpec['PHONE COUNTRY CODE 2'] = array('type' => 'string', 'length' => 5);
    $exportSpec['PHONE 2'] = array('type' => 'string', 'length' => 14);
    $exportSpec['PHONE EXTENSION 2'] = array('type' => 'string', 'length' => 14);
    $exportSpec['CASCADE 2'] = array();
    $exportSpec['EMAIL LABEL 1'] = array('type' => 'string');
    $exportSpec['EMAIL 1'] = array('type' => 'string');
    $exportSpec['EMAIL LABEL 2'] = array('type' => 'string');
    $exportSpec['EMAIL 2'] = array('type' => 'string');
    $exportSpec['SMS LABEL 1'] = array();
    $exportSpec['SMS 1'] = array();
    $exportSpec['BB PIN LABEL 1'] = array();
    $exportSpec['BB PIN 1'] = array();

//        // Mapping column headers.
//    $nameMapping = array(
//      'family' => 'LAST NAME',
//      'given' => 'FIRST NAME',
//      'middle' => 'MIDDLE INITIAL'
//    );
//
//    $addressMapping = array(
//      'line 1' => 'ADDRESS 1',
//      'line 2' => 'ADDRESS 2',
//      'city' => 'CITY',
//      'state' => 'STATE/PROVINCE',
//      'zip5' => 'ZIP/POSTAL CODE',
//      'country' => 'COUNTRY'
//    );
//
//    $phoneMapping = array(
//      'contact type' => 'PHONE LABEL',
//      'country code' => 'PHONE COUNTRY CODE',
//      'phone' => 'PHONE',
//      'extension' => 'PHONE EXTENSION'
//    );
//
//    $emailMapping = array(
//      'contact type' => 'EMAIL LABEL',
//      'email' => 'EMAIL'
//    );

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
    $exportComponents[] = array('method' => 'setUniqueId');
    $exportComponents[] = array('method' => 'setName');

    // Set the exportComponents and components
    $this->exportComponents = $exportComponents;
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
    $q->select('evs.id AS event_staff_id')
        ->addSelect('sr.id AS staff_resource_id')
        ->addSelect('s.id AS staff_id')
        ->addSelect('p.id AS person_id')
        ->addSelect('e.id AS entity_id')
          ->innerJoin('sr.agStaff s')
          ->innerJoin('s.agPerson p')
          ->innerJoin('p.agEntity e');

    return $q;
  }
}
