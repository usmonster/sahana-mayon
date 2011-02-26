<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of agImportNormalization
 *
 * @author shirley.chan
 */
class agImportNormalization {

  public $events = array();
  public $numRecordsNormalized;
  public $numRecordsFailed;

  function __construct($scenarioId, $sourceTable, $XLSColumnHeader) {
    $this->scenarioId = $scenarioId;
    $this->sourceTable = $sourceTable;
    $this->XLSColumnHeader = is_array($XLSColumnHeader) ? $XLSColumnHeader : array();
  }

  function __destruct() {
    
  }

  private function dataValidation() {
// Do data validations here.
  }

  public function normalizeImport() {
    try {
      $contactType = 'work';
      $phoneFormatTypes = array('USA 10 digit', 'USA 10 digit with an extension');
      $addressStandard = 'us standard';

      $facilityResourceTypeIds = agDoctrineQuery::create()
                      ->select('frt.facility_resource_type_abbr, frt.id')
                      ->from('agFacilityResourceType frt')
                      ->execute(array(), 'key_value_pair');
      $facilityResourceStatusIds = agDoctrineQuery::create()
                      ->select('frs.facility_resource_status, frs.id')
                      ->from('agFacilityResourceStatus frs')
                      ->execute(array(), 'key_value_pair');
      $facilityResourceAllocationStatusIds = agDoctrineQuery::create()
                      ->select('facility_resource_allocation_status, id')
                      ->from('agFacilityResourceAllocationStatus')
                      ->execute(array(), 'key_value_pair');
      $facilityGroupTypeIds = agDoctrineQuery::create()
                      ->select('facility_group_type, id')
                      ->from('agFacilityGroupType')
                      ->execute(array(), 'key_value_pair');
      $facilityGroupAllocationStatusIds = agDoctrineQuery::create()
                      ->select('facility_group_allocation_status, id')
                      ->from('agFacilitygroupAllocationStatus')
                      ->execute(array(), 'key_value_pair');
      $emailContactTypeIds = agDoctrineQuery::create()
                      ->select('email_contact_type, id')
                      ->from('agEmailContactType')
                      ->execute(array(), 'key_value_pair');
      $phoneContactTypeIds = agDoctrineQuery::create()
                      ->select('phone_contact_type, id')
                      ->from('agPhoneContactType')
                      ->execute(array(), 'key_value_pair');
      $phoneFormatTypeIds = agDoctrineQuery::create()
                      ->select('pft.phone_format_type, pf.id')
                      ->from('agPhoneFormatType pft')
                      ->innerJoin('pft.agPhoneFormat pf')
                      ->whereIn('phone_format_type', $phoneFormatTypes)
                      ->execute(array(), 'key_value_pair');
      $addressContactTypeIds = agDoctrineQuery::create()
                      ->select('address_contact_type, id')
                      ->from('agAddressContactType')
                      ->execute(array(), 'key_value_pair');
      $addressStandardObj = agDoctrineQuery::create()
                      ->from('agAddressStandard')
                      ->where('address_standard = ?', $addressStandard)
                      ->fetchOne();

      $query = 'SELECT * FROM ' . $this->sourceTable . ' AS i';

      $conn = Doctrine_Manager::connection();
      $pdo = $conn->execute($query);
      $pdo->setFetchMode(Doctrine_Core::FETCH_ASSOC);
      $sourceRecords = $pdo->fetchAll();

      $conn->beginTransaction();

      foreach ($sourceRecords as $record) {

        $facility_name = $record['facility_name'];
        $facility_code = $record['facility_code'];
        $facility_resource_type_abbr = $record['facility_resource_type_abbr'];
        $facility_resource_status = $record['facility_resource_status'];
        $capacity = $record['facility_capacity'];
        $facility_activation_sequence = $record['facility_activation_sequence'];
        $facility_allocation_status = $record['facility_allocation_status'];
        $facility_group = $record['facility_group'];
        $facility_group_type = $record['facility_group_type'];
        $facility_group_allocation_status = $record['facility_group_allocation_status'];
        $facility_group_activation_sequence = $record['facility_group_activation_status'];
        $email = $record['work_email'];
        $phone = $record['work_phone'];
        $fullAddress = array('line 1' => $record['street_1'],
            'line 2' => $record['street_2'],
            'city' => $record['city'],
            'state' => $record['state'],
            'zip5' => $record['postal_code'],
            'borough' => $record['borough'],
            'country' => $record['country']);

// TODO: implement this
        $this->dataValidation();
//        isValid = validate_row(facility_name, facility_code, facility_resource_type_abbr, facility_resource_status, capacity[, ...])
//        if (!isValid):
//          report warning
//          next;

        $facility_resource_type_id = $facilityResourceTypeIds[$facility_resource_type_abbr];
        $facility_resource_status_id = $facilityResourceStatusIds[$facility_resource_status];
        $facility_group_type_id = $facilityGroupTypeIds[$facility_group_type];
        $facility_group_allocation_status_id = $facilityGroupAllocationStatusIds[$facility_group_allocation_status];
        $facility_resource_allocation_status_id = $facilityResourceAllocationStatusIds[$facility_allocation_status];
        $workEmailTypeId = $emailContactTypeIds[$contactType];
        $workPhoneTypeId = $phoneContactTypeIds[$contactType];
        $workPhoneFormatId = $phoneFormatTypeIds[$phoneFormatTypes[(preg_match('/^\d{10}$/', $phone) ? 0 : 1)]];
        $workAddressTypeId = $addressContactTypeIds[$contactType];
        $workAddressStandardId = $addressStandardObj->getId();
        $addressElementIds = agDoctrineQuery::create()
                        ->select('ae.address_element, ae.id')
                        ->from('agAddressElement ae')
                        ->innerJoin('ae.agAddressFormat af')
                        ->where('af.address_standard_id = ?', $workAddressStandardId)
                        ->execute(array(), 'key_value_pair');

        // facility
        // tries to find an existing record based on a unique identifier.
        $facility = agDoctrineQuery::create()
                        ->from('agFacility f')
                        ->leftJoin('f.agFacilityResource fr')
                        ->leftJoin('fr.agFacilityResourceType frt')
                        ->where('f.facility_code = ?', $facility_code)
                        ->fetchOne();
        $facilityResource = NULL;
        $scenarioFacilityResource = NULL;

        if (empty($facility)) {
          $facility = $this->createFacility($facility_name, $facility_code);
        } else {
          $facility = $this->updateFacility($facility, $facility_name);
          $facilityResource = agDoctrineQuery::create()
                          ->from('agFacilityResource r')
                          ->where('r.facility_id = ?', $facility->getId())
                          ->andWhere('r.facility_resource_type_id = ?', $facility_resource_type_id)
                          ->fetchOne();
        }

        // facility resource
        if (empty($facilityResource)) {
          $facilityResource = $this->createFacilityResource($facility, $facility_resource_type_id, $facility_resource_status_id, $capacity);
        } else {
          $facilityResource = $this->updateFacilityResource($facilityResource, $facility_resource_status, $capacity);
          $scenarioFacilityResource = agDoctrineQuery::create()
                          ->from('agScenarioFacilityResource sfr')
                          //TODO
                          ->innerJoin('sfr.agScenarioFacilityGroup sfg')
                          ->where('sfg.scenario_id = ?', $this->scenarioId)
                          ->andWhere('facility_resource_id = ?', $facilityResource->id)
                          ->fetchOne();
        }

        // facility group
        $scenarioFacilityGroup = agDoctrineQuery::create()
                        ->from('agScenarioFacilityGroup')
                        ->where('scenario_id = ?', $this->scenarioId)
                        ->andWhere('scenario_facility_group = ?', $facility_group)
                        ->fetchOne();

        if (empty($scenarioFacilityGroup)) {
          $scenarioFacilityGroup = $this->createScenarioFacilityGroup($this->scenarioId, $facility_group, $facility_group_type_id, $facility_group_allocation_status_id, $facility_group_activation_sequence);
        } else {
          $scenarioFacilityGroup = $this->updateScenarioFacilityGroup($scenarioFacilityGroup, $facility_group_type_id, $facility_group_allocation_status_id, $facility_group_activation_sequence);
        }

        // facility resource
        if (empty($scenarioFacilityResource)) {
          $scenarioFacilityResource = $this->createScenarioFacilityResource($facilityResource, $scenarioFacilityGroup, $facility_resource_allocation_status_id, $facility_activation_sequence);
        } else {
          $scenarioFacilityResource = $this->updateScenarioFacilityResource($scenarioFacilityResource, $facility_resource_allocation_status_id, $facility_activation_sequence);
        }

        // email
        $this->updateFacilityEmail($facility, $email, $workEmailTypeId);

        // phone
        $this->updateFacilityPhone($facility, $phone, $workPhoneTypeId, $workPhoneFormatId);

        // address
        $this->updateFacilityAddress($facility, $fullAddress, $workAddressTypeId, $workAddressStandardId, $addressElementIds);

        $facility->save();
        $facilityResource->save();
        $scenarioFacilityGroup->save();
        $scenarioFacilityResource->save();
      } // end foreach

      $conn->commit();
    } catch (Exception $e) {
      echo '<BR><BR>Unable to normalize data.  Exception error mesage: ' . $e;
      $this->event[] = array('type' => 'ERROR', 'message' => 'Unable to normalize data.  Exception error mesage: ' . $e);
      $conn->rollBack();
    }
  }

