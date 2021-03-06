<?php

/**
 * BaseagEventFacilityResourceActivationTime
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $event_facility_resource_id
 * @property integer $activation_time
 * @property agEventFacilityResource $agEventFacilityResource
 * 
 * @method integer                               getId()                         Returns the current record's "id" value
 * @method integer                               getEventFacilityResourceId()    Returns the current record's "event_facility_resource_id" value
 * @method integer                               getActivationTime()             Returns the current record's "activation_time" value
 * @method agEventFacilityResource               getAgEventFacilityResource()    Returns the current record's "agEventFacilityResource" value
 * @method agEventFacilityResourceActivationTime setId()                         Sets the current record's "id" value
 * @method agEventFacilityResourceActivationTime setEventFacilityResourceId()    Sets the current record's "event_facility_resource_id" value
 * @method agEventFacilityResourceActivationTime setActivationTime()             Sets the current record's "activation_time" value
 * @method agEventFacilityResourceActivationTime setAgEventFacilityResource()    Sets the current record's "agEventFacilityResource" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEventFacilityResourceActivationTime extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_event_facility_resource_activation_time');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('event_facility_resource_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('activation_time', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));


        $this->index('agEventFacilityResourceActivationTime_unq', array(
             'fields' => 
             array(
              0 => 'event_facility_resource_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agEventFacilityResource', array(
             'local' => 'event_facility_resource_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}