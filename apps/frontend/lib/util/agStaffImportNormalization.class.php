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
 * @author Charles Wisniewski, CUY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agStaffImportNormalization extends agImportNormalization
{
  function __construct($importTable)
  {
    // declare variables
    $this->importTable = $importTable;
    $this->defineStatusTypes();

  }

  function __destruct()
  {
//    //drop temp table.
//    $this->conn->export->dropTable($this->sourceTable);
//    $this->conn->close();
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
    $this->importComponents[] = array( 'component' => 'entity', 'throwOnError' => TRUE, 'method' => 'setEntity');
    $this->importComponents[] = array( 'component' => 'personName', 'throwOnError' => TRUE, 'method' => 'setPersonName', 'helperClassName' => 'agPersonNameHelper');
    $this->importComponents[] = array( 'component' => 'email', 'throwOnError' => TRUE, 'method' => 'setEntityEmail', 'helperClassName' => 'agEntityEmailHelper');
  }

  public function setEntity($throwOnError)
  {
    // loop our import data and pick up any existing entity Ids
    foreach ($this->importData as $rowId => $rowData)
    {
      $rawData = $rowData['_rawData'];

      if(! is_null($rawData['entity_id']))
      {
        $rawEntities[] = $rawData['entity_id'] ;
      }
    }

    // build our initial collection
    $q = agDoctrineQuery::create()
    ->select('e.*')
        ->addSelect('p.*')
        ->addSelect('s.*')
      ->from('agEntity e INDEX BY e.id')
        ->innerJoin('e.agPerson p')
        ->leftJoin('p.agStaff s')
      ->whereIn('e.id', $rawEntities) ;
    $coll = $q->execute() ;

    // we no longer need this
    unset($rawEntities) ;

    //loop foreach $coll member
    foreach ($coll as $entityId => $entityData)
    {
      // if staff id doesn't exist yet, make it so
      if (empty($entityData->agPerson[0]->agStaff[0]))
      {
        $newStaff = new agStaff() ;
        $newStaff['person_id'] = $entityData->agPerson[0]['id'] ;
        $entityData->agPerson[0]->agStaff->add($newStaff) ;
      }
    }

    // add new entities / persons / staff
    foreach ($this->importdata['_rawData'] as $rowId => $rowData)
    {
      // thi should satisfy both NULL entity_ids and ones that didn't make our initial filter
      if (! array_key_exists($rowData['entity_id'], $coll))
      {
        $newEntity = new agEntity() ;
        $coll->add($newEntity) ;
        // @todo Shirley, I'm not sure the best way to add this in such a way that we can 'getId'
        // needs a little figuring out. getId() might work, might not. Also look into fromArray()
        // (synchto array is BS, but fromArray isn't)
        $entityId = '' ;
      }
    }

    // commit / save our collection

    // and finally, loop our $coll and add to our $keyData() array in the $importData bit
    
  }

  public static function testCollInsert()
  {
    $q = agDoctrineQuery::create()
    ->select('p.*')
        ->addSelect('s.*')
      ->from('agPerson p INDEXBY p.id')
        ->leftJoin('p.agStaff s')
      ->whereIn('p.id', array(16,17)) ;
    $coll = $q->execute() ;

    foreach ($coll as $personId => $data)
    {
      if (empty($data->agStaff[0]))
      {
        $staff = new agStaff();
        $staff['person_id'] = $personId;
        $data->agStaff->add($staff) ;
      }
      echo 'Yay' ;
    }

    $coll->save() ;

    echo 'Foo' ;
  }
}
