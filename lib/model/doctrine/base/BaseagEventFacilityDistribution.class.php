<?php

/**
 * BaseagEventFacilityDistribution
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $event_service_area_id
 * @property integer $event_facility_group_id
 * @property agEventServiceArea $agEventServiceArea
 * @property agEventFacilityGroup $agEventFacilityGroup
 * 
 * @method integer                     getId()                      Returns the current record's "id" value
 * @method integer                     getEventServiceAreaId()      Returns the current record's "event_service_area_id" value
 * @method integer                     getEventFacilityGroupId()    Returns the current record's "event_facility_group_id" value
 * @method agEventServiceArea          getAgEventServiceArea()      Returns the current record's "agEventServiceArea" value
 * @method agEventFacilityGroup        getAgEventFacilityGroup()    Returns the current record's "agEventFacilityGroup" value
 * @method agEventFacilityDistribution setId()                      Sets the current record's "id" value
 * @method agEventFacilityDistribution setEventServiceAreaId()      Sets the current record's "event_service_area_id" value
 * @method agEventFacilityDistribution setEventFacilityGroupId()    Sets the current record's "event_facility_group_id" value
 * @method agEventFacilityDistribution setAgEventServiceArea()      Sets the current record's "agEventServiceArea" value
 * @method agEventFacilityDistribution setAgEventFacilityGroup()    Sets the current record's "agEventFacilityGroup" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEventFacilityDistribution extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_event_facility_distribution');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('event_service_area_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('event_facility_group_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));


        $this->index('UX_agEventFacilityDistribution', array(
             'fields' => 
             array(
              0 => 'event_service_area_id',
              1 => 'event_facility_group_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agEventServiceArea', array(
             'local' => 'event_service_area_id',
             'foreign' => 'id'));

        $this->hasOne('agEventFacilityGroup', array(
             'local' => 'event_facility_group_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}