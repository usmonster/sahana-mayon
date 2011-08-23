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
  }

  /**
   * Method to set an export spec
   */
  protected function setExportSpec()
  {
    // define a few variables
    $exportSpec = array();
    $exportSpec['UNIQUE ID'] = array('type' => 'integer');
    $exportSpec['LAST NAME'] = array('type' => 'string', 'length' => 30, 'mapsTo' => 'given');
    $exportSpec['FIRST NAME'] = array('type' => 'string', 'length' => 30, 'mapsTo' => 'family');
    $exportSpec['MIDDLE INITIAL'] = array('type' => 'string', 'length' => 1, 'mapsTo' => 'middle');
    $exportSpec['PIN Code'] = array('type' => 'integer');
    $exportSpec['GROUP ID'] = array('type' => 'integer');
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
    $exportComponents[] = array('method' => 'setUniqueId');
    $exportComponents[] = array('method' => 'setName');
    $exportComponents[] = array('method' => 'setAddress');
    $exportComponents[] = array('method' => 'setPhones');
    $exportComponents[] = array('method' => 'setEmails');

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

    return $q;
  }

  /**
   * Method to set the unique id field
   */
  protected function setUniqueId()
  {
    foreach ($this->exportRawData as $rowId => $rawData) {
      $this->exportData[$rowId]['UNIQUE ID'] = $rawData->a__id;
      $this->exportData[$rowId]['GROUP ID'] = $rawData->a9__id;
      $this->exportData[$rowId]['GROUP DESCRIPTION'] = substr($rawData->a9__organization, 0,
        $this->exportSpec['GROUP DESCRIPTION']['length']);
    }
  }

  /**
   * Method to set person names
   */
  protected function setName()
  {
    $pnh = $this->getHelperObject('agPersonNameHelper');

    $nameFields = array('LAST NAME', 'FIRST NAME', 'MIDDLE INITIAL');

    foreach ($this->exportRawData as $rowId => $rowData) {
      $names = $pnh->getPrimaryNameByType(array($rowData->a7__id));
      if (! isset($names[$rowData->a7__id])) {
        continue;
      }
      $names = $names[$rowData->a7__id];

      foreach ($nameFields as $nameField) {
        $spec = $this->exportSpec[$nameField];
        $type = $spec['mapsTo'];
        if (array_key_exists($type, $names)) {
          $this->exportData[$rowId][$nameField] = substr($names[$type], 0, $spec['length']);
        }
      }
    }
  }

  /**
   * Method to set address data
   */
  protected function setAddress()
  {
    $eah = $this->getHelperObject('agEntityAddressHelper');

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
    $eph = $this->getHelperObject('agEntityPhoneHelper');

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

  /**
   * Method to set phone done
   */
  protected function setEmails()
  {
    $eeh = $this->getHelperObject('agEntityEmailHelper');

    $emailFields = array('EMAIL LABEL', 'EMAIL');
    $suffixes = array(1, 2);

    foreach ($this->exportRawData as $rowId => $rowData) {

      $emails = $eeh->getEntityEmail(array($rowData->a8__id), TRUE, FALSE, agEmailHelper::EML_GET_VALUE);
       // skip this row if no email exists
      if (isset($emails[$rowData->a8__id])) {
       $emails = $emails[$rowData->a8__id];

        foreach ($suffixes as $suffix) {
          $sKey = $suffix - 1;
          if (array_key_exists($sKey, $emails)) {
            $email = array();
            $email['email'] = $emails[$sKey][1];
            $email['contact type'] = $emails[$sKey][0];

            foreach ($emailFields as $emailField) {
            $emailField = $emailField . ' ' . $suffix;
            $spec = $this->exportSpec[$emailField];
            $component = $spec['mapsTo'];
              if (array_key_exists($component, $email)) {
                $this->exportData[$rowId][$emailField] = substr($email[$component], 0, $spec['length']);
              }
            }
          }
        }
      }
    }
  }
}