  /* Facility */

  protected function createFacility($facilityName, $facilityCode) {
    $entity = new agEntity();
    $entity->save();
    $site = new agSite();
    $site->set('entity_id', $entity->id);
    $site->save();
    $facility = new agFacility();
    $facility->set('site_id', $site->id)
            ->set('facility_name', $facilityName)
            ->set('facility_code', $facilityCode);
    $facility->save();
    return $facility;
  }

  protected function updateFacility($facility, $facilityName) {
    //TODO
    return $facility;
  }

  /* Facility Resource */

  protected function createFacilityResource($facility, $facilityResourceTypeAbbrId, $facilityResourceStatusId, $capacity) {
    $facilityResource = new agFacilityResource();
    $facilityResource->set('facility_id', $facility->id)
            ->set('facility_resource_type_id', $facilityResourceTypeAbbrId)
            ->set('facility_resource_status_id', $facilityResourceStatusId)
            ->set('capacity', $capacity);
    $facilityResource->save();
    return $facilityResource;
  }

  protected function updateFacilityResource($facilityResource, $facilityResourceStatus, $capacity) {
    //TODO
    return $facilityResource;
  }

  /* Scenario Facility Group */

  protected function createScenarioFacilityGroup($scenarioId, $facilityGroup, $facilityGroupTypeId, $facilityGroupAllocationStatusId, $facilityGroupActivationSequence) {
    $scenarioFacilityGroup = new agScenarioFacilityGroup();
    $scenarioFacilityGroup->set('scenario_id', $scenarioId)
            ->set('scenario_facility_group', $facilityGroup)
            ->set('facility_group_type_id', $facilityGroupTypeId)
            ->set('facility_group_allocation_status_id', $facilityGroupAllocationStatusId)
            ->set('activation_sequence', $facilityGroupActivationSequence);
    $scenarioFacilityGroup->save();
    return $scenarioFacilityGroup;
  }

