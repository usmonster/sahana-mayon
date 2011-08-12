<?php

/**
 * Normalizing facility import data.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUY SPS
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agFacilityImportNormalization extends agImportNormalization
{

  protected $scenarioId = NULL,
            $defaultFacilityGroupActivationSequence,
            $defaultFacilityResourceActivationSequence,
            $staffResourceTypes = array();

  /**
   * Method to return an instance of this class
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   * @return agFacilityImportNormalization An instance of this class
   */
  public static function getInstance($scenarioId, $tempTable, $logEventLevel = NULL)
  {
    $self = new self();
    $self->__setScenario($scenarioId);
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
    if (is_null($tempTable))
    {
      $tempTable = 'temp_facility_import';
    }

    // DO NOT REMOVE
    parent::__init($tempTable, $logEventLevel);

    // set the import components array as a class property
    $this->setImportComponents();
    $this->tempTableOptions = array('type' => 'MYISAM', 'charset' => 'utf8');
    $this->importHeaderStrictValidation = TRUE;

    $this->eh->setErrThreshold(intval(agGlobal::getParam('import_error_threshold')));

    // Construct lookup variables.
    $this->buildLookup();
  }

  /**
   * Method to pre-gather all lookup values into class properties for later facility import 
   * processing.  Note: All returned values are converted to all lower case characters to gaurantee
   * a case-insensitive match.  For all data matching against these class property values much also
   * be pre- lower case.
   */
  protected function buildLookup()
  {
    $this->facRscTypeAbbrIds = agDoctrineQuery::create()
            ->select('frt.facility_resource_type_abbr')
            ->addSelect('frt.id')
            ->from('agFacilityResourceType frt')
            ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
    $this->facRscTypeAbbrIds = array_change_key_case($this->facRscTypeAbbrIds, CASE_LOWER);

    $this->facRscStatusIds = agDoctrineQuery::create()
            ->select('frs.facility_resource_status')
            ->addSelect('frs.id')
            ->from('agFacilityResourceStatus frs')
            ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
    $this->facRscStatusIds = array_change_key_case($this->facRscStatusIds, CASE_LOWER);

    $this->facGrpTypeIds = agDoctrineQuery::create()
            ->select('fg.facility_group_type')
            ->addSelect('fg.id')
            ->from('agFacilityGroupType fg')
            ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
    $this->facGrpTypeIds = array_change_key_case($this->facGrpTypeIds, CASE_LOWER);

    $this->facGrpAllocStatusIds = agDoctrineQuery::create()
            ->select('fgas.facility_group_allocation_status')
            ->addSelect('fgas.id')
            ->from('agFacilityGroupAllocationStatus fgas')
            ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
    $this->facGrpAllocStatusIds = array_change_key_case($this->facGrpAllocStatusIds, CASE_LOWER);

    $this->facRscAllocStatusIds = agDoctrineQuery::create()
            ->select('fras.facility_resource_allocation_status')
            ->addSelect('fras.id')
            ->from('agFacilityResourceAllocationStatus fras')
            ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
    $this->facRscAllocStatusIds = array_change_key_case($this->facRscAllocStatusIds, CASE_LOWER);

    $this->defaultFacilityGroupActivationSequence = agGlobal::getParam('default_facility_group_activation_sequence');
    $this->defaultFacilityResourceActivationSequence = agGlobal::getParam('default_facility_resource_activation_sequence');
  }

  public function __setScenario($scenarioId)
  {
    $this->scenarioId = $scenarioId;
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
    $this->unprocessedBaseName = agGlobal::getParam('unprocessed_facility_import_basename');
  }

  /**
   * Method to set the dynamic field type. Does not need to be called here, will be called in parent
   */
  protected function setDynamicFieldType()
  {
    $this->dynamicFieldType = array('type' => 'integer', 'length' => 6);
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
    $importSpec['facility_name'] = array('type' => "string", 'length' => 64);
    $importSpec['facility_code'] = array('type' => "string", 'length' => 10);
    $importSpec['facility_resource_type_abbr'] = array('type' => "string", 'length' => 10);
    $importSpec['facility_resource_status'] = array('type' => "string", 'length' => 40);
    $importSpec['facility_capacity'] = array('type' => "integer", 'length' => 2);
    $importSpec['facility_activation_sequence'] = array('type' => "integer", 'length' => 1);
    $importSpec['facility_allocation_status'] = array('type' => "string", 'length' => 30);
    $importSpec['facility_group'] = array('type' => "string", 'length' => 64);
    $importSpec['facility_group_type'] = array('type' => "string", 'length' => 30);
    $importSpec['facility_group_allocation_status'] = array('type' => "string", 'length' => 30);
    $importSpec['facility_group_activation_sequence'] = array('type' => "integer", 'length' => 1);
    $importSpec['work_email'] = array('type' => "string", 'length' => 255);
    $importSpec['work_phone'] = array('type' => "string", 'length' => 16);
    $importSpec['street_1'] = array('type' => "string", 'length' => 128);
    $importSpec['street_2'] = array('type' => "string", 'length' => 128);
    $importSpec['city'] = array('type' => "string", 'length' => 128);
    $importSpec['state'] = array('type' => "string", 'length' => 128);
    $importSpec['postal_code'] = array('type' => "string", 'length' => 128);
    $importSpec['borough'] = array('type' => "string", 'length' => 128);
    $importSpec['country'] = array('type' => "string", 'length' => 128);
    $importSpec['longitude'] = array('type' => "decimal", 'length' => 12, 'scale' => 8);
    $importSpec['latitude'] = array('type' => "decimal", 'length' => 12, 'scale' => 8);
//    $importSpec['opr_max'] = array('type' => "integer", 'length' => 6);

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
      'component' => 'facility',
      'throwOnError' => TRUE,
      'method' => 'setFacilities',
    );
    $this->importComponents[] = array(
      'component' => 'facilityResource',
      'throwOnError' => TRUE,
      'method' => 'setFacilityResources'
    );
    $this->importComponents[] = array(
      'component' => 'facilityGroup',
      'throwOnError' => TRUE,
      'method' => 'setScenarioFacilityGroups',
    );
    $this->importComponents[] = array(
      'component' => 'scenarioFacilityResource',
      'throwOnError' => TRUE,
      'method' => 'setScenarioFacilityResources',
    );
    $this->importComponents[] = array(
      'component' => 'email',
      'throwOnError' => FALSE,
      'method' => 'setEntityEmail',
      'helperClass' => 'agEntityEmailHelper'
    );
    $this->importComponents[] = array(
      'component' => 'phone',
      'throwOnError' => FALSE,
      'method' => 'setEntityPhone',
      'helperClass' => 'agEntityPhoneHelper'
    );

    $this->importComponents[] = array(
      'component' => 'address',
      'throwOnError' => TRUE,
      'method' => 'setEntityAddress',
      'helperClass' => 'agEntityAddressHelper'
    );

    $this->importComponents[] = array(
      'component' => 'resourceReqs',
      'throwOnError' => TRUE,
      'method' => 'setResourceRequirements',
    );

  }

  /**
   * Method to check for required columns and calls the method to set / create new entities, site,
   * and facility.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setFacilities($throwOnError, Doctrine_Connection $conn)
  {
    // check for required columns
    $requiredColumns = array('facility_name', 'facility_code');

    foreach ($this->importData AS $rowId => &$rowData)
    {
      // used at the end to determine whether to continue processing this record
      $err = FALSE;

      foreach ($requiredColumns AS $column)
      {
        if (!isset($rowData['_rawData'][$column]))
        {
          $errMsg = 'Required column ' . $column . ' is missing from record id ' . $rowId .
                  '.  This facility is excluded from import';
          unset($this->import[$rowId]);
          $err = TRUE;
        }

        // oh, poo!
        if ($err)
        {
          // log our error either way
          $this->eh->logErr($errMsg);

          // otherwise just break
          break;
        }
      }
    }

    // we need to capture errors just to make sure we don't store failed ID inserts
    try {
      $this->updateFacilities($conn);
    }
    catch (Exception $e) {
      foreach ($this->importData as $rowId => &$rowData)
      {
        unset($rowData['primaryKeys']['entity_id']);
        unset($rowData['primaryKeys']['site_id']);
        unset($rowData['primaryKeys']['facility_id']);
      }

      // continue throwing our exception
      throw $e;
    }
  }

  /**
   * Method to set / create new entities, site, and facility.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function updateFacilities(Doctrine_Connection $conn)
  {
    // explicit declarations are good
    $rawFacilityCodes = array();
    $uniqFacilities = array();
    $facilityEntities = array();

    // loop our import data and pick up any existing facility code
    foreach ($this->importData as $rowId => $rowData)
    {
      $rawData = $rowData['_rawData'];

      if (isset($rawData['facility_code']))
      {
        $uniqFacilities[strtolower($rawData['facility_code'])][] = $rowId;
        $rawFacilityCodes[$rowId] = strtolower($rawData['facility_code']); // >>>> REMOVE?
      }
    }
    unset($rowData);

    // find current entity + facilities
    $q = agDoctrineQuery::create($conn)
                    ->select('f.*')
                    ->addSelect('s.*')
                    ->from('agFacility f INDEXBY f.facility_code')
                    ->innerJoin('f.agSite s')
                    ->whereIn('f.facility_code', array_keys($uniqFacilities));
    $facilityColl = $q->execute();

    $this->eh->logDebug('{' . count($facilityColl) . '} facility entities found in the database.');

    // update our row keys array
    $this->eh->logDebug('Updating primary keys for found entities.');

    // Add entity id and facility id to $this->importData['primaryKeys'].
    // While we're looping the existing facility, make facility update where necessary.
    foreach ($facilityColl AS $facilityCode => $facility)
    {
      foreach ($uniqFacilities[$facilityCode] as $index => $rowId) {
        $this->importData[$rowId]['primaryKeys']['entity_id'] = $facility->agSite['entity_id'];
        $this->importData[$rowId]['primaryKeys']['facility_id'] = $facility['id'];
      }

      $facility['facility_name'] = $this->importData[$rowId]['_rawData']['facility_name'];
      unset($uniqFacilities[$facilityCode]);
    }
    $facilityColl->save($conn);
    $facilityColl->free(TRUE);
    unset($facilityColl);

    // Build the table and collection objects for entity and site. Only build table object for
    // facility as we will use the facility collection created from above.
    $entityTable = $conn->getTable('agEntity');
    $entityColl = new Doctrine_Collection('agEntity');
    $siteTable = $conn->getTable('agSite');
    $siteColl = new Doctrine_Collection('agSite');
    $facilityTable = $conn->getTable('agFacility');
    $facilityColl = new Doctrine_Collection('agFacility');

    // Create new entity using rowId as the key to $entityColl.
    foreach ($uniqFacilities AS $facilityCode => $rowIds)
    {
      $entity = new agEntity($entityTable, TRUE);
      $entityColl->add($entity, $facilityCode);
    }
    $entityColl->save($conn);

    // Create new site using rowId as the key to $siteColl.
    foreach ($entityColl AS $facilityCode => $entity)
    {
      $entityId = $entity['id'];
      $site = new agSite($siteTable, TRUE);
      $site['entity_id'] = $entityId;
      $siteColl->add($site, $facilityCode);

      $facilityEntities[$facilityCode] = $entityId;
    }
    $siteColl->save($conn);
    $entityColl->free(TRUE);
    unset($entityColl);

    // Create new facility using rowId as the key to $facilityColl.
    foreach ($siteColl AS $facilityCode => $site)
    {
      // whoah! what??? how do you not isset()? well, because of how uniq is built we are guaranteed
      // that each entry has at least one rowId, eg, position [0]
      $rawData = $this->importData[$uniqFacilities[$facilityCode][0]]['_rawData'];
      $facility = new agFacility($facilityTable, TRUE);
      $facility['site_id'] = $site['id'];
      $facility['facility_name'] = $rawData['facility_name'];
      $facility['facility_code'] = $facilityCode;
      $facilityColl->add($facility, $facilityCode);
    }
    $facilityColl->save($conn);

    foreach ($uniqFacilities as $facilityCode => $rowIds) {
      foreach ($rowIds as $rowId) {
        $this->importData[$rowId]['primaryKeys']['entity_id'] = $facilityEntities[$facilityCode];
        $this->importData[$rowId]['primaryKeys']['facility_id'] = $facilityColl[$facilityCode]['id'];
      }
    }
    $facilityColl->free(TRUE);
    unset($facilityColl);

    unset($facilityEntities);
    unset($uniqFacilities);
  }

  /**
   * Method to check for required columns and to set / create new facility resources.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setFacilityResources($throwOnError, Doctrine_Connection $conn)
  {
    $facRscTypeAbbrIds = $this->facRscTypeAbbrIds;
    $facRscStatusIds = $this->facRscStatusIds;

    // Capacity is a required field but does not reference to any other tables, thus, pointing to
    // NULL value.
    $requiredColumns = array('facility_resource_type_abbr' => 'facRscTypeAbbrIds',
                             'facility_resource_status' => 'facRscStatusIds',
                             'facility_capacity' => NULL
                            );

    $uniqFacRscTypes = array();
    // Check for valid facility resource type abbr and facility resource status provided.
    // Meanwhile also build the $rawFacilityResources array for later use.
    foreach ($this->importData AS $rowId => $rowData)
    {
      // used at the end to determine whether to continue processing this record
      $err = FALSE;

      // loop through each of the required columns and validate it
      foreach ($requiredColumns as $column => $validator)
      {
        if (!isset($rowData['_rawData'][$column]))
        {
          $errMsg = 'Required column ' . $column . ' is missing from record id ' . $rowId . '.';
          $err = TRUE;
        }
        else
        {
          if (empty($validator))
          {
            // Make sure the column's value is a positive integer.
            $value = $rowData['_rawData'][$column];
            if (!ctype_digit($value))
            {
              if (!isset(${$validator}[strtolower($rowData['_rawData'][$column])]))
              {
                $errMsg = 'Invalid ' . $column . ' "' . $rowData['_rawData'][$column] . '" given for ' .
                        'record id ' . $rowId . '.';
                $err = TRUE;
              }
            }
          }
          else
          {
            // Validate all table referencing columns against the db values.
            if (!isset(${$validator}[strtolower($rowData['_rawData'][$column])]))
            {
              $errMsg = 'Invalid ' . $column . ' "' . $rowData['_rawData'][$column] . '" given for ' .
                      'record id ' . $rowId . '.';
              $err = TRUE;
            }
          }
        }

        // oh, poo!
        if ($err)
        {
          // log our error either way
          $this->eh->logErr($errMsg);

          // Throw error immediately if any of the required columns are not provided.
          throw new Exception($errMsg);
        }
      }
      $facilityId = $rowData['primaryKeys']['facility_id'];
      $facRscTypeId = $facRscTypeAbbrIds[strtolower($rowData['_rawData']['facility_resource_type_abbr'])];
//      $rawFacilityResources[$rowId]['facility_id'] = $rowData['primaryKeys']['facility_id'];
//      $rawFacilityResources[$rowId]['facility_resource_type_id'] = $facRscTypeId;
      $uniqFacRscTypes[$facRscTypeId] = TRUE;

      if (!isset($rawFacilityResources[$facilityId][$facRscTypeId])) {
        $rawFacilityResources[$facilityId][$facRscTypeId] = $rowId;
      } else {
        $warnMsg = 'Import row ' . $rowId . ' (' . $rowData['_rawData']['facility_code'] . ' ' .
        $rowData['_rawData']['facility_resource_type_abbr'] . ') is a duplicate of row ' .
          $rawFacilityResources[$facilityId][$facRscTypeId] . ' and will be skipped.';

        unset($this->importData[$rowId]);
        $this->eh->logWarning($warnMsg);
      }
    }

    // Query for related facility resource in a doctrine collection.
    $q = agDoctrineQuery::create($conn)
                  ->from('agFacilityResource fr');

    $firstWhereClause = TRUE;
    foreach ($rawFacilityResources AS $facilityId => $facRscTypes) {
      foreach ($facRscTypes as $facRscTypeId => $rowId) {
        if ($firstWhereClause)
        {
          $q->where('(fr.facility_id = ? AND fr.facility_resource_type_id = ?)',
                    array($facilityId, $facRscTypeId));
          $firstWhereClause = FALSE;
        }
        else
        {
          $q->orWhere('(fr.facility_id = ? AND fr.facility_resource_type_id = ?)',
                      array($facilityId, $facRscTypeId));
        }
      }
    }
    $facilityResourceColl = $q->execute();

    $this->eh->logDebug('{' . count($facilityResourceColl) . '} facility entities found in the database.');

    // update our row keys array
    $this->eh->logDebug('Updating primary keys for found entities.');

    // Add facility resource id to $this->importData['primaryKeys'].
    // While we're looping the existing facility, make facility resource updates where necessary.
    foreach ($facilityResourceColl AS $facRsc)
    {
      $rowId = $rawFacilityResources[$facRsc['facility_id']][$facRsc['facility_resource_type_id']];

      $rowData = & $this->importData[$rowId];
      $rowData['primaryKeys']['facility_resource_id'] = $facRsc['id'];
      $facRscStatusId = $facRscStatusIds[strtolower($rowData['_rawData']['facility_resource_status'])];
      $facRsc['facility_resource_status_id'] = $facRscStatusId;
      $facRsc['capacity'] = $rowData['_rawData']['facility_capacity'];
      unset($rawFacilityResources[$facRsc['facility_id']][$facRsc['facility_resource_type_id']]);
      unset($rowData);
    }
    $facilityResourceColl->save($conn);
    $facilityResourceColl->free(TRUE);
    unset($facilityResourceColl);

    // Create new facility resource using rowId as the key to $facilityResourceColl.
    $facilityResourceColl = new Doctrine_Collection('agFacilityResource');
    $facilityResourceTable = $conn->getTable('agFacilityResource');
    foreach ($rawFacilityResources AS $facilityId => $facRscTypes) {
      foreach ($facRscTypes as $facRscTypeId => $rowId) {
        $rawData = $this->importData[$rowId]['_rawData'];
        $facResource = new agFacilityResource($facilityResourceTable, TRUE);
        $facResource['facility_id'] = $facilityId;
        $facResource['facility_resource_type_id'] = $facRscTypeId;
        $facRscStatusId = $facRscStatusIds[strtolower($rawData['facility_resource_status'])];
        $facResource['facility_resource_status_id'] = $facRscStatusId;
        $facResource['capacity'] = $rawData['facility_capacity'];
        $facilityResourceColl->add($facResource, $rowId);
        unset($rawFacilityResources[$facilityId][$facRscTypeId]);
      }
    }
    $facilityResourceColl->save($conn);

    // Store new facility resource ids to $this->importData['primaryKeys'].
    foreach ($facilityResourceColl AS $rowId => $facilityResource)
    {
      $this->importData[$rowId]['primaryKeys']['facility_resource_id'] = $facilityResource['id'];
    }
    $facilityResourceColl->free(TRUE);
    unset($facilityResourceColl);

    $defaultScenarioFacilityResourceTypesTable = $conn->getTable('agDefaultScenarioFacilityResourceType');
    $defaultScenarioFacilityResourceTypes = agDoctrineQuery::create($conn)
      ->select('dsfrt.*')
        ->from('agDefaultScenarioFacilityResourceType dsfrt INDEXBY dsfrt.facility_resource_type_id')
        ->where('dsfrt.scenario_id = ?', $this->scenarioId)
        ->execute(array());

    foreach ($uniqFacRscTypes as $id => $value) {
      if (!isset($defaultScenarioFacilityResourceTypes[$id])) {
        $defaultScenarioFacilityResourceType = new agDefaultScenarioFacilityResourceType($defaultScenarioFacilityResourceTypesTable, TRUE);
        $defaultScenarioFacilityResourceType['scenario_id'] = $this->scenarioId;
        $defaultScenarioFacilityResourceType['facility_resource_type_id'] = $id;
        $defaultScenarioFacilityResourceTypes->add($defaultScenarioFacilityResourceType, $id);
      }
    }
    $defaultScenarioFacilityResourceTypes->save($conn);
    $defaultScenarioFacilityResourceTypes->free(TRUE);
    unset($defaultScenarioFacilityResourceType);
    unset($defaultScenarioFacilityResourceTypes);
    unset($uniqFacRscTypes);
  }

  /**
   * Method to check for required columns and to set / create new facility resources.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setScenarioFacilityGroups($throwOnError, Doctrine_Connection $conn)
  {
    $facGrpTypeIds = $this->facGrpTypeIds;
    $facGrpAllocStatusIds = $this->facGrpAllocStatusIds;

    $requiredColumns = array('facility_group' => NULL,
                             'facility_group_type' => 'facGrpTypeIds',
                             'facility_group_allocation_status' => 'facGrpAllocStatusIds'
                            );

    // Check for valid facility group name, group type, and group allocation status.
    // Meanwhile also build the $rawFacilityGroup array for later use.
    foreach ($this->importData AS $rowId => $rowData)
    {
      // used at the end to determine whether to continue processing this record
      $err = FALSE;

      // loop through each of the required columns and validate it
      foreach ($requiredColumns as $column => $validator)
      {
        if (!isset($rowData['_rawData'][$column]))
        {
          $errMsg = 'Required column ' . $column . ' is missing from record id ' . $rowId . '.';
          $err = TRUE;
        }
        else
        {
          if (empty($validator))
          {
            if ($column == 'facility_group')
            {
              $rawFacilityGroups[$rowId] = strtolower($rowData['_rawData'][$column]);
            }
            else if ($column == 'facility_group_activation_sequence')
            {
              if (!ctype_digit($rowData['_rawData'][$column]))
              {
                $errMsg = 'Invalid ' . $column . ' "' . $rowData['_rawData'][$column] . '" given for ' .
                        'record id ' . $rowId . '.';
                $err = TRUE;
              }
            }
          }
          else
          {
            // Valid all columns that references against the db values.
            if (!isset(${$validator}[strtolower($rowData['_rawData'][$column])]))
            {
              $errMsg = 'Invalid ' . $column . ' "' . $rowData['_rawData'][$column] . '" given for ' .
                      'record id ' . $rowId . '.';
              $err = TRUE;
            }
          }
        }

        // oh, poo!
        if ($err)
        {
          // log our error either way
          $this->eh->logErr($errMsg);

          unset($rawFacilityGroups[$rowId]);

          // Throw error immediately if any of the required columns are not provided.
          throw new Exception($errMsg);
        }
      }
    }

    // Get the duplicate scenario facility group name now before the foreach loop.
    $facGrpCount = array_count_values($rawFacilityGroups);
    $uniqRawFacGrps = array();

    foreach ($rawFacilityGroups AS $rowId => $facGrp)
    {
      // find the position of the element or return false
      $pos = array_search($facGrp, $uniqRawFacGrps);

      // need to be really strict here because we don't want any [0] positions throwing us
      if ($pos === FALSE)
      {
        // add it to our unique array
        $uniqRawFacGrps[] = $facGrp;

        // the the most recently inserted key
        $pos = max(array_keys($uniqRawFacGrps));
      }

      // Point $rawFacGrp to the position in $uniqRawFacGrp.
      $rawFacilityGroups[$rowId] = $pos;
    }

    $qry = agDoctrineQuery::create($conn)
      ->from('agScenarioFacilityGroup sfg')
      ->where('scenario_id = ?', $this->scenarioId)
      ->andWhereIn('scenario_facility_group', $uniqRawFacGrps);
    $qryString = $qry->getSqlQuery();
    $facilityGroupColl = $qry->execute();

    $this->eh->logDebug('{' . count($facilityGroupColl) . '} scenario facility group entities found in the database.');

    // update our row keys array
    $this->eh->logDebug('Updating primary keys for found entities.');

    // Add facility group id to $this->importData['primaryKeys'].
    // While we're looping the existing facility, make facility resource updates where necessary.
    foreach ($facilityGroupColl AS $facGrp)
    {
      $facGrpName = strtolower($facGrp['scenario_facility_group']);
      $posId = array_search($facGrpName, $uniqRawFacGrps);

      // Scenario facility group can be associated to multiple facility resources.  In this case,
      // the same scenario facility group will be listed multiple times, each associated to a
      // different facility resources, in the spreadsheet.  We will then need to assign the
      // scenario facility group id to each of the import data row's primary key.
      for ($i = 1; $i <= $facGrpCount[$facGrpName]; $i++)
      {
        $rowId = array_search($posId, $rawFacilityGroups);

        $rowData = & $this->importData[$rowId];
        $rowData['primaryKeys']['scenario_facility_group_id'] = $facGrp['id'];
        $facGrpTypeId = $facGrpTypeIds[strtolower($rowData['_rawData']['facility_group_type'])];
        $facGrpAllocStatusId = $facGrpAllocStatusIds[strtolower($rowData['_rawData']['facility_group_allocation_status'])];
        $facGrp['facility_group_type_id'] = $facGrpTypeId;
        $facGrp['facility_group_allocation_status_id'] = $facGrpAllocStatusId;
        if (isset($rowData['_rawData']['facility_group_activation_sequence'])) {
          $facGrp['activation_sequence'] = $rowData['_rawData']['facility_group_activation_sequence'];
        }
        unset($rawFacilityGroups[$rowId]);
        unset($rowData);
      }
      unset($uniqRawFacGrps[$posId]);
    }
    $facilityGroupColl->save($conn);
    $facilityGroupColl->free(TRUE);
    unset($facilityGroupColl);

    // Create new scenario facility group using rowId as the key to $facilityGroupColl.
    $facilityGroupColl = new Doctrine_Collection('agScenarioFacilityGroup');
    $facilityGroupTable = $conn->getTable('agScenarioFacilityGroup');
    $missingSequence = 0;
    foreach ($uniqRawFacGrps AS $posId => $facGrp)
    {
      $facilityGroup = new agScenarioFacilityGroup($facilityGroupTable, TRUE);
      $facilityGroup['scenario_id'] = $this->scenarioId;
      for($i=1; $i <= $facGrpCount[$facGrp]; $i++)
      {
        $rowId = array_search($posId, $rawFacilityGroups);
        $rowData = $this->importData[$rowId];
        $facGrpTypeId = $facGrpTypeIds[strtolower($rowData['_rawData']['facility_group_type'])];
        $facGrpAllocStatusId = $facGrpAllocStatusIds[strtolower($rowData['_rawData']['facility_group_allocation_status'])];
        $facilityGroup['scenario_facility_group'] = $rowData['_rawData']['facility_group'];
        $facilityGroup['facility_group_type_id'] = $facGrpTypeId;
        $facilityGroup['facility_group_allocation_status_id'] = $facGrpAllocStatusId;
        if (isset($rowData['_rawData']['facility_group_activation_sequence'])) {
          $facilityGroup['activation_sequence'] = $rowData['_rawData']['facility_group_activation_sequence'];
        } else {
          $facilityGroup['activation_sequence'] = $this->defaultFacilityGroupActivationSequence;
          $missingSequence++;
        }
      }
      $facilityGroupColl->add($facilityGroup, $posId);
      unset($uniqRawFacGrps[$posId]);
    }
    if ($missingSequence > 0) {
      $this->eh->logWarning($missingSequence . ' facility groups missing a decalred activation ' .
        'sequence. Used default sequence `' . $this->defaultFacilityGroupActivationSequence .
        '` instead.');
    }
    $facilityGroupColl->save($conn);

    // Store new scenario facility group ids to $this->importData['primaryKeys'].
    foreach($rawFacilityGroups AS $rowId => $posId)
    {
      $rowData =& $this->importData[$rowId];
      $rowData['primaryKeys']['scenario_facility_group_id'] = $facilityGroupColl[$posId]['id'];
      unset($rawFacilityGroups[$rowId]);
    }
    $facilityGroupColl->free(TRUE);
    unset($facilityGroupColl);
  }

  /**
   * Method to check for required columns and to set / create new scenario facility resources.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setScenarioFacilityResources($throwOnError, Doctrine_Connection $conn)
  {
    $facRscAllocStatusIds = $this->facRscAllocStatusIds;

    // activation_sequence is a required field but does not reference to any other tables, thus,
    // pointing to NULL value.
    $requiredColumns = array('facility_allocation_status' => 'facRscAllocStatusIds'
                            );

    // Check for valid facility resource type abbr and facility resource status provided.
    // Meanwhile also build the $rawFacilityResources array for later use.
    foreach ($this->importData AS $rowId => $rowData)
    {
      // used at the end to determine whether to continue processing this record
      $err = FALSE;

      // loop through each of the required columns and validate it
      foreach ($requiredColumns as $column => $validator)
      {
        if (!isset($rowData['_rawData'][$column]))
        {
          $errMsg = 'Required column ' . $column . ' is missing from record id ' . $rowId . '.';
          $err = TRUE;
        }
        else
        {
          if (empty($validator))
          {
            // Make sure the column's value is a positive integer.
            $value = $rowData['_rawData'][$column];
            if (!ctype_digit($value))
            {
              if (!isset(${$validator}[strtolower($rowData['_rawData'][$column])]))
              {
                $errMsg = 'Invalid ' . $column . ' "' . $rowData['_rawData'][$column] . '" given for ' .
                        'record id ' . $rowId . '.';
                $err = TRUE;
              }
            }
          }
          else
          {
            // Validate all table referencing columns against the db values.
            if (!isset(${$validator}[strtolower($rowData['_rawData'][$column])]))
            {
              $errMsg = 'Invalid ' . $column . ' "' . $rowData['_rawData'][$column] . '" given for ' .
                      'record id ' . $rowId . '.';
              $err = TRUE;
            }
          }
        }

        // oh, poo!
        if ($err)
        {
          // log our error either way
          $this->eh->logErr($errMsg);

          unset($rawScenarioFacilityResources[$rowId]);

          // Throw error immediately if any of the required columns are not provided.
          throw new Exception($errMsg);
        }
      }
      $facRscId = $rowData['primaryKeys']['facility_resource_id'];
      $scenFacGrpId = $rowData['primaryKeys']['scenario_facility_group_id'];
      $rawScenarioFacilityResources[$rowId] = array('facility_resource_id' => $facRscId,
                                                    'scenario_facility_group_id' => $scenFacGrpId
                                                   );
    }

    // Query for related scenario facility resource in a doctrine collection.
    $qry = agDoctrineQuery::create($conn)
                    ->from('agScenarioFacilityResource sfr');

    $firstWhereClause = TRUE;
    foreach ($rawScenarioFacilityResources AS $rowId => $scenFacRscInfo)
    {
      $rowData = $this->importData[$rowId];
      $facRscAllocStatus = strtolower($rowData['_rawData']['facility_allocation_status']);
      $facilityResourceId = $rawScenarioFacilityResources[$rowId]['facility_resource_id'];
      $scenarioFacilityGroupId = $rawScenarioFacilityResources[$rowId]['scenario_facility_group_id'];

      if ($firstWhereClause)
      {
        $qry->where('(sfr.facility_resource_id = ? AND sfr.scenario_facility_group_id = ?)',
                    array($facilityResourceId, $scenarioFacilityGroupId));
        $firstWhereClause = FALSE;
      }
      else
      {
        $qry->orWhere('(sfr.facility_resource_id = ? AND sfr.scenario_facility_group_id = ?)',
                      array($facilityResourceId, $scenarioFacilityGroupId));
      }
    }
    $scenarioFacilityResourceColl = $qry->execute();

    $this->eh->logDebug('{' . count($scenarioFacilityResourceColl) . '} facility entities found in the database.');

    // update our row keys array
    $this->eh->logDebug('Updating primary keys for found entities.');

    // Add scenario facility resource id to $this->importData['primaryKeys'].
    // While we're looping the existing scenario facility resource, make scenario facility resource
    // updates where necessary.
    foreach ($scenarioFacilityResourceColl AS $scenFacRsc)
    {
      $currScenFacRsc = array('facility_resource_id' => $scenFacRsc['facility_resource_id'],
                              'scenario_facility_group_id' => $scenFacRsc['scenario_facility_group_id']);
      $rowId = array_search($currScenFacRsc, $rawScenarioFacilityResources);

      $rowData = & $this->importData[$rowId];
      $rowData['primaryKeys']['scenario_facility_resource_id'] = $scenFacRsc['id'];
      $facRscAllocStatusId = $facRscAllocStatusIds[strtolower($rowData['_rawData']['facility_allocation_status'])];
      $scenFacRsc['facility_resource_allocation_status_id'] = $facRscAllocStatusId;
      if (isset($rowData['_rawData']['facility_activation_sequence'])) {
        $scenFacRsc['activation_sequence'] = $rowData['_rawData']['facility_activation_sequence'];
      }
      unset($rawScenarioFacilityResources[$rowId]);
      unset($rowData);
    }
    $scenarioFacilityResourceColl->save($conn);
    $scenarioFacilityResourceColl->free(TRUE);
    unset($scenarioFacilityResourceColl);

    // Create new scenario facility resource using rowId as the key to $scenarioFacilityResourceColl.
    $scenarioFacilityResourceColl = new Doctrine_Collection('agScenarioFacilityResource');
    $scenarioFacilityResourceTable = $conn->getTable('agScenarioFacilityResource');
    $missingSequence = 0;
    foreach ($rawScenarioFacilityResources AS $rowId => $rawScenFacResc)
    {
      $rowData = $this->importData[$rowId];
      $scenFacResource = new agScenarioFacilityResource($scenarioFacilityResourceTable, TRUE);
      $scenFacResource['facility_resource_id'] = $rowData['primaryKeys']['facility_resource_id'];
      $scenFacResource['scenario_facility_group_id'] = $rowData['primaryKeys']['scenario_facility_group_id'];
      $facRscAllocstatusId = $facRscAllocStatusIds[strtolower($rowData['_rawData']['facility_allocation_status'])];
      $scenFacResource['facility_resource_allocation_status_id'] = $facRscAllocstatusId;
      if (isset($rowData['_rawData']['facility_activation_sequence'])) {
        $facResource['activation_sequence'] = $rowData['_rawData']['facility_activation_sequence'];
      } else {
        $facResource['activation_sequence'] = $this->defaultFacilityResourceActivationSequence;
        $missingSequence++;
      }
      $scenarioFacilityResourceColl->add($scenFacResource, $rowId);
      unset($rawScenarioFacilityResources[$rowId]);
    }
    if ($missingSequence > 0) {
      $this->eh->logWarning($missingSequence . ' facility resources missing a decalred activation ' .
        'sequence. Used default sequence `' . $this->defaultFacilityResourceActivationSequence .
        '` instead.');
    }
    
    $scenarioFacilityResourceColl->save($conn);

    // Store new scenario facility resource ids to $this->importData['primaryKeys'].
    foreach ($this->importData AS $rowId => &$rowData)
    {
      if (!isset($rowData['primaryKeys']['scenario_facility_resource_id']))
      {
        $rowData['primaryKeys']['scenario_facility_resource_id'] = $scenarioFacilityResourceColl[$rowId]['id'];
      }
    }
    $scenarioFacilityResourceColl->free(TRUE);
    unset($scenarioFacilityResourceColl);

  }

  /**
   * Method to set facility resource requirements
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   * @param Doctrine_Connection $conn A doctrine connection for data manipulation.
   */
  protected function setResourceRequirements($throwOnError, Doctrine_Connection $conn)
  {
    $err = FALSE;

    if (empty($this->staffResourceTypes)) {
      $this->staffResourceTypes = agDoctrineQuery::create()
      ->select('srt.id')
          ->addSelect('srt.staff_resource_type_abbr')
        ->from('agStaffResourceType srt')
      ->execute(array(),agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
      foreach ($this->staffResourceTypes as $id => &$abbr) {
        $abbr = $this->cleanColumnName($abbr);
        $minSpec = $abbr . '_min';
        $maxSpec = $abbr . '_max';

        if (isset($this->importSpec[$minSpec]) && !isset($this->importSpec[$maxSpec])) {
          $errMsg = 'Missing paired column ' . $maxSpec . ' from import file.';
          $err = TRUE;
        } elseif (!isset($this->importSpec[$minSpec]) && isset($this->importSpec[$maxSpec])) {
          $errMsg = 'Missing paired column ' . $minSpec . ' from import file.';
          $err = TRUE;
        }

        if ($err) {
          $this->eh->logErr($errMsg);

          if ($throwOnError) {
            throw new Exception($errMsg);
          }
        }
      }
      unset($abbr);
      $this->staffResourceTypes = array_flip($this->staffResourceTypes);
    }

    $err = FALSE;
    $missingColumn = 0;
    $negativeNum = 0;

    // try to avoid re-querying where possible
    if (empty($this->staffResourceTypes)) {
      $this->staffResourceTypes = agDoctrineQuery::create()
        ->select('srt.staff_resource_type_abbr')
            ->addSelect('srt.id')
          ->from('agStaffResourceType srt')
      ->execute(array(),agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
      
        foreach ($this->staffResourceTypes as $abbr => $id) {
        $minSpec = $abbr . '_min';
        $maxSpec = $abbr . '_max';

        if (isset($this->importSpec[$minSpec]) && !isset($this->importSpec[$maxSpec])) {
          $errMsg = 'Missing paired column ' . $maxSpec . ' from import file.';
          $err = TRUE;
        } elseif (!isset($this->importSpec[$minSpec]) && isset($this->importSpec[$maxSpec])) {
          $errMsg = 'Missing paired column ' . $minSpec . ' from import file.';
          $err = TRUE;
        }

        if ($err) {
          $this->eh->logErr($errMsg);

          if ($throwOnError) {
            throw new Exception($errMsg);
          }
        }
      }
    }

    $defaultStaffResourceTypesTable = $conn->getTable('agDefaultScenarioStaffResourceType');
    $defaultStaffResourceTypes = agDoctrineQuery::create($conn)
      ->select('dssrt.*')
        ->from('agDefaultScenarioStaffResourceType dssrt INDEXBY dssrt.staff_resource_type_id')
        ->where('dssrt.scenario_id = ?', $this->scenarioId)
        ->execute(array());

    $rscReqs = array();
    $uniqResourceTypes = array();
    foreach ($this->importData as $rowId => $rowData) {
      $rawData = $rowData['_rawData'];
      $scFacRscId = $rowData['primaryKeys']['scenario_facility_resource_id'];

      $tmpRscReqs = array();
      foreach ($this->staffResourceTypes as $abbr => $id ) {
        $minSpec = $abbr . '_min';
        $maxSpec = $abbr . '_max';

        if (isset($rawData[$minSpec]) && isset($rawData[$maxSpec])) {
          $min = $rawData[$minSpec];
          $max = $rawData[$maxSpec];

          if (ctype_digit($min) && ctype_digit($max) && $min <= $max) {
            $tmpRscReqs[$abbr] = array($min, $max);
            $uniqResourceTypes[$abbr] = $id;
          } else {
            $negativeNum++;
          }

        } elseif (isset($rawData[$minSpec]) || isset($rawData[$maxSpec])) {
          $missingColumn++;
        }
      }
      $rscReqs[$scFacRscId] = $tmpRscReqs;
    }

    // ie: $rscReqs[$rowId] = array('opr' => array(2,15), 'ecm' => array(3, 39))

    $facilityStaffResources = agDoctrineQuery::create($conn)
      ->select('fsr.*')
        ->from('agFacilityStaffResource fsr')
        ->whereIn('fsr.scenario_facility_resource_id', array_keys($rscReqs))
        ->execute(array());
    $facilityStaffResourceTable = $facilityStaffResources->getTable();
    $facilityStaffResourceTable->setConnection($conn);

    foreach ($facilityStaffResources as $index => $facilityStaffResource) {
      $scFacRscId = $facilityStaffResource['scenario_facility_resource_id'];

      foreach($rscReqs[$scFacRscId] as $abbr => $values) {
        $staffRscTypeId = $this->staffResourceTypes[$abbr];
        if ($facilityStaffResource['staff_resource_type_id'] == $staffRscTypeId) {
          if ($values[0] == 0 && $values[1] == 0) {
            $facilityStaffResources->remove($index);
          } else {
            $facilityStaffResource['minimum_staff'] = $values[0];
            $facilityStaffResource['maximum_staff'] = $values[1];
          }
        }
        unset($rscReqs[$scFacRscId][$abbr]);
      }
    }
    $facilityStaffResources->save($conn);
    $facilityStaffResources->free(TRUE);
    unset($facilityStaffResource);
    unset($facilityStaffResources);

    $facilityStaffResources = new Doctrine_Collection('agFacilityStaffResource');
    foreach ($rscReqs as $scFacRscId => $rscReq) {
      foreach ($rscReq as $abbr => $values) {
        if ($values[0] == 0 && $values[1] == 0) {
          continue;
        }
        $rscTypeId = $this->staffResourceTypes[$abbr];

        $facilityStaffResource = new agFacilityStaffResource($facilityStaffResourceTable, TRUE);
        $facilityStaffResource['scenario_facility_resource_id'] = $scFacRscId;
        $facilityStaffResource['staff_resource_type_id'] = $rscTypeId;
        $facilityStaffResource['minimum_staff'] = $values[0];
        $facilityStaffResource['maximum_staff'] = $values[1];
        $facilityStaffResources->add($facilityStaffResource);
      }
      unset($rscReqs[$scFacRscId]);
    }
    $facilityStaffResources->save($conn);
    $facilityStaffResources->free(TRUE);
    unset($facilityStaffResource);
    unset($facilityStaffResources);

    foreach ($uniqResourceTypes as $abbr => $rscTypeId) {
      if (!isset($defaultStaffResourceTypes[$rscTypeId])) {
        $defaultStaffResourceType = new agDefaultScenarioStaffResourceType($defaultStaffResourceTypesTable, TRUE);
        $defaultStaffResourceType['scenario_id'] = $this->scenarioId;
        $defaultStaffResourceType['staff_resource_type_id'] = $rscTypeId;
        $defaultStaffResourceTypes->add($defaultStaffResourceType, $rscTypeId);
      }
    }

    $defaultStaffResourceTypes->save($conn);
    $defaultStaffResourceTypes->free(TRUE);
    unset($defaultStaffResourceType);
    unset($defaultStaffResourceTypes);
    
    if ($missingColumn > 0) {
      $warnMsg = $missingColumn . ' cells missing paired min/max data. Please review your staff ' .
        'resource requirements to ensure data consistency.';
      $this->eh->logWarning($warnMsg);
    }
    if ($negativeNum > 0) {
      $warnMsg = $negativeNum . ' cells with negative resource requirements or switched min/max ' .
      'values Please review your staff resource requirements to ensure data consistency.';
      $this->eh->logWarning($warnMsg);
    }
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
    $importEmailTypes = array('work_email' => 'work');
    $entityEmails = array();
    $results = array();

    // let's get ahold of our helper object since we're going to use him/her a lot
    $eeh = & $this->helperObjects['agEntityEmailHelper'];

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
      if (isset($rowData['primaryKeys']['entity_id']))
      {
        // this just makes it easier to use
        $entityId = $rowData['primaryKeys']['entity_id'];

        // we start with an empty array, devoid of types in case the entity has no types/values
        $entityEmails[$entityId] = array();
        foreach ($importEmailTypes as $emailType => $emailTypeId)
        {
          if (isset($rowData['_rawData'][$emailType]))
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
    $importPhoneTypes = array('work_phone' => 'work');
    $entityPhones = array();
    $results = array();

    // let's get ahold of our helper object since we're going to use him/her a lot
    $eph = & $this->helperObjects['agEntityPhoneHelper'];

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
      if (isset($rowData['primaryKeys']['entity_id']))
      {
        // this just makes it easier to use
        $entityId = $rowData['primaryKeys']['entity_id'];

        // we start with an empty array, devoid of types in case the entity has no types/values
        $entityPhones[$entityId] = array();
        foreach ($importPhoneTypes as $phoneType => $phoneTypeId)
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
    $importAddressTypes = array('work' => 'work');
    $importAddressElements = array('street_1' => 'line 1', 'street_2' => 'line 2', 'city' => 'city',
        'state' => 'state', 'postal_code' => 'zip5', 'borough' => 'borough', 'country' => 'country');
    $entityAddresses = array();
    $missingGeo = 0;
    $results = array();
    $errMsg = NULL;

    // let's get ahold of our helper object since we're going to use him/her a lot
    $eah = & $this->helperObjects['agEntityAddressHelper'];

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

    // get our address standards and geo match scores
    $addressStandard = agGlobal::getParam('staff_import_address_standard');
    $addressStandardId = agAddressHelper::getAddressStandardIds(array($addressStandard));
    $addressStandardId = $addressStandardId[$addressStandard];
    $geoMatchScore = agGlobal::getParam('default_geo_match_score');
    $geoMatchScoreId = agGeoHelper::getGeoMatchScoreIds(array($geoMatchScore));
    $geoMatchScoreId = $geoMatchScoreId[$geoMatchScore];

    // loop through our raw data and build our entity address data
    foreach ($this->importData as $rowId => $rowData)
    {
      // this just makes it easier to use
      $entityId = $rowData['primaryKeys']['entity_id'];
      $rawData = $rowData['_rawData'];

      // we start with an empty array, devoid of types in case the entity has no types/values
      $workAddr = array();
      foreach ($importAddressElements AS $element => $id)
      {
        if (isset($rawData[$element])) {
          $workAddr[$id] = $rawData[$element];
        }
      }

      if (!isset($rawData['latitude']) || !isset($rawData['longitude']))
      {
        // log our error or at least grab our counter
        $missingGeo++;
        $errMsg = sprintf('Missing work address/geo information from record id  %d', $rowId);
        if ($throwOnError)
        {
          $this->eh->logErr($errMsg);
          throw new Exception($errMsg);
        }

        $this->eh->logWarning($errMsg);
        continue;
      }

      // everything is hunky-dory so we can add this to our array to be passed to the helper (which is especially hunky)
      $workAddrComp = array($workAddr, $addressStandardId);
      $workAddrComp[] = array(array(array($rawData['latitude'], $rawData['longitude'])),
        $geoMatchScoreId);
      $entityAddresses[$entityId][] = array($importAddressTypes['work'], $workAddrComp);
    }

    // pick up some of our components / objects
    $keepHistory = FALSE;
    $enforceStrict = agGlobal::getParam('enforce_strict_contact_formatting');

    if (!isset($this->geoSourceId))
    {
      $this->geoSourceId = agDoctrineQuery::create()
                      ->select('gs.id')
                      ->from('agGeoSource gs')
                      ->where('gs.geo_source = ?', agGlobal::getParam('facility_import_geo_source'))
                      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    }
    $geoSourceId = $this->geoSourceId;

    $addressGeo = array();
    // @TODO Handle geo upserts along with address.
    // execute the helper and finish
    $results = $eah->setEntityAddress($entityAddresses, $geoSourceId, $keepHistory, $enforceStrict,
                    $throwOnError, $conn);
    unset($entityAddresses);

    if ($missingGeo > 0)
    {
      $warnMsg = 'Batch contains ' . $missingGeo . ' addresses without associated geo information.';
      $this->eh->logWarning($warnMsg);
    }
    // @todo do your results reporting here
  }

}
