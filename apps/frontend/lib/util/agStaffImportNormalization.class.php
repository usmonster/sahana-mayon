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
//        $rawEntities[] = $rawData['entity_id'];
        $rawEntities[$rawData['entity_id']] = $rowId;
      }
    }

    // build our initial collection
    $q = agDoctrineQuery::create()
    ->select('e.*')
        ->addSelect('p.*')
        ->addSelect('s.*')
      ->from('agEntity e INDEXBY e.id')
        ->innerJoin('e.agPerson p')
        ->leftJoin('p.agStaff s')
      ->whereIn('e.id', array_keys($rawEntities));
    $coll = $q->execute();

    //loop foreach $coll member
    foreach ($coll as $entityId => &$entityData)
    {
      // if staff id doesn't exist yet, make it so
      if (empty($entityData->agPerson[0]->agStaff[0]))
      {
        $newStaff = new agStaff();
        $newStaff['person_id'] = $entityData->agPerson[0]['id'];
        $entityData->agPerson[0]->agStaff->add($newStaff);
      }
      // Unset good entity from rawEntity, so that rawEntity only stores bad records with bad or no
      // entity ids.
      unset($rawEntities[$entityId]);
      $this->totalProcessedRecordCount++;
    }

    // add new entities / persons / staff for records with bad or no entity ids.
    foreach ($this->importdata['_rawData'] as $rowId => $rowData)
    {
      // this should satisfy both NULL entity_ids and ones that didn't make our initial filter
      if (is_null($rowData['entity_id']) || (! array_key_exists($rowData['entity_id'], $coll)))
      {
        $newData = array( 'agPerson' => array( 'agStaff' => array() ) );
        $newEntity = new agEntity();
        $newEntity->fromArray($newData);
        $coll->add($newEntity);
        $newEntityId = $coll->getLast()->getId();
        $this->newEntityCount++;
        $this->totalProcessedRecordCount++;
      }
    }

    // commit / save our collection
    $coll->save();

    // and finally, loop our $coll and add to our $keyData() array in the $importData bit

//    // loop through importData to replace bad entityIds with good entityIds.
//    foreach ($rawEntities AS $invalidEntityId => $rowId)
//    {
////      $replaceEntityId = array_shift($replaceEntityIds);
//      $importData[$rowId]['entity_id'] = $replaceEntityIds[$invalidEntityId];
//      // Log invalid staff's entity id.
//      $logMsg = sprintf("Bad entity id (%s).  Generated a new entityId().", $invalidEntityId, $replaceEntityIds[$invalidEntityId]);
//    }

//        $newEntityId;
//        if (is_null($rowdata['entity_id']))
//        {
//          // Capture new entity id and save to importdata
//        }
//        else
//        {
//          // Log invalid staff's entity id.
//          $logMsg = sprintf("Bad entity id (%s).  Generated a new entityId().",
//                                $rowData['entity_id'], $newEntityId);
//
//        }

    // we no longer need this
    unset($rawEntities) ;
  }

  public static function testCollInsert()
  {
//    $q = agDoctrineQuery::create()
//    ->select('p.*')
//        ->addSelect('s.*')
//      ->from('agPerson p INDEXBY p.id')
//        ->leftJoin('p.agStaff s')
//      ->whereIn('p.id', array(16,17, 18)) ;
//    $coll = $q->execute() ;
//
//    foreach ($coll as $personId => &$data)
//    {
//      if (empty($data->agStaff[0]))
//      {
//        $staffData = array();
//        $staff = new agStaff();
//        $staff->fromArray($staffData);
////        $staff['person_id'] = $personId;
//        $data->agStaff->add($staff) ;
//        $pId = $data->id;
//        $sId = $staff->id;
//        echo "personId: $pId\nstaffId: $sId";
//      }
//      echo 'Yay' ;
//    }
//
//    $coll->save() ;

    $q = agDoctrineQuery::create()
    ->select('e.*')
        ->addSelect('p.*')
        ->addSelect('s.*')
      ->from('agEntity e')
        ->innerJoin('e.agPerson p')
        ->leftJoin('p.agStaff s')
        ->leftJoin('s.agStaffResource sr')
      ->whereIn('e.id');
    $coll2 = $q->execute() ;

//    $newData = array( 'agPerson' => array( 'agStaff' => array() ) );
    $newData = array( 'agPerson' => array( 'agStaff' => array('agStaffResource' => array('agStaffResoruceType' => array('staff_resource_type' => 'UORC'))) ) );
    $newEntity = new agEntity();
    $newEntity->fromArray($newData);
    $coll2->add($newEntity);
    $newEntityId = $coll2->getLast();
    print_r($newEntityId->toArray());
//    echo "\nnewEntityId: $newEntityId";

    $coll2->save();


    echo 'Foo' ;
  }
}