  protected function updateScenarioFacilityGroup($scenarioFacilityGroup, $facilityGroupTypeId, $facilityGroupAllocationStatusId, $facilityGroupActivationSequence) {
    //TODO
    return $scenarioFacilityGroup;
  }

  /* Scenario Facility Resource */

  protected function createScenarioFacilityResource($facilityResource, $scenarioFacilityGroup, $facilityResourceAllocationStatusId, $facilityActivationSequence) {
    $scenarioFacilityResource = new agScenarioFacilityResource();
    $scenarioFacilityResource->set('facility_resource_id', $facilityResource->id)
            ->set('scenario_facility_group_id', $scenarioFacilityGroup->id)
            ->set('facility_resource_allocation_status_id', $facilityResourceAllocationStatusId)
            ->set('activation_sequence', $facilityActivationSequence);
    $scenarioFacilityResource->save();
    return $scenarioFacilityResource;
  }

  protected function updateScenarioFacilityResource($scenarioFacilityResource, $facilityResourceAllocationStatusId, $facilityActivationSequence) {
    //TODO
    return $scenarioFacilityResource;
  }

  /* EMAIL */

  protected function getEmailObject($email) {
    $emailContact = agDoctrineQuery::create()
                    ->from('agEmailContact')
                    ->where('email_contact = ?', $email)
                    ->fetchOne();
    return $emailContact;
  }

