<?php

/**
 * BaseagEventStaffStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $event_staff_id
 * @property timestamp $time_stamp
 * @property integer $staff_allocation_status_id
 * @property agEventStaff $agEventStaff
 * @property agStaffAllocationStatus $agStaffAllocationStatus
 * 
 * @method integer                 getId()                         Returns the current record's "id" value
 * @method integer                 getEventStaffId()               Returns the current record's "event_staff_id" value
 * @method timestamp               getTimeStamp()                  Returns the current record's "time_stamp" value
 * @method integer                 getStaffAllocationStatusId()    Returns the current record's "staff_allocation_status_id" value
 * @method agEventStaff            getAgEventStaff()               Returns the current record's "agEventStaff" value
 * @method agStaffAllocationStatus getAgStaffAllocationStatus()    Returns the current record's "agStaffAllocationStatus" value
 * @method agEventStaffStatus      setId()                         Sets the current record's "id" value
 * @method agEventStaffStatus      setEventStaffId()               Sets the current record's "event_staff_id" value
 * @method agEventStaffStatus      setTimeStamp()                  Sets the current record's "time_stamp" value
 * @method agEventStaffStatus      setStaffAllocationStatusId()    Sets the current record's "staff_allocation_status_id" value
 * @method agEventStaffStatus      setAgEventStaff()               Sets the current record's "agEventStaff" value
 * @method agEventStaffStatus      setAgStaffAllocationStatus()    Sets the current record's "agStaffAllocationStatus" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEventStaffStatus extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_event_staff_status');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('event_staff_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('time_stamp', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => true,
             ));
        $this->hasColumn('staff_allocation_status_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));


        $this->index('UX_agEventStaffStatus', array(
             'fields' => 
             array(
              0 => 'event_staff_id',
              1 => 'time_stamp',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agEventStaff', array(
             'local' => 'event_staff_id',
             'foreign' => 'id'));

        $this->hasOne('agStaffAllocationStatus', array(
             'local' => 'staff_allocation_status_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}