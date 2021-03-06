<?php

/**
 * BaseagEventFacilityGroup
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $event_id
 * @property string $event_facility_group
 * @property integer $facility_group_type_id
 * @property integer $activation_sequence
 * @property agEvent $agEvent
 * @property agFacilityGroupType $agFacilityGroupType
 * @property Doctrine_Collection $agFacilityResource
 * @property Doctrine_Collection $agEventFacilityDistribution
 * @property Doctrine_Collection $agEventFacilityResource
 * @property Doctrine_Collection $agEventFacilityGroupStatus
 * 
 * @method integer              getId()                          Returns the current record's "id" value
 * @method integer              getEventId()                     Returns the current record's "event_id" value
 * @method string               getEventFacilityGroup()          Returns the current record's "event_facility_group" value
 * @method integer              getFacilityGroupTypeId()         Returns the current record's "facility_group_type_id" value
 * @method integer              getActivationSequence()          Returns the current record's "activation_sequence" value
 * @method agEvent              getAgEvent()                     Returns the current record's "agEvent" value
 * @method agFacilityGroupType  getAgFacilityGroupType()         Returns the current record's "agFacilityGroupType" value
 * @method Doctrine_Collection  getAgFacilityResource()          Returns the current record's "agFacilityResource" collection
 * @method Doctrine_Collection  getAgEventFacilityDistribution() Returns the current record's "agEventFacilityDistribution" collection
 * @method Doctrine_Collection  getAgEventFacilityResource()     Returns the current record's "agEventFacilityResource" collection
 * @method Doctrine_Collection  getAgEventFacilityGroupStatus()  Returns the current record's "agEventFacilityGroupStatus" collection
 * @method agEventFacilityGroup setId()                          Sets the current record's "id" value
 * @method agEventFacilityGroup setEventId()                     Sets the current record's "event_id" value
 * @method agEventFacilityGroup setEventFacilityGroup()          Sets the current record's "event_facility_group" value
 * @method agEventFacilityGroup setFacilityGroupTypeId()         Sets the current record's "facility_group_type_id" value
 * @method agEventFacilityGroup setActivationSequence()          Sets the current record's "activation_sequence" value
 * @method agEventFacilityGroup setAgEvent()                     Sets the current record's "agEvent" value
 * @method agEventFacilityGroup setAgFacilityGroupType()         Sets the current record's "agFacilityGroupType" value
 * @method agEventFacilityGroup setAgFacilityResource()          Sets the current record's "agFacilityResource" collection
 * @method agEventFacilityGroup setAgEventFacilityDistribution() Sets the current record's "agEventFacilityDistribution" collection
 * @method agEventFacilityGroup setAgEventFacilityResource()     Sets the current record's "agEventFacilityResource" collection
 * @method agEventFacilityGroup setAgEventFacilityGroupStatus()  Sets the current record's "agEventFacilityGroupStatus" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEventFacilityGroup extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_event_facility_group');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('event_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('event_facility_group', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('facility_group_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('activation_sequence', 'integer', 1, array(
             'default' => 100,
             'unsigned' => true,
             'type' => 'integer',
             'notnull' => true,
             'length' => 1,
             ));


        $this->index('UX_agEventFacilityGroup', array(
             'fields' => 
             array(
              0 => 'event_id',
              1 => 'event_facility_group',
             ),
             'type' => 'unique',
             ));
        $this->index('IX_agEventFacilityGroup_eventFacilityGroup', array(
             'fields' => 
             array(
              0 => 'event_facility_group',
             ),
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agEvent', array(
             'local' => 'event_id',
             'foreign' => 'id'));

        $this->hasOne('agFacilityGroupType', array(
             'local' => 'facility_group_type_id',
             'foreign' => 'id'));

        $this->hasMany('agFacilityResource', array(
             'refClass' => 'agEventFacilityResource',
             'local' => 'event_facility_group_id',
             'foreign' => 'facility_resource_id'));

        $this->hasMany('agEventFacilityDistribution', array(
             'local' => 'id',
             'foreign' => 'event_facility_group_id'));

        $this->hasMany('agEventFacilityResource', array(
             'local' => 'id',
             'foreign' => 'event_facility_group_id'));

        $this->hasMany('agEventFacilityGroupStatus', array(
             'local' => 'id',
             'foreign' => 'event_facility_group_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}