  protected function createEmail($email) {
    $emailContact = new agEmailContact();
    $emailContact->set('email_contact', $email);
    $emailContact->save();
    return $emailContact;
  }

  protected function createEntityEmail($entityId, $emailId, $typeId) {
    $priority = $this->getPriorityCounter('email', $entityId);

    $entityEmail = new agEntityEmailContact();
    $entityEmail->set('entity_id', $entityId)
            ->set('email_contact_id', $emailId)
            ->set('email_contact_type_id', $typeId)
            ->set('priority', $priority);
    $entityEmail->save();

    return $entityEmail;
  }

  protected function updateEntityEmail($entityEmailObject, $emailObject) {
    //TODO
    // Replace existing entity email with new email.
    return $entityEmail;
  }

  /**
   *
   * @param <type> $facility
   * @param <type> $email
   * @param <type> $workEmailTypeId
   * @return bool true if an update or create happened, false otherwise.
   */
  protected function updateFacilityEmail($facility, $email, $workEmailTypeId) {
    $entityId = $facility->getAgSite()->entity_id;
    $facilityEmail = $this->getEntityContactObject('email', $entityId, $workEmailTypeId);

    if (empty($facilityEmail)) {
      $facilityEmail = '';
    } else {
      $facilityEmail = $facilityEmail->getAgEmailContact()->email_contact;
    }

    //  if oldEmail is importedEmail
    if ($email == $facilityEmail) {
      return FALSE;
    }

    // only useful for nonrequired emails
    //  if importedEmail null
    if (empty($email)) {
      //    clear f.email
      //TODO: write delete here
      return TRUE;
    }

    $emailEntity = $this->getEmailObject($email);

    if (empty($emailEntity)) {
      $emailEntity = $this->createEmail($email);
    }

    if (empty($facilityEmail)) {
      $this->createEntityEmail($entityId, $emailEntity->id, $workEmailTypeId);
      return TRUE;
    }

    $this->updateEntityEmail($facilityEmail, $emailEntity);
    return TRUE;
  }

  /* PHONE */

  protected function getPhoneObject($phone) {
    $phoneContact = NULL;
    $phoneContact = agDoctrineQuery::create()
                    ->from('agPhoneContact')
                    ->where('phone_contact = ?', $phone)
                    ->fetchOne();
    return $phoneContact;
  }

  protected function createPhone($phone, $phoneFormatId) {
    $phoneContact = new agPhoneContact();
    $phoneContact
            ->set('phone_contact', $phone)
            ->set('phone_format_id', $phoneFormatId);
    $phoneContact->save();
    return $phoneContact;
  }

  protected function createEntityPhone($entityId, $phoneContactId, $typeId) {
    $priority = $this->getPriorityCounter('phone', $entityId);

    $entityPhone = new agEntityPhoneContact();
    $entityPhone->set('entity_id', $entityId)
            ->set('phone_contact_id', $phoneContactId)
            ->set('phone_contact_type_id', $typeId)
            ->set('priority', $priority);
    $entityPhone->save();

    return $entityPhone;
  }

