<?php

/**
 * BaseagEventFacilityGroupStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $event_facility_group_id
 * @property timestamp $time_stamp
 * @property integer $facility_group_allocation_status_id
 * @property agFacilityGroupAllocationStatus $agFacilityGroupAllocationStatus
 * @property agEventFacilityGroup $agEventFacilityGroup
 * 
 * @method integer                         getId()                                  Returns the current record's "id" value
 * @method integer                         getEventFacilityGroupId()                Returns the current record's "event_facility_group_id" value
 * @method timestamp                       getTimeStamp()                           Returns the current record's "time_stamp" value
 * @method integer                         getFacilityGroupAllocationStatusId()     Returns the current record's "facility_group_allocation_status_id" value
 * @method agFacilityGroupAllocationStatus getAgFacilityGroupAllocationStatus()     Returns the current record's "agFacilityGroupAllocationStatus" value
 * @method agEventFacilityGroup            getAgEventFacilityGroup()                Returns the current record's "agEventFacilityGroup" value
 * @method agEventFacilityGroupStatus      setId()                                  Sets the current record's "id" value
 * @method agEventFacilityGroupStatus      setEventFacilityGroupId()                Sets the current record's "event_facility_group_id" value
 * @method agEventFacilityGroupStatus      setTimeStamp()                           Sets the current record's "time_stamp" value
 * @method agEventFacilityGroupStatus      setFacilityGroupAllocationStatusId()     Sets the current record's "facility_group_allocation_status_id" value
 * @method agEventFacilityGroupStatus      setAgFacilityGroupAllocationStatus()     Sets the current record's "agFacilityGroupAllocationStatus" value
 * @method agEventFacilityGroupStatus      setAgEventFacilityGroup()                Sets the current record's "agEventFacilityGroup" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEventFacilityGroupStatus extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_event_facility_group_status');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('event_facility_group_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('time_stamp', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => true,
             ));
        $this->hasColumn('facility_group_allocation_status_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));


        $this->index('UX_agEventFacilityGroupStatus', array(
             'fields' => 
             array(
              0 => 'event_facility_group_id',
              1 => 'time_stamp',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agFacilityGroupAllocationStatus', array(
             'local' => 'facility_group_allocation_status_id',
             'foreign' => 'id'));

        $this->hasOne('agEventFacilityGroup', array(
             'local' => 'event_facility_group_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}