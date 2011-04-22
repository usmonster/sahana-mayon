<?php

/**
 * Normalizing import data for Staff
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
  protected   $scenarioId;

  function __construct($importTable)
  {
    parent::__construct();
    $this->setImportComponents();
    // declare variables
//    $this->importTable = $importTable;
//    $this->defineStatusTypes();

  }

  protected function setImportQuery()
  {
    $this->importQuery = 'SELECT * FROM ' . $this->importTable;
  }

  /**
   * @todo This data should belong in a configuration file (eg, YML)
   */
  protected function setImportComponents()
  {
    // array( [order] => array(componentName => component name, helperClassName => Name of the helper class, throwOnError => boolean, methodName => method name) )
    // setEntity creates entity, person, and staff records.
    $this->importComponents[] = array( 'component' => 'entity', 'throwOnError' => TRUE, 'method' => 'setEntities');
#    $this->importComponents[] = array( 'component' => 'personName', 'throwOnError' => TRUE, 'method' => 'setPersonName', 'helperClassName' => 'agPersonNameHelper');
#    $this->importComponents[] = array( 'component' => 'email', 'throwOnError' => TRUE, 'method' => 'setEntityEmail', 'helperClassName' => 'agEntityEmailHelper');
  }

  /**
   * Method to set / create new entities, persons, and staff.
   * @param boolean $throwOnError Parameter sometimes used by import normalization methods to
   * control whether or not errors will be thrown.
   */
  protected function setEntities($throwOnError)
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
    // loop our import data and pick up any existing entity Ids
    foreach ($this->importData as $rowId => $rowData)
    {
      $rawData = $rowData['_rawData'];

      if(! is_null($rawData['entity_id']))
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
    unset($rawEntityIds) ;

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
    foreach ($this->importdata as $rowId => &$rowData)
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

  /*
   * Method to set new entities from our $_rawData.
   */
  protected function setNewEntities()
  {
    // add new entities / persons / staff for records with bad or no entity ids.
    foreach ($this->importdata as $rowId => &$rowData)
    {
      // initially be pessimistic about the actions we'll need to take
      $createNew = FALSE;
      $warnBadEntity = FALSE;

      // pick these up by reference so we can use the pointers more easily
      $rawData =& $rowData['_rawData'];
      $pKeys =& $rowData['primaryKeys'];

      // this should satisfy both NULL entity_ids and ones that didn't make our initial filter
      if (is_null($rawData['entity_id']))
      {
        $createNew = TRUE;
      }
      elseif (! array_key_exists($rawData['entity_id'], $pKeys['entity_id']))
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
        $warnMsg = sprintf("Bad entity id (%s).  Generated a new entityId().",
          $rowData['entity_id'], $newEntityId);
        sfContext::getInstance()->getLogger()->warning($warnMsg) ;
      }
    }
  }

  public function testDataNorm()
  {
    $_rawData = array('entity_id' => '',
                      'first_name' => '',
                      'middle_name' => '',
                      'last_name' => '',
                      'mobile_phone' => '',
                      'home_phone' => '',
                      'home_email' => '',
                      'work_phone' => '',
                      'work_email' => ''
                     );
    $importData = array( '_rawData' => $_rawData, 'primaryKey' => array(), 'success' => 0);
    $this->normalizeData();
  }
}