  protected function updateEntityPhone($entityPhoneObject, $phoneObject) {
    //TODO
    // Replace existing entity phone with new phone.
    return $entityPhone;
  }

  /**
   *
   * @param <type> $facility
   * @param <type> $phone
   * @param <type> $workEmailTypeId
   * @return bool true if an update or create happened, false otherwise.
   */
  protected function updateFacilityPhone($facility, $phone, $workPhoneTypeId, $phoneFormatId) {
    //TODO
    $entityId = $facility->getAgSite()->entity_id;
    $facilityPhone = $this->getEntityContactObject('phone', $entityId, $workPhoneTypeId);

    if (empty($facilityPhone)) {
      $facilityPhone = '';
    } else {
      $facilityPhone = $facilityPhone->getAgPhoneContact()->phone_contact;
    }

    //  if oldEmail is importedEmail
    if ($phone == $facilityPhone) {
      return FALSE;
    }

    // only useful for nonrequired phones
    //  if importedEmail null
    if (empty($phone)) {
      //    clear f.phone
      //TODO: write delete here
      return TRUE;
    }

    $phoneEntity = $this->getPhoneObject($phone);

    if (empty($phoneEntity)) {
      $phoneEntity = $this->createPhone($phone, $phoneFormatId);
    }

    if (empty($facilityPhone)) {
      $this->createEntityPhone($entityId, $phoneEntity->id, $workPhoneTypeId);
      return TRUE;
    }

    $this->updateEntityPhone($facilityPhone, $phoneEntity);
    return TRUE;
  }

  /* Address */

  protected function getAssociateAddressElementValues($addressId, array $addressElementIds) {
    $entityAddressElementValues = agDoctrineQuery::create()
                    ->select('ae.address_element, av.value')
                    ->from('agAddressElement ae')
                    ->innerJoin('ae.agAddressValue av')
                    ->innerJoin('av.agAddressMjAgAddressValue aav')
                    ->where('aav.address_id = ?', $addressId)
                    ->andWhereIn('ae.id', array($addressElementIds))
                    ->execute(array(), 'key_value_pair');
    return $entityAddressElementValues;
  }

  private function isEmptyStringArray($vals) {
    if (empty($vals)) {
      return FALSE;
    }

    foreach ($vals as $val) {
      if (strlen($val) != 0) {
        return FALSE;
      }
    }
    return TRUE;
  }

  protected function createAddressValues(array $fullAddress, $workAddressStandardId, array $addressElementIds) {
    $newAddressValueIds = array();
    foreach ($fullAddress as $elem => $elemVal) {
      if (empty($elemVal)) {
        continue;
      }
      $addressValue = agDoctrineQuery::create()
                      ->from('agAddressValue a')
                      ->innerJoin('a.agAddressElement ae')
                      ->where('a.value = ?', $elemVal)
                      ->andWhere('ae.address_element = ?', $elem)
                      ->fetchOne();
      if (empty($addressValue)) {
        $newAddressValue = new agAddressValue();
        $newAddressValue->set('value', $elemVal)
                ->set('address_element_id', $addressElementIds[$elem]);
        $newAddressValue->save();
        $newAddressValueIds[] = $newAddressValue->id;
      }
    }
    return $newElementIds;
  }

  protected function createEntityAddress($entityId, $addressTypeId, $fullAddress, $addressStandardId, $addressElementIds) {
    // create new address with importedElements
    $address = new agAddress();
    $address->set('address_standard_id', $addressStandardId);
    $address->save();

    $addressId = $address->id;

    $newAddressValueIds = $this->createAddressValues($fullAddress, $addressStandardId, $addressElementIds);

    foreach ($newAddressValueIds as $addressValueId) {
      $mappingAddress = new agAddressMjAgAddressValue();
      $mappingAddress->set('address_id', $addressId)
              ->set('address_value_id', $addressValueId);
      $mappingAddress->save();
    }

    $priority = $this->getPriorityCounter('address', $entityId);

    $entityAddress = new agEntityAddressContact();
    $entityAddress->set('entity_id', $entityId)
            ->set('address_id', $addressId)
            ->set('priority', $priority)
            ->set('address_contact_type', $addressTypeId);
    $entityAddress->save();
  }

