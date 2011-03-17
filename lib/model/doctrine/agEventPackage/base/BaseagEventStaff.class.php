<?php

/**
 * BaseagEventStaff
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $event_id
 * @property integer $staff_resource_id
 * @property integer $deployment_weight
 * @property agEvent $agEvent
 * @property agStaffResource $agStaffResource
 * @property Doctrine_Collection $agEventFacilityShift
 * @property Doctrine_Collection $agEventStaffShift
 * @property Doctrine_Collection $agEventStaffRotation
 * @property Doctrine_Collection $agEventStaffStatus
 * @property Doctrine_Collection $agClientNote
 * @property Doctrine_Collection $agPetNote
 * 
 * @method integer             getId()                   Returns the current record's "id" value
 * @method integer             getEventId()              Returns the current record's "event_id" value
 * @method integer             getStaffResourceId()      Returns the current record's "staff_resource_id" value
 * @method integer             getDeploymentWeight()     Returns the current record's "deployment_weight" value
 * @method agEvent             getAgEvent()              Returns the current record's "agEvent" value
 * @method agStaffResource     getAgStaffResource()      Returns the current record's "agStaffResource" value
 * @method Doctrine_Collection getAgEventFacilityShift() Returns the current record's "agEventFacilityShift" collection
 * @method Doctrine_Collection getAgEventStaffShift()    Returns the current record's "agEventStaffShift" collection
 * @method Doctrine_Collection getAgEventStaffRotation() Returns the current record's "agEventStaffRotation" collection
 * @method Doctrine_Collection getAgEventStaffStatus()   Returns the current record's "agEventStaffStatus" collection
 * @method Doctrine_Collection getAgClientNote()         Returns the current record's "agClientNote" collection
 * @method Doctrine_Collection getAgPetNote()            Returns the current record's "agPetNote" collection
 * @method agEventStaff        setId()                   Sets the current record's "id" value
 * @method agEventStaff        setEventId()              Sets the current record's "event_id" value
 * @method agEventStaff        setStaffResourceId()      Sets the current record's "staff_resource_id" value
 * @method agEventStaff        setDeploymentWeight()     Sets the current record's "deployment_weight" value
 * @method agEventStaff        setAgEvent()              Sets the current record's "agEvent" value
 * @method agEventStaff        setAgStaffResource()      Sets the current record's "agStaffResource" value
 * @method agEventStaff        setAgEventFacilityShift() Sets the current record's "agEventFacilityShift" collection
 * @method agEventStaff        setAgEventStaffShift()    Sets the current record's "agEventStaffShift" collection
 * @method agEventStaff        setAgEventStaffRotation() Sets the current record's "agEventStaffRotation" collection
 * @method agEventStaff        setAgEventStaffStatus()   Sets the current record's "agEventStaffStatus" collection
 * @method agEventStaff        setAgClientNote()         Sets the current record's "agClientNote" collection
 * @method agEventStaff        setAgPetNote()            Sets the current record's "agPetNote" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEventStaff extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_event_staff');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('event_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('staff_resource_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('deployment_weight', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 1,
             ));


        $this->index('UX_agEventStaff', array(
             'fields' => 
             array(
              0 => 'event_id',
              1 => 'staff_resource_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agEvent', array(
             'local' => 'event_id',
             'foreign' => 'id'));

        $this->hasOne('agStaffResource', array(
             'local' => 'staff_resource_id',
             'foreign' => 'id'));

        $this->hasMany('agEventShift as agEventFacilityShift', array(
             'refClass' => 'agEventStaffShift',
             'local' => 'event_staff_id',
             'foreign' => 'event_shift_id'));

        $this->hasMany('agEventStaffShift', array(
             'local' => 'id',
             'foreign' => 'event_staff_id'));

        $this->hasMany('agEventStaffRotation', array(
             'local' => 'id',
             'foreign' => 'event_staff_id'));

        $this->hasMany('agEventStaffStatus', array(
             'local' => 'id',
             'foreign' => 'event_staff_id'));

        $this->hasMany('agClientNote', array(
             'local' => 'id',
             'foreign' => 'event_staff_id'));

        $this->hasMany('agPetNote', array(
             'local' => 'id',
             'foreign' => 'event_staff_id'));

        $luceneable0 = new Luceneable(array(
             ));
        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($luceneable0);
        $this->actAs($timestampable0);
    }
}