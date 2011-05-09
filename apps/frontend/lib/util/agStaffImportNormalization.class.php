<?php

/**
 * A Staff data import class. Standard (minimal) usage of this class would be to call
 * <code>
 * $this->importStaffFrom*();
 * $this->processBatch(); // in a loop
 * $this->concludeImport();
 * <code>
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUY SPS
 * @author Shirley Chan, CUY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agStaffImportNormalization extends agImportNormalization
{
  /**
   * This class's constructor.
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   */
  public function __construct($tempTable, $logEventLevel = NULL)
  {
    // DO NOT REMOVE
    parent::__construct($tempTable, $logEventLevel);

    // set the import components array as a class property
    $this->setImportComponents();
  }

  /**
   * Method to import staff from an excel file.
   */
  public function importStaffFromExcel()
  {
    // @todo call agImportHelper (parent) import excel method
    // @todo Build temp table here --^

    // start our iterator and initialize our select query
    $this->tempToRaw($this->buildTempSelectQuery());
  }

  /**
   * Method to set the dynamic field type. Does not need to be called here, will be called in parent
   */
  protected function setDynamicFieldType()
  {
    $this->dynamicFieldType = array('type' => "string", 'length' => 255);
  }

  /**
   * Method to extend the import specification to include dynamic columns from the file headers
   * @param array $importFileHeaders A single-dimension array of import file headers / column names
   */
  protected function addDynamicColumns($importFileHeaders)
  {
    $dynamicColumns = array_diff($importFileHeaders, array_keys($this->importSpec));
    foreach($dynamicColumns as $column)
    {
      $this->importSpec[$column] = $this->dynamicFieldType;
    }
  }

  /**
   * Method to set the classes' import specification.
   * Note: This intentionally excludes non-data fields (such as id, or success indicators); these
   * are set at a later point in the process.
   */
  protected function setImportSpec()
  {
    $importSpec['entity_id'] = array('type' => 'integer');
    $importSpec['first_name'] = array('type' => "string", 'length' => 64);
    $importSpec['middle_name'] = array('type' => "string", 'length' => 64);
    $importSpec['last_name'] = array('type' => "string", 'length' => 64);
    $importSpec['home_phone'] = array('type' => "string", 'length' => 16);
    $importSpec['home_email'] = array('type' => "string", 'length' => 255);
    $importSpec['work_phone'] = array('type' => "string", 'length' => 16);
    $importSpec['work_email'] = array('type' => "string", 'length' => 255);
    $importSpec['home_address_line_1'] = array('type' => "string", 'length' => 255);
    $importSpec['home_address_line_2'] = array('type' => "string", 'length' => 255);
    $importSpec['home_address_city'] = array('type' => "string", 'length' => 255);
    $importSpec['home_address_state'] = array('type' => "string", 'length' => 255);
    $importSpec['home_address_zip'] = array('type' => "string", 'length' => 5);
    $importSpec['home_address_country'] = array('type' => "string", 'length' => 128);
    $importSpec['home_latitude'] = array('type' => "decimal", 'length' => 12, 'scale' => 8);
    $importSpec['home_longitude'] = array('type' => "decimal", 'length' => 12, 'scale' => 8);
    $importSpec['work_address_line_1'] = array('type' => "string", 'length' => 255);
    $importSpec['work_address_line_2'] = array('type' => "string", 'length' => 255);
    $importSpec['work_address_city'] = array('type' => "string", 'length' => 255);
    $importSpec['work_address_state'] = array('type' => "string", 'length' => 255);
    $importSpec['work_address_zip'] = array('type' => "string", 'length' => 5);
    $importSpec['work_address_country'] = array('type' => "string", 'length' => 128);
    $importSpec['work_latitude'] = array('type' => "decimal", 'length' => 12, 'scale' => 8);
    $importSpec['work_longitude'] = array('type' => "decimal", 'length' => 12, 'scale' => 8);
    $importSpec['organization'] = array('type' => "string", 'length' => 128);
    $importSpec['resource_type'] = array('type' => "string", 'length' => 64);
    $importSpec['resource_status'] = array('type' => "string", 'length' => 30);
    $importSpec['language_1'] = array('type' => "string", 'length' => 128); //ag_person_mj_ag_language
    $importSpec['l1_speak'] = array('type' => "string", 'length' => 64); //ag_person_language_competency
    $importSpec['l1_read'] = array('type' => "string", 'length' => 64);
    $importSpec['l1_write'] = array('type' => "string", 'length' => 64);
    $importSpec['language_2'] = array('type' => "string", 'length' => 128); //ag_person_mj_ag_language
    $importSpec['l2_speak'] = array('type' => "string", 'length' => 64); //ag_person_language_competency
    $importSpec['l2_read'] = array('type' => "string", 'length' => 64);
    $importSpec['l2_write'] = array('type' => "string", 'length' => 64);

    // set the class property to the newly created 
    $this->importSpec = $importSpec;
  }

  /**
   * Method to clean a column name, removing leading and trailing spaces, special characters,
   * and replacing between-word spaces with an underscore. Will also throw if a zls is produced.
   * Note: This method is intentionally kept in the child class to allow customization if necesary
   *
   * @param string $columnName A string value representing a column name
   * @return string A properly formatted column name.
   */
  protected function cleanColumnName($columnName)
  {
    // keep this in case we need to throw an error
    $oldColumnName = $columnName;

    // trim once, for good measure, then replace spaces with underscores
    $columnName = trim(strtolower($columnName));
    $columnName = str_replace(' ', '_', trim(strtolower($columnName)));

    // many db's complain about numbers prepending column names
    $columnName = preg_replace('/^\d+/', '', $columnName);

    // filter out all special characters
    $columnName = preg_replace('/[\W]/', '', $columnName);

    // reduce any duplicate underscore pairs we may have created
    $columnName = preg_replace('/__+/', '_', $columnName);

    // lastly, in case this method created an unusable empty string, we throw (eg, fatal)
    if (strlen($columnName) == 0)
    {
      $errMsg = "Column name {$oldColumnName} could not be parsed.";
      $this->logErr($errMsg, 1);
      throw new Exception($errMsg);
    }
    return $columnName;
  }

  /**
   * Method to dynamically build a (mostly) static tempSelectQuery
   * @return <type>
   */
  protected function buildTempSelectQuery()
  {
    $query = sprintf(
      'SELECT t.*
         FROM %s AS t',
      $this->tempTable);

    return $query;
  }
  
  /**
   * @todo This data should belong in a configuration file (eg, YML)
   */
  protected function setImportComponents()
  {
    // array( [order] => array(component => component name, helperClass => Name of the helper class, throwOnError => boolean, method => method name) )
    // setEntity creates entity, person, and staff records.
    $this->importComponents[] = array( 'component' => 'entity', 'throwOnError' => TRUE, 'method' => 'setEntities');
    $this->importComponents[] = array( 'component' => 'personName', 'throwOnError' => TRUE, 'method' => 'setPersonNames', 'helperClass' => 'agPersonNameHelper');
    $this->importComponents[] = array( 'component' => 'phone', 'throwOnError' => FALSE, 'method' => 'setEntityPhone', 'helperClass' => 'agEntityPhoneHelper');
    $this->importComponents[] = array( 'component' => 'email', 'throwOnError' => FALSE, 'method' => 'setEntityEmail', 'helperClass' => 'agEntityEmailHelper');
#    $this->importComponents[] = array( 'component' => 'address', 'throwOnError' => FALSE, 'method' => 'setEntityAddress', 'helperClass' => 'agEntityAddressHelper');
    $this->importComponents[] = array( 'component' => 'customField', 'throwOnError' => FALSE, 'method' => 'setPersonCustomField');
    $this->importComponents[] = array( 'component' => 'staffResource', 'throwOnError' => TRUE, 'method' => 'setStaffResourceOrg');
    $this->importComponents[] = array( 'component' => 'personLanguage', 'throwOnError' => FALSE, 'method' => 'setPersonLanguage');
  }



  /**
   * Method to set / create new entities, persons, and staff.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setEntities($throwOnError, Doctrine_Connection $conn)
  {
    // we need to capture errors just to make sure we don't store failed ID inserts
    try
    {
      $this->loadCurrentEntities();
      $this->setNewEntities();
    }
    catch(Exception $e)
    {
      foreach ($this->importData as $rowId => &$rowData)
      {
        unset($rowData['primaryKeys']['entity_id']);
        unset($rowData['primaryKeys']['person_id']);
        unset($rowData['primaryKeys']['staff_id']);
      }

      // continue throwing our exception
      throw $e;
    }
  }

  /*
   * Method to load any existing entities from the database into our object.
   */
  protected function loadCurrentEntities()
  {
    // explicit declarations are good
    $rawEntityIds = array();

    // loop our import data and pick up any existing entity Ids
    foreach ($this->importData as $rowId => $rowData)
    {
      $rawData = $rowData['_rawData'];

      if(array_key_exists('entity_id', $rawData))
      {
        $rawEntityIds[] = $rawData['entity_id'];
      }
    }

    // find current entity + persons
    $q = agDoctrineQuery::create()
    ->select('e.id')
        ->addSelect('p.id')
        ->addSelect('s.id')
      ->from('agEntity e')
        ->innerJoin('e.agPerson p')
        ->leftJoin('p.agStaff s')
      ->whereIn('e.id', $rawEntityIds);
    $entities = $q->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_ARRAY);

    // we no longer need this array (used for the ->whereIN)
    unset($rawEntityIds);

    //loop foreach $entities member
    foreach ($entities as $entityId => &$entityData)
    {
      // if staff id doesn't exist yet, make it so
      if (is_null($entityData[1]))
      {
        $entityData[1] = $this->createNewRec('agStaff', array('person_id' => $entityData[0]));
      }
    }
    
    // update our row keys array
    foreach ($this->importData as $rowId => &$rowData)
    {
      if (array_key_exists('entity_id', $rowData['_rawData']))
      {
        $entityId = $rowData['_rawData']['entity_id'];
        if (array_key_exists($entityId, $entities))
        {
          $rowData['primaryKeys']['entity_id'] = $entityId;
          $rowData['primaryKeys']['person_id'] = $entities[$entityId][0];
          $rowData['primaryKeys']['staff_id'] = $entities[$entityId][1];
          unset($entities[$entityId]);
        }
      }
    }
  }

  /*
   * Method to set new entities from our $_rawData.
   */
  protected function setNewEntities()
  {
    // add new entities / persons / staff for records with bad or no entity ids.
    foreach ($this->importData as $rowId => &$rowData)
    {
      // initially be pessimistic about the actions we'll need to take
      $createNew = FALSE;
      $warnBadEntity = FALSE;

      // pick these up by reference so we can use the pointers more easily
      $rawData =& $rowData['_rawData'];
      $pKeys =& $rowData['primaryKeys'];

      // this should satisfy both NULL entity_ids and ones that didn't make our initial filter
      if (! array_key_exists('entity_id',$rawData))
      {
        $createNew = TRUE;
      }
      elseif ($rawData['entity_id'] != $pKeys['entity_id'])
      {
        $createNew = TRUE;
        $warnBadEntity = TRUE;
      }

      if ($createNew)
      {
        $fKeys = array();
        $pKeys['entity_id'] = $this->createNewRec('agEntity', $fKeys);

        $fKeys = array('entity_id' => $pKeys['entity_id']);
        $pKeys['person_id'] = $this->createNewRec('agPerson', $fKeys);

        $fKeys = array('person_id' => $pKeys['person_id']);
        $pKeys['staff_id'] = $this->createNewRec('agStaff', $fKeys);
      }

      // here we log warnings about bad entities that we've been passed and chose to override
      if ($warnBadEntity)
      {
        $warnMsg = sprintf("Bad entity id (%s).  Generated a new entity id (%s).",
          $rawData['entity_id'], $pKeys['entity_id']);
        sfContext::getInstance()->getLogger()->warning($warnMsg);
      }
    }
  }

  /**
   * Method to set person names during staff import.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setPersonNames($throwOnError, Doctrine_Connection $conn)
  {
    // always start with any data maps we'll need so they're explicit
    $importNameTypes = array('first_name'=>'given', 'middle_name'=>'middle', 'last_name'=>'family');
    $personNames = array();
    $results = array();

    // let's get ahold of our helper object since we're going to use him/her a lot
    $pnh =& $this->helperObjects['agPersonNameHelper'];

    // get our name types and map them back to the importNameTypes
    $nameTypes = $pnh->getNameTypeIds(array_values($importNameTypes));
    foreach ($importNameTypes as $key => &$val)
    {
      $val = $nameTypes[$val];
    }
    unset($val);
    unset($nameTypes);

    // loop through our raw data and build our person name data
    foreach ($this->importData as $rowId => $rowData)
    {
      if (array_key_exists('person_id', $rowData['primaryKeys']))
      {
        $personId = $rowData['primaryKeys']['person_id'];

        // we start with an empty array, devoid of types in case the entity has no types/values
        $personNames[$personId] = array();
        foreach($importNameTypes as $nameType => $nameTypeId)
        {
          if (array_key_exists($nameType, $rowData['_rawData']))
          {
            $personNames[$personId][$nameTypeId][] = $rowData['_rawData'][$nameType];
          }
        }
      }
    }

    // pick up some of our components / objects
    $keepHistory = agGlobal::getParam('staff_import_keep_history');

    // execute the helper and finish
    $results = $pnh->setPersonNames($personNames, $keepHistory, $throwOnError, $conn);
    unset($personNames);

    // @todo do your results reporting here
  }

  /**
   * Method to set entity emails during staff import.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setEntityEmail($throwOnError, Doctrine_Connection $conn)
  {
    // always start with any data maps we'll need so they're explicit
    $importEmailTypes = array('work_email'=>'work', 'home_email'=>'personal');
    $entityEmails = array();
    $results = array();

    // let's get ahold of our helper object since we're going to use him/her a lot
    $eeh =& $this->helperObjects['agEntityEmailHelper'];

    // get our email types and map them back to the importEmailTypes
    $emailTypes = agEmailHelper::getEmailContactTypeIds(array_values($importEmailTypes));
    foreach ($importEmailTypes as $key => &$val)
    {
      $val = $emailTypes[$val];
    }
    unset($val);
    unset($emailTypes);

    // loop through our raw data and build our entity email data
    foreach ($this->importData as $rowId => $rowData)
    {
      if (array_key_exists('entity_id', $rowData['primaryKeys']))
      {
        // this just makes it easier to use
        $entityId = $rowData['primaryKeys']['entity_id'];

        // we start with an empty array, devoid of types in case the entity has no types/values
        $entityEmails[$entityId] = array();
        foreach($importEmailTypes as $emailType => $emailTypeId)
        {
          if (array_key_exists($emailType, $rowData['_rawData']))
          {
            $entityEmails[$entityId][] = array($emailTypeId, $rowData['_rawData'][$emailType]);
          }
        }
      }
    }

    // pick up some of our components / objects
    $keepHistory = agGlobal::getParam('staff_import_keep_history');
    $enforceStrict = agGlobal::getParam('enforce_strict_contact_formatting');

    // execute the helper and finish
    $results = $eeh->setEntityEmail($entityEmails, $keepHistory, $enforceStrict, $throwOnError, $conn);
    unset($entityEmails);

    // @todo do your results reporting here
  }

  /**
   * Method to set entity phones during staff import.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setEntityPhone($throwOnError, Doctrine_Connection $conn)
  {
    // always start with any data maps we'll need so they're explicit
    $importPhoneTypes = array('work_phone'=>'work', 'home_phone'=>'home', 'mobile_phone' => 'mobile');
    $entityPhones = array();
    $results = array();

    // let's get ahold of our helper object since we're going to use him/her a lot
    $eph =& $this->helperObjects['agEntityPhoneHelper'];

    // get our email types and map them back to the importEmailTypes
    $phoneTypes = agPhoneHelper::getPhoneContactTypeIds(array_values($importPhoneTypes));
    foreach ($importPhoneTypes as $key => &$val)
    {
      $val = $phoneTypes[$val];
    }
    unset($val);
    unset($phoneTypes);

    // loop through our raw data and build our entity phone data
    foreach ($this->importData as $rowId => $rowData)
    {
      if (array_key_exists('entity_id', $rowData['primaryKeys']))
      {
        // this just makes it easier to use
        $entityId = $rowData['primaryKeys']['entity_id'];

        // we start with an empty array, devoid of types in case the entity has no types/values
        $entityPhones[$entityId] = array();
        foreach($importPhoneTypes as $phoneType => $phoneTypeId)
        {
          if (array_key_exists($phoneType, $rowData['_rawData']))
          {
            $entityPhones[$entityId][] = array($phoneTypeId, array($rowData['_rawData'][$phoneType]));
          }
        }
      }
    }

    // pick up some of our components / objects
    $keepHistory = agGlobal::getParam('staff_import_keep_history');
    $enforceStrict = agGlobal::getParam('enforce_strict_contact_formatting');

    // execute the helper and finish
    $results = $eph->setEntityPhone($entityPhones, $keepHistory, $enforceStrict, $throwOnError, $conn);
    unset($entityPhones);

    // @todo do your results reporting here
  }

  /**
   * Method to set entity address during staff import.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setEntityAddress($throwOnError, Doctrine_Connection $conn)
  {
    // always start with any data maps we'll need so they're explicit
    $importAddressTypes = array('work_address'=>'work', 'home_address'=>'home');
    $importAddressElements = array('line_1' => 'line 1', 'line_2' => 'line 2', 'city' => 'city',
                             'state' => 'state', 'zip' => 'zip5', 'country' => 'country');
    $entityAddresses = array();
    $results = array();

    // let's get ahold of our helper object since we're going to use him/her a lot
    $eah =& $this->helperObjects['agEntityAddressHelper'];

    // get our address types and map them back to the importAddressTypes
    $addressTypes = agAddressHelper::getAddressContactTypeIds(array_values($importAddressTypes));
    foreach ($importAddressTypes as $key => &$val)
    {
      $val = $addressTypes[$val];
    }
    unset($val);
    unset($addressTypes);

    // get our address elements and map them back to the importAddressElements
    $addressElements = agAddressHelper::getAddressElementIds(array_values($importAddressElements));
    foreach ($importAddressElements as $key => &$val)
    {
      $val = $addressElements[$val];
    }
    unset($val);
    unset($addressElements);

    // get our address standards
    $addressStandard = agGlobal::getParam('staff_import_address_standard');
    $addressStandardId = agAddressHelper::getAddressStandardIds($addressStandard);
    $addressStandardId = $addressStandardId[$addressStandard];

    // loop through our raw data and build our entity address data
    foreach ($this->importData as $rowId => $rowData)
    {
      if (array_key_exists('entity_id', $rowData['primaryKeys']))
      {
        // this just makes it easier to use
        $entityId = $rowData['primaryKeys']['entity_id'];

        // we start with an empty array, devoid of types in case the entity has no types/values
        $entityAddresses[$entityId] = array();
        list($homeAddr, $workAddr) = array(array(), array());
        foreach ($rowData['_rawData'] AS $element => $value)
        {
          if (preg_match('/^home_address/', $element))
          {
            $formElem = str_replace('home_address_', '', $element);
            $elemId = $importAddressElements[$formElem];
            $homeAddr[$importAddressElements[str_replace('home_address_', '', $element)]] = $value;
          }
          else if (preg_match('/^work_address/', $element))
          {
            $workAddr[$importAddressElements[str_replace('work_address_', '', $element)]] = $value;
          }
        }

        if (count($homeAddr) > 0)
        {
          $entityAddresses[$entityId][] = array($importAddressTypes['home_address'], array($homeAddr, $addressStandardId));
        }
        if (count($workAddr) > 0)
        {
          $entityAddresses[$entityId][] = array($importAddressTypes['work_address'], array($workAddr, $addressStandardId));
        }
      }
    }

    // pick up some of our components / objects
    $keepHistory = agGlobal::getParam('staff_import_keep_history');
    $enforceStrict = agGlobal::getParam('enforce_strict_contact_formatting');

    $addressGeo = array();
    // @TODO Handle geo upserts along with address.

    // execute the helper and finish
    $results = $eah->setEntityAddress($entityAddresses, $addressGeo, $keepHistory,  $enforceStrict, $throwOnError, $conn);
    unset($entityAddresses);

    // @todo do your results reporting here
  }

  /**
   * Method to set custom field data for persons.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setPersonCustomField ($throwOnError, Doctrine_Connection $conn)
  {
    $errMsg = NULL;
    $importCustomFields = array('drivers_license_class' => 'Drivers License Class',
                                'pms_id' => 'PMS ID',
                                'civil_service_title' => 'Civil Service Title');

    // @TODO Generate person custom field edges and s/getter.

    $customFieldIds = agDoctrineQuery::create()
      ->select('pcf.person_custom_field')
          ->addSelect('pcf.id')
        ->from('agPersonCustomField pcf')
        ->whereIn('pcf.person_custom_field', array_values($importCustomFields))
        ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);


    // first loop through and grab all our personIds into a single array
    // we intentionally let this fail if there is no person id since we should never have that case
    $personIds = array();
    $updatedPersonCustomField = array();
    foreach ($this->importData as $rowId => $rowData)
    {
      $personIds[$rowData['primaryKeys']['person_id']] = $rowId;
      $updatedPersonCustomField[$rowData['primaryKeys']['person_id']] = array();
    }

    // get all of our custom records in a collection
    $coll = agDoctrineQuery::create()
           ->select('pcv.*')
             ->from('agPersonCustomFieldValue pcv')
             ->whereIn('pcv.person_id', array_keys($personIds))
           ->execute();

    // Perform custom updates on person with existing custom fields.
    foreach($coll AS $index => &$record)
    {
      $pId = $record['person_id'];
      $rawData = $this->importData[$personIds[$pId]]['_rawData'];
      $recordCustomFieldId = $record['person_custom_field_id'];
      $customField = array_search(array_search($recordCustomFieldId, $customFieldIds),
                                  $importCustomFields);

      if (array_key_exists($customField, $rawData))
      {
        $record['value'] = $rawData[$customField];
        $updatedPersonCustomField[$pId][] = $customField;
      }
      else
      {
        // Delete record if record exists in db, but none is provided in import.
        $coll->remove($index);
        $updatedPersonCustomField[$pId][] = $customField;
      }
    }

    $coll->save($conn);
    unset($coll);

    // Now process person with new custom field if provided in import.
    foreach($personIds AS $pId => $rowId)
    {
      if (count($updatedPersonCustomField[$pId]) < count($importCustomFields))
      {
        $newCustomFields = array_diff(array_keys($importCustomFields), $updatedPersonCustomField[$pId]);
        $rawData = $this->importData[$personIds[$pId]]['_rawData'];
        foreach ($newCustomFields AS $customField)
        {
          $mappedCustomField = $importCustomFields[$customField];
          if (array_key_exists($customField, $rawData))
          {
            $fKeys = array();
            $fKeys['person_id'] = $pId;
            $fKeys['person_custom_field_id'] = $customFieldIds[$importCustomFields[$customField]];
            $fKeys['value'] = $rawData[$customField];

            $personCustomField = $this->createNewRec('agPersonCustomFieldValue', $fKeys);
          }
        }

      }
    }
    unset($personIds);
    unset($updatedPersonCustomField);
    
  }

  /**
   * Method to set staff's resource and organization relations during staff import.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   * control whether or not errors will be thrown.
   */
  protected function setStaffResourceOrg($throwOnError, Doctrine_Connection $conn)
  {
    $errMsg = NULL;

    // Required columns.
    $requiredColumns = array('organization', 'resource_type', 'resource_status');

    $organizationIds = agDoctrineQuery::create()
        ->select('o.organization')
            ->addSelect('o.id')
          ->from('agOrganization o')
          ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    $stfRscTypeIds = agDoctrineQuery::create()
        ->select('srt.staff_resource_type')
            ->addSelect('srt.id')
          ->from('agStaffResourceType srt')
          ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    $stfRscStatusIds = agDoctrineQuery::create()
        ->select('srs.staff_resource_status')
            ->addSelect('srs.id')
          ->from('agStaffResourceStatus srs')
          ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    // Retrieve staff ids from raw data.
    $staffIds = array();
    foreach ($this->importData AS $rowId => $rowData)
    {
      foreach($requiredColumns AS $column)
      {
        if(!array_key_exists($column, $rowData['_rawData']))
        {
          $errMsg = sprintf('Missing required column %s from record id %d.', $column, $rowId);
          break;
        }
      }

      if (is_null($errMsg))
      {
        $staffIds[$rowData['primaryKeys']['staff_id']] = $rowId;
      }
      else
      {
        // Capture error in error log.
        $this->logErr($errMsg);

        if($throwOnError)
        {
          throw new Exception($errMsg);
        }
      }
    }

    $coll = agDoctrineQuery::create()
           ->select('sr.*')
             ->from('agStaffResource sr')
             ->whereIn('sr.staff_id', array_keys($staffIds))
           ->execute();

    // Perform organization updates on existing staff resource.
    foreach($coll AS $index => &$record)
    {
      $stfId = $record['staff_id'];
      $rawData = $this->importData[$staffIds[$stfId]]['_rawData'];
      $rscTypeId = $stfRscTypeIds[$rawData['resource_type']];

      if ( $record['staff_resource_type_id'] == $rscTypeId )
      {
        $orgId = $organizationIds[$rawData['organization']];
        $record['organization_id'] = $orgId;
        $stfRscStatusId = $stfRscStatusIds[$rawData['resource_status']];
        $record['staff_resource_status_id'] = $stfRscStatusId;
        unset($staffIds[$stfId]);
      }
    }

    $coll->save($conn);
    unset($coll);

    // Now $staffIds are left with new staff resources.
    foreach($staffIds AS $stfId => $rowId)
    {
      $rawData = $this->importData[$staffIds[$stfId]]['_rawData'];
      $fKeys = array();
      $fKeys['staff_id'] = $stfId;
      $fKeys['organization_id'] = $organizationIds[$rawData['organization']];
      $fKeys['staff_resource_type_id'] = $stfRscTypeIds[$rawData['resource_type']];
      $fKeys['staff_resource_status_id'] = $stfRscStatusIds[$rawData['resource_status']];

      $staffResource = $this->createNewRec('agStaffResource', $fKeys);
    }

  }

  /**
   * Method to set person language during staff import.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setPersonLanguage($throwOnError, Doctrine_Connection $conn)
  {
    // always start with any data maps we'll need so they're explicit
    $importLanguageComponents = array( 'language_1' => array( 'l1_speak' => 'speak', 
                                                              'l1_read' => 'read', 
                                                              'l1_write' => 'write'),
                                       'language_2' => array( 'l2_speak' => 'speak', 
                                                              'l2_read' => 'read', 
                                                              'l2_write' => 'write'),
                                       'language_3' => array( 'l3_speak' => 'speak', 
                                                              'l3_read' => 'read', 
                                                              'l3_write' => 'write')
                                     );

    $personLanguages = array();
    $results = array();

    // loop through our raw data and build our person language data
    foreach ($this->importData as $rowId => $rowData)
    {
      if (array_key_exists('person_id', $rowData['primaryKeys']))
      {
        // this just makes it easier to use
        $personId = $rowData['primaryKeys']['person_id'];

        // Always initialize $languageComponents as an empty array.  Assign an empty array to
        // $personLanguages only if no language was specified from import.
        $languageComponents = array();
        foreach($importLanguageComponents as $importLanguage => $langComponents)
        {
          if (array_key_exists($importLanguage, $rowData['_rawData']))
          {
            // Always initialize $formatCompetencies as an emtpy array. Assign an emtpy array to
            // $languageComponents only if no format/competency details is specified for the
            // language from import.
            $formatCompetencies = array();
            foreach ($langComponents as $importFormat => $dbFormat)
            {
              if (array_key_exists($importFormat, $rowData['_rawData']))
              {
                $formatCompetencies[$dbFormat] = $rowData['_rawData'][$importFormat];
              }
            }
            $languageComponents[] = array($rowData['_rawData'][$importLanguage], $formatCompetencies);
          }
        }
        $personLanguages[$personId] = $languageComponents;
      }
    }

    // pick up some of our components / objects
    $keepHistory = agGlobal::getParam('staff_import_keep_history');
    $createEdgeTableValues = agGlobal::getParam('create_edge_table_values');;

    // execute the helper and finish
    $results = $plh->setPersonLanguage($personLanguages, $keepHistory, $createEdgeTableValues, $throwOnError, $conn);
    unset($personLanguages);

    // @todo do your results reporting here
  }

  public function testDataNorm()
  {
    $this->setLogEventLevel(self::EVENT_INFO);
    $_rawData1 = array('entity_id' => '3',
                      'first_name' => 'Mork',
                      'last_name' => 'Ork',
                      'mobile_phone' => '123.123.1234',
                      'home_email' => 'mork.ork@home.com',
                      'home_address_line_1' => '5 Silly Lane Street',
                      'home_address_city' => 'Inwood',
                      'home_address_state' => 'Bronze',
                      'home_address_country' => 'United States of America',
                      'work_address_line_1' => '5 Silly Man',
                      'work_address_line_2' => 'In rooom 728',
                      'work_address_city' => 'New York',
                      'work_address_state' => 'NY',
                      'work_address_zip' => '10013',
                      'work_address_country' => 'United States of America',
                      'organization' => 'GOAL',
                      'resource_type' => 'Medical Nurse',
                      'resource_status' => 'active',
                      'language_1' => 'English',
                      'l1_speak' => 'fluent',
                      'l1_read' => 'fluent',
                      'l1_write' => 'fluent',
                      'language_2' => 'Spanish',
                      'l2_speak' => 'intermediate',
                      'drivers_license_class' => 'Class A',
                      'civil_service_title' => 'Court Clerk'
                     );

    $_rawData2 = array('entity_id' => '183',
                      'first_name' => 'Ork',
                      'middle_name' => 'Mork',
                      'home_phone' => '(222) 222-1234',
                      'work_phone' => '(212) 234-2344 x234',
                      'work_address_line_1' => '235 President Pl',
                      'work_address_city' => 'New York',
                      'work_address_state' => 'NY',
                      'work_address_zip' => '11001',
                      'work_address_country' => 'United States of America',
                      'organization' => 'volunteer',
                      'resource_type' => 'Specialist',
                      'resource_status' => 'inactive',
                      'language_2' => 'Latin',
                      'l1_read' => 'superior',
                      'civil_service_title' => 'Judge'
                     );

    $_rawData3 = array('entity_id' => '11',
                      'work_phone' => '333.3333.3333.',
                      'new custom field' => 'smiley face'
                     );

    $_rawData4 = array('entity_id' => '4983',
                      'first_name' => 'Ae432',
                      'middle_name' => 'LinjaAA  ',
                      'last_name' => '   asdkfjkl;   ',
                      'mobile_phone' => '999.9999.9999',
                      'work_phone' => '222.3333.4444 x342',
                      'work_email' => 'Linjawork.com',
                      'organization' => 'CHAD',
                      'resource_type' => 'Medical Assistant',
                      'resource_status' => 'Bouncing',
                      'pms_id' => 'P23423452'
                     );

    $_rawData5 = array('entity_id' => '392',
                      'first_name' => 'Aimee',
                      'last_name' => 'Adu',
                      'work_email' => 'aimeeemailnow@work.com'
                     );

    $this->importData[1] = array( '_rawData' => $_rawData1, 'primaryKey' => array(), 'success' => 0);
    $this->importData[2] = array( '_rawData' => $_rawData2, 'primaryKey' => array(), 'success' => 0);
    $this->importData[3] = array( '_rawData' => $_rawData3, 'primaryKey' => array(), 'success' => 0);
//    $this->importData[4] = array( '_rawData' => $_rawData4, 'primaryKey' => array(), 'success' => 0);
//    $this->importData[5] = array( '_rawData' => $_rawData5, 'primaryKey' => array(), 'success' => 0);

    $this->clearNullRawData();
    $this->normalizeData();
    print_r($this->getEvents());
  }
}