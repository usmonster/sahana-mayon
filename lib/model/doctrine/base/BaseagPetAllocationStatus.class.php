<?php

/**
 * BaseagPetAllocationStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $pet_id
 * @property timestamp $time_stamp
 * @property integer $event_facility_resource_id
 * @property integer $pet_allocation_status_type_id
 * @property agPet $agPet
 * @property agEventFacilityResource $agEventFacilityResource
 * @property agPetAllocationStatusType $agPetAllocationStatusType
 * 
 * @method integer                   getId()                            Returns the current record's "id" value
 * @method integer                   getPetId()                         Returns the current record's "pet_id" value
 * @method timestamp                 getTimeStamp()                     Returns the current record's "time_stamp" value
 * @method integer                   getEventFacilityResourceId()       Returns the current record's "event_facility_resource_id" value
 * @method integer                   getPetAllocationStatusTypeId()     Returns the current record's "pet_allocation_status_type_id" value
 * @method agPet                     getAgPet()                         Returns the current record's "agPet" value
 * @method agEventFacilityResource   getAgEventFacilityResource()       Returns the current record's "agEventFacilityResource" value
 * @method agPetAllocationStatusType getAgPetAllocationStatusType()     Returns the current record's "agPetAllocationStatusType" value
 * @method agPetAllocationStatus     setId()                            Sets the current record's "id" value
 * @method agPetAllocationStatus     setPetId()                         Sets the current record's "pet_id" value
 * @method agPetAllocationStatus     setTimeStamp()                     Sets the current record's "time_stamp" value
 * @method agPetAllocationStatus     setEventFacilityResourceId()       Sets the current record's "event_facility_resource_id" value
 * @method agPetAllocationStatus     setPetAllocationStatusTypeId()     Sets the current record's "pet_allocation_status_type_id" value
 * @method agPetAllocationStatus     setAgPet()                         Sets the current record's "agPet" value
 * @method agPetAllocationStatus     setAgEventFacilityResource()       Sets the current record's "agEventFacilityResource" value
 * @method agPetAllocationStatus     setAgPetAllocationStatusType()     Sets the current record's "agPetAllocationStatusType" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPetAllocationStatus extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_pet_allocation_status');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('pet_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('time_stamp', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => true,
             ));
        $this->hasColumn('event_facility_resource_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('pet_allocation_status_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));


        $this->index('UX_agPetAllocationStatus', array(
             'fields' => 
             array(
              0 => 'pet_id',
              1 => 'time_stamp',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agPet', array(
             'local' => 'pet_id',
             'foreign' => 'id'));

        $this->hasOne('agEventFacilityResource', array(
             'local' => 'event_facility_resource_id',
             'foreign' => 'id'));

        $this->hasOne('agPetAllocationStatusType', array(
             'local' => 'pet_allocation_status_type_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}