  protected function updateFacilityAddress($facility, array $fullAddress, $workAddressTypeId, $workAddressStandardId, array $addressElementIds) {
    $entityId = $facility->getAgSite()->entity_id;
    $facilityAddress = $this->getEntityContactObject('address', $entityId, $workAddressTypeId);
    $isImportAddressEmpty = $this->isEmptyStringArray($fullAddress);

    // Remove existing facility work address if import address is null.
    if ((!empty($facilityAddress)) && $isImportAddressEmpty) {
      $this->deleteEntityAddressMapping($facilityAddress->id);
      return TRUE;
    }

    // Create new facility address with import address where facility does not have an address and
    // an address is given in import.
    $isCreateNew = FALSE;
    if (empty($facilityAddress) && !$isImportAddressEmpty) {
      $isCreateNew = TRUE;
    }

    $facilityAddress = $this->getEntityContactObject('address', 87, $workAddressTypeId);

    if (!empty($facilityAddress)) {
//      $intAddressElementIds = $this->convertArrayValueStringToInt($addressElementIds);
      $facilityAddressElements = $this->getAssociateAddressElementValues($facilityAddress->address_id, $addressElementIds);

      if (empty(array_diff(array_keys($facilityAddressElements), array_keys($fullAddress)))) {
        foreach ($fullAddress as $eltName => $eltVal) {
          if ($eltVal != $facilityAddressElements[$eltName]) {
            $isCreateNew = TRUE;
            break;
          }
        }
      } else {
        $isCreateNew = TRUE;
      }
    }

    if ($isCreateNew) {
      $this->createEntityAddress($entityId, $workAddressTypeId, $fullAddress, $workAddressStandardId, $addressElementIds);
    }

    return $isCreateNew;
  }

  protected function deleteEntityAddressMapping($entityAddressId) {
    $query = agDoctrineQuery::create()
                    ->delete('agEntityAddressContact')
                    ->where('id = ?', $entityAddressId)
                    ->execute();
  }

  /* ENTITY */

  protected function getEntityContactObject($contactMedium, $entityId, $contactTypeId) {
    $entityContactObject = NULL;

    switch ($contactMedium) {
      case 'phone':
        $table = 'agEntityPhoneContact';
        $contactType = 'phone_contact_type_id';
        break;
      case 'email':
        $table = 'agEntityEmailContact';
        $contactType = 'email_contact_type_id';
        break;
      case 'address':
        $table = 'agEntityAddressContact';
        $contactType = 'address_contact_type_id';
        break;
      default:
        return $entityContactObject;
    }

    $entityContactObject = agDoctrineQuery::create()
                    ->from($table)
                    ->where('entity_id = ?', $entityId)
                    ->andWhere($contactType . ' = ?', $contactTypeId)
                    ->orderBy('priority')
                    ->fetchOne();

    return $entityContactObject;
  }

  protected function getPriorityCounter($contactMedium, $entityId) {
    $priority = NULL;

    switch ($contactMedium) {
      case 'phone':
        $table = 'agEntityPhoneContact';
        break;
      case 'email':
        $table = 'agEntityEmailContact';
        break;
      case 'address':
        $table = 'agEntityAddressContact';
        break;
      default:
        return $priority;
    }

    $currentPriority = agDoctrineQuery::create()
                    ->select('max(priority)')
                    ->from($table)
                    ->where('entity_id = ?', $entityId)
                    ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    if (empty($currentPriority)) {
      $priority = 1;
    } else {
      $priority = $currentPriority[0] + 1;
    }

    return $priority;
  }

//  protected function convertArrayValueStringToInt($stringValueArray) {
//    $intValueArray = array();
//    foreach ($stringValueArray as $key=>$value) {
//      $intValueArray[$key] = (int)$value;
//    }
//    return $intValueArray;
//  }
}
