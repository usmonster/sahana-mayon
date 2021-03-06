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
   * Method to return an instance of this class
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   * @return agStaffImportNormalization An instance of this class
   */
  public static function getInstance($tempTable, $logEventLevel = NULL)
  {
    $self = new self();
    $self->__init($tempTable, $logEventLevel);
    return $self;
  }

  /**
   * Method to initialize this class
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   */
  public function __init($tempTable = NULL, $logEventLevel = NULL)
  {
    if (is_null($tempTable)) {
      $tempTable = 'temp_staff_import';
    }

    // DO NOT REMOVE
    parent::__init($tempTable, $logEventLevel);

    // set the import components array as a class property
    $this->setImportComponents();
    $this->tempTableOptions = array('type' => 'MYISAM', 'charset' => 'utf8');
    $this->importHeaderStrictValidation = TRUE;

    $this->eh->setErrThreshold(intval(agGlobal::getParam('import_error_threshold')));
  }

  /**
   * Imports staff from an excel file.
   */
  public function processXlsImportFile($importFile)
  {
    // process the excel file and create a temporary table
    parent::processXlsImportFile($importFile);

    // start our iterator and initialize our select query
    $this->tempToRaw($this->buildTempSelectQuery());
  }

  /**
   * Method to set the unprocessed records basename
   */
  protected function setUnprocessedBaseName()
  {
    $this->unprocessedBaseName = agGlobal::getParam('unprocessed_staff_import_basename');
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
  protected function addDynamicColumns(array $importFileHeaders)
  {
    $dynamicColumns = array_diff($importFileHeaders, array_keys($this->importSpec));
    foreach ($dynamicColumns as $column) {
      $this->importSpec[$column] = $this->dynamicFieldType;
      $this->specStrLengths[$column] = self::getSpecificationStrLen($this->dynamicFieldType);
      $this->eh->logInfo('Adding dynamic column {' . $column . '} to the import specification.');
    }
  }

  /**
   * Method to set the classes' import specification.
   * Note: This intentionally excludes non-data fields (such as id, or success indicators); these
   * are set at a later point in the process.
   */
  protected function setImportSpec()
  {
    $importSpec['entity_id'] = array('type' => 'integer', 'length' => 6);
    $importSpec['first_name'] = array('type' => "string", 'length' => 64);
    $importSpec['middle_name'] = array('type' => "string", 'length' => 64);
    $importSpec['last_name'] = array('type' => "string", 'length' => 64);
    $importSpec['mobile_phone'] = array('type' => "string", 'length' => 16);
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
    if (strlen($columnName) == 0) {
      $errMsg = "Column name {$oldColumnName} could not be parsed.";
      $this->eh->logCrit($errMsg, 1);
      throw new Exception($errMsg);
    }
    return $columnName;
  }

  /**
   * This method is an extension of the parent validate column headers method allowing
   * domain-specific header validation.
   * @param array $importFileHeaders An array of import file headers.
   * @param string $sheetName The name of the sheet being validated.
   * @return boolean A boolean indicating un/successful validation of column headers.
   */
  protected function validateColumnHeaders(array $importFileHeaders, $sheetName)
  {
    // DO NOT REMOVE THIS LINE UNLESS YOU KNOW WHAT YOU ARE DOING
    $validated = parent::validateColumnHeaders($importFileHeaders, $sheetName);

    return $validated;
  }

  /**
   * Method to dynamically build a (mostly) static tempSelectQuery
   * @return string Returns a string query
   */
  protected function buildTempSelectQuery()
  {
    $query = sprintf(
        'SELECT t.*
         FROM %s AS t', $this->tempTable);

    return $query;
  }

  /**
   * @todo This data should belong in a configuration file (eg, YML)
   */
  protected function setImportComponents()
  {
    // array( [order] => array(component => component name, helperClass => Name of the helper class, throwOnError => boolean, method => method name) )
    // setEntity creates entity, person, and staff records.
    $this->importComponents[] = array(
      'component' => 'createEntityHashes',
      'throwOnError' => TRUE,
      'method' => 'createEntityHashes'
    );
    $this->importComponents[] = array(
      'component' => 'entity',
      'throwOnError' => TRUE,
      'method' => 'setEntities'
    );
    $this->importComponents[] = array(
      'component' => 'personName',
      'throwOnError' => TRUE,
      'method' => 'setPersonNames',
      'helperClass' => 'agPersonNameHelper'
    );
    $this->importComponents[] = array(
      'component' => 'phone',
      'throwOnError' => FALSE,
      'method' => 'setEntityPhone',
      'helperClass' => 'agEntityPhoneHelper'
    );
    $this->importComponents[] = array(
      'component' => 'email',
      'throwOnError' => FALSE,
      'method' => 'setEntityEmail',
      'helperClass' => 'agEntityEmailHelper'
    );
    $this->importComponents[] = array(
      'component' => 'address',
      'throwOnError' => FALSE,
      'method' => 'setEntityAddress',
      'helperClass' => 'agEntityAddressHelper'
    );
    $this->importComponents[] = array(
      'component' => 'customField',
      'throwOnError' => FALSE,
      'method' => 'setPersonCustomField'
    );
    $this->importComponents[] = array(
      'component' => 'staffResource',
      'throwOnError' => TRUE,
      'method' => 'setStaffResourceOrg'
    );
    $this->importComponents[] = array(
      'component' => 'personLanguage',
      'throwOnError' => FALSE,
      'method' => 'setPersonLanguage',
      'helperClass' => 'agPersonLanguageHelper'
    );
    $this->importComponents[] = array(
      'component' => 'setImportEntityHash',
      'throwOnError' => FALSE,
      'method' => 'setImportEntityHash'
    );
  }

  /**
   * Method to create entity hashes for import rows
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function createEntityHashes($throwOnError, Doctrine_Connection $conn)
  {
    // loop through the import data
    $hashes = array();
    $matched = 0;
    foreach ($this->importData as $rowId => &$rowData) {

      // first calculate the hash without the entity ID
      $hashData = $rowData['_rawData'];
      unset($hashData['entity_id']);
      ksort($hashData);
      $hash = md5(json_encode($hashData));

      // we do this for in-batch duplicate detection
      if (!isset($hashes[$hash])) {
        $hashes[$hash] = TRUE;
        $rowData['primaryKeys']['import_hash'] = $hash;

        // build a specific hash data array that excludes entity ID
        if (!isset($rowData['_rawData']['entity_id'])) {
          $entityId = $this->getEntityFromImportHash($hash);
          if (!empty($entityId)) {
            $rowData['_rawData']['entity_id'] = $entityId;
            $matched++;
          }
        }
      } else {
        $eventMsg = 'Duplicate import row found on row ' . $rowId . '. Ignoring duplicate.';
        $this->eh->logWarning($eventMsg);
        unset($this->importData[$rowId]);
      }
    }
    unset($rowData);

    if ($matched > 0) {
      $eventMsg = 'Found ' . $matched . ' rows without Entity ID\'s that matched a previous import. ' .
      'Rows were assigned the matched Entity ID.';
      $this->eh->logWarning($eventMsg);
    }
  }

  /**
   * Method to retrieve an entity import row hash
   * @param string $importHash An import row hash
   * @return integer An import hash ID
   */
  protected function getEntityFromImportHash($importHash) {

    $q = agDoctrineQuery::create()
      ->select('ieh.entity_id')
      ->from('agImportEntityHash ieh')
      ->where('ieh.row_hash = ?', $importHash)
      ->useResultCache(TRUE, 1800);

    $result = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    if (empty($result))
    {
      $cacheDriver = Doctrine_Manager::getInstance()->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
      $cacheDriver->delete($q->getResultCacheHash());
    }

    return $result;
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
    try {
      $this->loadCurrentEntities($conn);
      $this->setNewEntities($conn);
    } catch (Exception $e) {
      foreach ($this->importData as $rowId => &$rowData) {
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
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */

  protected function loadCurrentEntities(Doctrine_Connection $conn)
  {
    // explicit declarations are good
    $rawEntityIds = array();

    // loop our import data and pick up any existing entity Ids
    foreach ($this->importData as $rowId => $rowData) {
      $rawData = $rowData['_rawData'];

      if (isset($rawData['entity_id'])) {
        $entityId = $rawData['entity_id'];
        // we apply a small check for dupes
        if (!isset($rawEntityIds[$entityId])) {
          $rawEntityIds[$entityId] = TRUE;
        } else {
          $eventMsg = 'A previous row was identified with Entity ID #' . $entityId . '. Skipping ' .
            'subsequent row.';
          $this->eh->logAlert($eventMsg, 0);

          unset($this->importData[$rowId]);
        }
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
        ->whereIn('e.id', array_keys($rawEntityIds));
    $entities = $q->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_ARRAY);
    $q->free();

    // we no longer need this array (used for the ->whereIN)
    unset($rawEntityIds);

    $this->eh->logDebug('{' . count($entities) . '} person entities found in the database.');

    $staffTable = $conn->getTable('agStaff');
    $staffColl = new Doctrine_Collection('agStaff');

    //loop foreach $entities member
    foreach ($entities as $entityId => $entityData) {
      // if staff id doesn't exist yet, make it so
      if (is_null($entityData[1])) {
        $this->eh->logDebug('Person ID {' . $entityData[0] . '} exists but is not staff. ' .
            'Creating staff record.');
        $nRec = new agStaff($staffTable,TRUE);
        $nRec['person_id'] = $entityData[0];
        $staffColl->add($nRec, $entityId);
      }
    }
    $staffColl->save($conn);

    // add the newly saved id's to the entities array
    foreach($staffColl as $entityId => $staffRec) {
       $entities[$entityId][1] = $staffRec['id'];
    }

    // free some mem
    $staffColl->free();
    unset($staffColl);

    // update our row keys array
    $this->eh->logDebug('Updating primary keys for found entities.');
    foreach ($this->importData as $rowId => &$rowData) {
      if (array_key_exists('entity_id', $rowData['_rawData'])) {
        $entityId = $rowData['_rawData']['entity_id'];
        if (array_key_exists($entityId, $entities)) {
          $rowData['primaryKeys']['entity_id'] = $entityId;
          $rowData['primaryKeys']['person_id'] = $entities[$entityId][0];
          $rowData['primaryKeys']['staff_id'] = $entities[$entityId][1];
          unset($entities[$entityId]);
        }
      }
    }
    unset($rowData);
  }

  /*
   * Method to set new entities from our $_rawData.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */

  protected function setNewEntities(Doctrine_Connection $conn)
  {
    // make these explicit at the to to avoid building the graph multiple times
    $entityTable = $conn->getTable('agEntity');
    $personTable = $conn->getTable('agPersonBulkLoad');
    $staffTable = $conn->getTable('agStaffBulkLoad');

    // define the collections once
    $entityColl = new Doctrine_Collection('agEntity');
    $personColl = new Doctrine_Collection('agPersonBulkLoad');
    $staffColl = new Doctrine_Collection('agStaffBulkLoad');

    // add new entities / persons / staff for records with bad or no entity ids.
    foreach ($this->importData as $rowId => $rowData) {
      // initially be pessimistic about the actions we'll need to take
      $createNew = FALSE;
      $warnBadEntity = FALSE;

      // pick these up by reference so we can use the pointers more easily
      $rawData = $rowData['_rawData'];
      $pKeys = $rowData['primaryKeys'];

      // this should satisfy both NULL entity_ids and ones that didn't make our initial filter
      if (!isset($rawData['entity_id'])) {
        $createNew = TRUE;
      } elseif (!isset($pKeys['entity_id']) || $rawData['entity_id'] != $pKeys['entity_id']) {
        $createNew = TRUE;
        $warnBadEntity = TRUE;
      }

      if ($createNew) {
        $this->eh->logDebug('Creating new entity for import rowId {' . $rowId . '}.');
        $newRec = new agEntity($entityTable, TRUE);
        $entityColl->add($newRec, $rowId);
      }

      // here we log warnings about bad entities that we've been passed and chose to override
      if ($warnBadEntity) {
        $warnMsg = sprintf("Bad entity id (%s).  Generated a new entity id.", $rawData['entity_id']);
        $this->eh->logWarning($warnMsg);
      }
    }
    $this->eh->logDebug('Saving new entities.');
    $entityColl->save($conn);
    $this->eh->logDebug('New entities successfully saved.');

    // now, loop through using the recently return import data and build staff the same way
    foreach ($this->importData as $rowId => &$rowData) {

      // grab our entityId from the entity collection or skip if unfound
      if (! isset($entityColl[$rowId])) {
        if (isset($rowData['primaryKeys']['person_id'])) {
          // no new parent, child record exists
          continue;
        }
        // no new parent, child record does not exist
        $entityId = $rowData['primaryKeys']['entity_id'];
      } else {
        // new parent, child record is irrelevant
        $entityId = $entityColl[$rowId]['id'];
      }

      // set our pkeys data
      $rowData['primaryKeys']['entity_id'] = $entityId;

      // create a new person entry and add it to the person collection
      $this->eh->logDebug('Creating new person for import Entity ID {' . $entityId . '}.');
      $newRec = new agPersonBulkLoad($personTable, TRUE);
      $newRec['entity_id'] = $entityId;
      $personColl->add($newRec, $rowId);
    }
    unset($rowData);
    $this->eh->logDebug('Saving new persons.');
    $personColl->save($conn);
    $this->eh->logDebug('New persons successfully saved.');

    // now we can free the entity collection's resources
    $entityColl->free();
    unset($entityColl);

    // repeat for staff
    foreach ($this->importData as $rowId => &$rowData) {

      // grab our entityId from the entity collection or skip if unfound
      if (! isset($personColl[$rowId])) {
        if (isset($rowData['primaryKeys']['staff_id'])) {
          // no new parent, child record exists
          continue;
        }
        // no new parent, child record does not exist
        $personId = $rowData['primaryKeys']['person_id'];
      } else {
        // new parent, child record is irrelevant
        $personId = $personColl[$rowId]['id'];
      }

      // set our pkeys data
      $rowData['primaryKeys']['person_id'] = $personId;

      // create a new staff entry and add it to the staff collection
      $this->eh->logDebug('Creating new staff for import Person ID {' . $personId . '}.');
      $newRec = new agStaffBulkLoad($staffTable, TRUE);
      $newRec['person_id'] = $personId;
      $staffColl->add($newRec, $rowId);
    }
    unset($rowData);
    $this->eh->logDebug('Saving new staff.');
    $staffColl->save($conn);
    $this->eh->logDebug('New staff successfully saved.');

    // now we can free the person collection's resources
    $personColl->free();
    unset($personColl);

    // repeat for staff
    foreach ($this->importData as $rowId => &$rowData) {
      // grab our staffId from the staff collection
      if (isset($staffColl[$rowId])) {
        $rowData['primaryKeys']['staff_id'] = $staffColl[$rowId]['id'];
      }
    }
    unset($rowData);

    // now we can free the staff collection's resources
    $staffColl->free();
    unset($staffColl);
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
    $importNameTypes = array('first_name' => 'given', 'middle_name' => 'middle', 'last_name' => 'family');
    $personNames = array();
    $results = array();

    // let's get ahold of our helper object since we're going to use him/her a lot
    $pnh = & $this->helperObjects['agPersonNameHelper'];

    // get our name types and map them back to the importNameTypes
    $nameTypes = $pnh->getNameTypeIds(array_values($importNameTypes));
    foreach ($importNameTypes as $key => &$val) {
      $val = $nameTypes[$val];
    }
    unset($val);
    unset($nameTypes);

    // loop through our raw data and build our person name data
    foreach ($this->importData as $rowId => $rowData) {
      if (isset($rowData['primaryKeys']['person_id'])) {
        $personId = $rowData['primaryKeys']['person_id'];

        // we start with an empty array, devoid of types in case the entity has no types/values
        $personNames[$personId] = array();
        foreach ($importNameTypes as $nameType => $nameTypeId) {
          if (isset($rowData['_rawData'][$nameType])) {
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
    $importEmailTypes = array('work_email' => 'work', 'home_email' => 'personal');
    $entityEmails = array();
    $results = array();

    // let's get ahold of our helper object since we're going to use him/her a lot
    $eeh = & $this->helperObjects['agEntityEmailHelper'];

    // get our email types and map them back to the importEmailTypes
    $emailTypes = agEmailHelper::getEmailContactTypeIds(array_values($importEmailTypes));
    foreach ($importEmailTypes as $key => &$val) {
      $val = $emailTypes[$val];
    }
    unset($val);
    unset($emailTypes);

    // loop through our raw data and build our entity email data
    foreach ($this->importData as $rowId => $rowData) {
      if (isset($rowData['primaryKeys']['entity_id'])) {
        // this just makes it easier to use
        $entityId = $rowData['primaryKeys']['entity_id'];

        // we start with an empty array, devoid of types in case the entity has no types/values
        $entityEmails[$entityId] = array();
        foreach ($importEmailTypes as $emailType => $emailTypeId) {
          if (isset($rowData['_rawData'][$emailType])) {
            $entityEmails[$entityId][] = array($emailTypeId, $rowData['_rawData'][$emailType]);
          }
        }
      }
    }

    // pick up some of our components / objects
    $keepHistory = agGlobal::getParam('staff_import_keep_history');
    $enforceStrict = agGlobal::getParam('enforce_strict_contact_formatting');

    // execute the helper and finish
    $results = $eeh->setEntityEmail($entityEmails, $keepHistory, $enforceStrict, $throwOnError,
                                    $conn);
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
    $importPhoneTypes = array('work_phone' => 'work', 'home_phone' => 'home', 'mobile_phone' => 'mobile');
    $entityPhones = array();
    $results = array();

    // let's get ahold of our helper object since we're going to use him/her a lot
    $eph = & $this->helperObjects['agEntityPhoneHelper'];

    // get our email types and map them back to the importEmailTypes
    $phoneTypes = agPhoneHelper::getPhoneContactTypeIds(array_values($importPhoneTypes));
    foreach ($importPhoneTypes as $key => &$val) {
      $val = $phoneTypes[$val];
    }
    unset($val);
    unset($phoneTypes);

    // loop through our raw data and build our entity phone data
    foreach ($this->importData as $rowId => $rowData) {
      if (isset($rowData['primaryKeys']['entity_id'])) {
        // this just makes it easier to use
        $entityId = $rowData['primaryKeys']['entity_id'];

        // we start with an empty array, devoid of types in case the entity has no types/values
        $entityPhones[$entityId] = array();
        foreach ($importPhoneTypes as $phoneType => $phoneTypeId) {
          if (array_key_exists($phoneType, $rowData['_rawData'])) {
            $entityPhones[$entityId][] = array($phoneTypeId, array($rowData['_rawData'][$phoneType]));
          }
        }
      }
    }

    // pick up some of our components / objects
    $keepHistory = agGlobal::getParam('staff_import_keep_history');
    $enforceStrict = agGlobal::getParam('enforce_strict_contact_formatting');

    // execute the helper and finish
    $results = $eph->setEntityPhone($entityPhones, $keepHistory, $enforceStrict, $throwOnError,
                                    $conn);
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
    $importAddressTypes = array('work_address' => 'work', 'home_address' => 'home');
    $importAddressElements = array('line_1' => 'line 1', 'line_2' => 'line 2', 'city' => 'city',
      'state' => 'state', 'zip' => 'zip5', 'country' => 'country');
    $entityAddresses = array();
    $missingGeo = 0;
    $results = array();
    $errMsg = NULL;

    // let's get ahold of our helper object since we're going to use him/her a lot
    $eah = & $this->helperObjects['agEntityAddressHelper'];

    // get our address types and map them back to the importAddressTypes
    $addressTypes = agAddressHelper::getAddressContactTypeIds(array_values($importAddressTypes));
    foreach ($importAddressTypes as $key => &$val) {
      $val = $addressTypes[$val];
    }
    unset($val);
    unset($addressTypes);

    // get our address elements and map them back to the importAddressElements
    $addressElements = agAddressHelper::getAddressElementIds(array_values($importAddressElements));
    foreach ($importAddressElements as $key => &$val) {
      $val = $addressElements[$val];
    }
    unset($val);
    unset($addressElements);

    // get our address standards and geo match scores
    $addressStandard = agGlobal::getParam('staff_import_address_standard');
    $addressStandardId = agAddressHelper::getAddressStandardIds(array($addressStandard));
    $addressStandardId = $addressStandardId[$addressStandard];
    $geoMatchScore = agGlobal::getParam('default_geo_match_score');
    $geoMatchScoreId = agGeoHelper::getGeoMatchScoreIds(array($geoMatchScore));
    $geoMatchScoreId = $geoMatchScoreId[$geoMatchScore];

    // loop through our raw data and build our entity address data
    foreach ($this->importData as $rowId => $rowData) {
      if (isset($rowData['primaryKeys']['entity_id'])) {
        // this just makes it easier to use
        $entityId = $rowData['primaryKeys']['entity_id'];
        $rawData = $rowData['_rawData'];

        // we start with an empty array, devoid of types in case the entity has no types/values
        list($homeAddr, $workAddr) = array(array(), array());
        foreach ($importAddressElements AS $element => $id) {
          if (isset($rawData['home_address_' . $element])) {
            $homeAddr[$id] = $rawData['home_address_' . $element];
          }

          if (isset($rawData['work_address_' . $element])) {
            $workAddr[$id] = $rawData['work_address_' . $element];
            $hasWorkElem = TRUE;
          }
        }

        if (count($homeAddr) > 0) {
          if (isset($rawData['home_latitude']) && isset($rawData['home_longitude'])) {
            $homeAddrComp = array($homeAddr, $addressStandardId);
            $homeAddrComp[] = array(array(array($rawData['home_latitude'],
                  $rawData['home_longitude'])),
              $geoMatchScoreId);
            $entityAddresses[$entityId][] = array($importAddressTypes['home_address'], $homeAddrComp);
          } else {
            // log our error or at least grab our counter
            $missingGeo++;
            if ($throwOnError) {
              $errMsg = sprintf('Missing home address/geo information from record id  %d', $rowId);
              $this->eh->logErr($errMsg);
              throw new Exception($errMsg);
            }
          }
        }

        if (count($workAddr) > 0) {
          if (isset($rawData['work_latitude']) && isset($rawData['work_longitude'])) {
            $workAddrComp = array($workAddr, $addressStandardId);
            $workAddrComp[] = array(array(array($rawData['work_latitude'],
                $rawData['work_longitude'])),
              $geoMatchScoreId);
            $entityAddresses[$entityId][] = array($importAddressTypes['work_address'], $workAddrComp);
          } else {
            // log our error or at least grab our counter
            $missingGeo++;
            if ($throwOnError) {
              $errMsg = sprintf('Missing work address/geo information from record id  %d', $rowId);
              $this->eh->logErr($errMsg);
              throw new Exception($errMsg);
            }
          }
        }
      }
    }

    // pick up some of our components / objects
    $keepHistory = agGlobal::getParam('staff_import_keep_history');
    $enforceStrict = agGlobal::getParam('enforce_strict_contact_formatting');

    if (!isset($this->geoSourceId)) {
      $this->geoSourceId = agDoctrineQuery::create()
          ->select('gs.id')
          ->from('agGeoSource gs')
          ->where('gs.geo_source = ?', agGlobal::getParam('staff_import_geo_source'))
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    }
    $geoSourceId = $this->geoSourceId;

    $addressGeo = array();
    // @TODO Handle geo upserts along with address.
    // execute the helper and finish
    $results = $eah->setEntityAddress($entityAddresses, $geoSourceId, $keepHistory, $enforceStrict,
                                      $throwOnError, $conn);
    unset($entityAddresses);

    if ($missingGeo > 0) {
      $warnMsg = 'Batch contains ' . $missingGeo . ' addresses without associated geo information.';
      $this->eh->logWarning($warnMsg);
    }
    // @todo do your results reporting here
  }

  /**
   * Method to set custom field data for persons.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setPersonCustomField($throwOnError, Doctrine_Connection $conn)
  {
    $errMsg = NULL;
    $importCustomFields = array('drivers_license_class' => 'Drivers License Class',
      'pms_id' => 'PMS ID',
      'civil_service_title' => 'Civil Service Title');

    // save this for future iterations
    if (!isset($this->customFieldIds)) {
      $this->customFieldIds = agDoctrineQuery::create()
          ->select('pcf.person_custom_field')
          ->addSelect('pcf.id')
          ->from('agPersonCustomField pcf')
          ->whereIn('pcf.person_custom_field', array_values($importCustomFields))
          ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
    }
    $customFieldIds = $this->customFieldIds;

    // first loop through and grab all our personIds into a single array
    // we intentionally let this fail if there is no person id since we should never have that case
    $personIds = array();
    $updatedPersonCustomField = array();
    foreach ($this->importData as $rowId => $rowData) {
      $personIds[$rowData['primaryKeys']['person_id']] = $rowId;
      $updatedPersonCustomField[$rowData['primaryKeys']['person_id']] = array();
    }

    // get all of our custom records in a collection
    $coll = agDoctrineQuery::create($conn)
        ->select('pcv.*')
        ->from('agPersonCustomFieldValue pcv')
        ->whereIn('pcv.person_id', array_keys($personIds))
        ->execute();

    // Perform custom updates on person with existing custom fields.
    foreach ($coll AS $index => $record) {
      $pId = $record['person_id'];
      $rawData = $this->importData[$personIds[$pId]]['_rawData'];
      $recordCustomFieldId = $record['person_custom_field_id'];
      $customField = array_search(array_search($recordCustomFieldId, $customFieldIds),
                                               $importCustomFields);

      if (isset($rawData[$customField])) {
        $record['value'] = $rawData[$customField];
        $updatedPersonCustomField[$pId][] = $customField;
      } else {
        // Delete record if record exists in db, but none is provided in import.
        $coll->remove($index);
        $updatedPersonCustomField[$pId][] = $customField;
      }
    }

    try {
      $coll->save($conn);
    } catch (Exception $e) {
      if ($throwOnError) {
        throw new Exception($errMsg);
      }
    }

    $coll->free();
    unset($coll);

    $personCustomFieldTable = $conn->getTable('agPersonCustomFieldValue');
    $coll = new Doctrine_Collection('agPersonCustomFieldValue');

    // Now process person with new custom field if provided in import.
    foreach ($personIds AS $pId => $rowId) {
      if (count($updatedPersonCustomField[$pId]) < count($importCustomFields)) {
        $newCustomFields = array_diff(array_keys($importCustomFields),
                                                 $updatedPersonCustomField[$pId]);
        $rawData = $this->importData[$personIds[$pId]]['_rawData'];
        foreach ($newCustomFields AS $customField) {
          $mappedCustomField = $importCustomFields[$customField];
          if (isset($rawData[$customField])) {
            $nRec = new agPersonCustomFieldValue($personCustomFieldTable, TRUE);
            $nRec['person_id'] = $pId;
            $nRec['person_custom_field_id'] = $customFieldIds[$importCustomFields[$customField]];
            $nRec['value'] = $rawData[$customField];

            $coll->add($nRec);
          }
        }
      }

      // get a little memory back
      unset($personIds[$pId]);
      unset($updatedPersonCustomField[$pId]);
    }

    // save
    $coll->save($conn);
    $coll->free();
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
    if (!isset($this->organizationIds)) {
      $this->organizationIds = agDoctrineQuery::create()
          ->select('o.organization')
          ->addSelect('o.id')
          ->from('agOrganization o')
          ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
      $this->organizationIds = array_change_key_case($this->organizationIds, CASE_LOWER);
    }
    $organizationIds = $this->organizationIds;

    if (!isset($this->stfRscTypeIds)) {
      $this->stfRscTypeIds = agDoctrineQuery::create()
          ->select('srt.staff_resource_type')
          ->addSelect('srt.id')
          ->from('agStaffResourceType srt')
          ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
      $this->stfRscTypeIds = array_change_key_case($this->stfRscTypeIds, CASE_LOWER);
    }
    $stfRscTypeIds = $this->stfRscTypeIds;

    if (!isset($this->stfRscStatusIds)) {
      $this->stfRscStatusIds = agDoctrineQuery::create()
          ->select('srs.staff_resource_status')
          ->addSelect('srs.id')
          ->from('agStaffResourceStatus srs')
          ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
      $this->stfRscStatusIds = array_change_key_case($this->stfRscStatusIds, CASE_LOWER);
    }
    $stfRscStatusIds = $this->stfRscStatusIds;

    // check for required columns
    $staffIds = array();
    $requiredColumns = array( 'organization' => 'organizationIds',
                              'resource_type' => 'stfRscTypeIds',
                              'resource_status' => 'stfRscStatusIds');
    foreach ($this->importData AS $rowId => &$rowData) {
      // used at the end to determine whether to continue processing this record
      $err = FALSE;

      // loop through each of the required columns and validate it
      foreach ($requiredColumns as $column => $validator) {
        if (! isset($rowData['_rawData'][$column])) {
          $errMsg = 'Required column ' . $column . ' is missing from record id ' . $rowId . '.';
          $err = TRUE;
        } else if (!isset(${$validator}[strtolower($rowData['_rawData'][$column])])) {
          $errMsg = 'Invalid ' . $column . ' "' . $rowData['_rawData'][$column] . '" given for ' .
          'record id ' . $rowId . '.';
          $err = TRUE;
        } else {
          $staffIds[$rowData['primaryKeys']['staff_id']][strtolower($rowData['_rawData']['resource_type'])] = $rowId;
        }

        // oh, poo!
        if ($err) {
          // log our error either way
          $this->eh->logErr($errMsg);

          // if our calling method instructs us to throw, let's do that too
          if ($throwOnError) {
            throw new Exception($errMsg);
          }

          // otherwise just break
          break;
        }
      }
    }

    // build a collection of good / known staffIds
    $coll = agDoctrineQuery::create($conn)
        ->select('sr.*')
        ->from('agStaffResource sr')
        ->whereIn('sr.staff_id', array_keys($staffIds))
        ->execute();

    // Perform organization updates on existing staff resource.
    foreach ($coll AS $index => $record) {
      $stfId = $record['staff_id'];
      $stfRscType = array_search($record['staff_resource_type_id'], $stfRscTypeIds);

      // check for it in our existing db
      if (isset($staffIds[$stfId][$stfRscType])) {
        $rawData = $this->importData[$staffIds[$stfId][$stfRscType]]['_rawData'];

        $record['organization_id'] = $organizationIds[strtolower($rawData['organization'])];
        $record['staff_resource_status_id'] = $stfRscStatusIds[strtolower($rawData['resource_status'])];
        unset($staffIds[$stfId][$stfRscType]);
      }
    }
    unset($record);

    $coll->save($conn);
    $coll->free();
    unset($coll);

    $staffResourceTable = $conn->getTable('agStaffResource');
    $coll = new Doctrine_Collection('agStaffResource');

    // Now $staffIds are left with only new staff resources.
    foreach ($staffIds AS $stfId => $stfResources) {
      foreach ($stfResources AS $stfRsc => $rowId) {
        // pick up our rawdata
        $rawData = $this->importData[$rowId]['_rawData'];

        // create a new record object
        $rec = new agStaffResource($staffResourceTable, TRUE);
        $rec['staff_id'] = $stfId;
        $rec['organization_id'] = $organizationIds[strtolower($rawData['organization'])];
        $rec['staff_resource_type_id'] = $stfRscTypeIds[strtolower($rawData['resource_type'])];
        $rec['staff_resource_status_id'] = $stfRscStatusIds[strtolower($rawData['resource_status'])];

        // add it to our collection
        $coll->add($rec);
      }

      // try to offset building all those objects a little
      unset($staffIds[$stfId]);
    }

    // save and we're done
    $coll->save($conn);
    $coll->free();
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
    $importLanguageComponents = array('language_1' => array('l1_speak' => 'speak',
        'l1_read' => 'read',
        'l1_write' => 'write'),
      'language_2' => array('l2_speak' => 'speak',
        'l2_read' => 'read',
        'l2_write' => 'write'),
      'language_3' => array('l3_speak' => 'speak',
        'l3_read' => 'read',
        'l3_write' => 'write')
    );

    $personLanguages = array();
    $results = array();

    // let's get ahold of our helper object since we're going to use him/her a lot
    $plh = & $this->helperObjects['agPersonLanguageHelper'];


    // loop through our raw data and build our person language data
    foreach ($this->importData as $rowId => $rowData) {
      if (isset($rowData['primaryKeys']['person_id'])) {
        // this just makes it easier to use
        $personId = $rowData['primaryKeys']['person_id'];

        // Always initialize $languageComponents as an empty array.  Assign an empty array to
        // $personLanguages only if no language was specified from import.
        $languageComponents = array();
        foreach ($importLanguageComponents as $importLanguage => $langComponents) {
          if (isset($rowData['_rawData'][$importLanguage])) {
            // Always initialize $formatCompetencies as an emtpy array. Assign an emtpy array to
            // $languageComponents only if no format/competency details is specified for the
            // language from import.
            $formatCompetencies = array();
            foreach ($langComponents as $importFormat => $dbFormat) {
              if (isset($rowData['_rawData'][$importFormat])) {
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
    $createEdgeTableValues = agGlobal::getParam('create_edge_table_values');
    ;

    // execute the helper and finish
    $results = $plh->setPersonLanguages($personLanguages, $keepHistory, $createEdgeTableValues,
                                        $throwOnError, $conn);
    unset($personLanguages);

    // @todo do your results reporting here
  }

  /**
   * Method to set import entity hash for future retrieval
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setImportEntityHash($throwOnError, Doctrine_Connection $conn)
  {
    $entityHashes = array();
    $table = $conn->getTable('agImportEntityHash');

    // simplify our array to be a k/v pair
    foreach ($this->importData as $importData) {
      $entityHashes[$importData['primaryKeys']['entity_id']] = $importData['primaryKeys']['import_hash'];
    }

    // find all existing records
    $coll = agDoctrineQuery::create($conn)
      ->select('*')
        ->from('agImportEntityHash ieh INDEXBY ieh.entity_id')
        ->whereIn('ieh.entity_id', array_keys($entityHashes))
        ->execute();

    // update existing row hashes
    foreach ($coll as $entityId => $rec) {
      $rec['row_hash'] = $entityHashes[$entityId];
      unset($entityHashes[$entityId]);
    }

    // make new records as appropriate
    foreach ($entityHashes as $entityId => $importHash) {
      $rec = new agImportEntityHash($table, TRUE);
      $rec['entity_id'] = $entityId;
      $rec['row_hash'] = $importHash;

      // this shouldn't error if entity uniqueness is preserved above
      $coll->add($rec, $entityId);
    }

    // save and release
    $coll->save($conn);
    $coll->free();
  }

}
