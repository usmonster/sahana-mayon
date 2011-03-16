<?php

/**
 * BaseagEventShift
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $event_facility_resource_id
 * @property integer $staff_resource_type_id
 * @property integer $minimum_staff
 * @property integer $maximum_staff
 * @property integer $minutes_start_to_facility_activation
 * @property integer $task_length_minutes
 * @property integer $break_length_minutes
 * @property integer $task_id
 * @property integer $shift_status_id
 * @property integer $staff_wave
 * @property integer $deployment_algorithm_id
 * @property agStaffResourceType $agStaffResourceType
 * @property agTask $agShiftTask
 * @property agEventFacilityResource $agEventFacilityResource
 * @property agShiftStatus $agShiftStatus
 * @property agDeploymentAlgorithm $agDeploymentAlgorithm
 * @property Doctrine_Collection $agStaffEvent
 * @property Doctrine_Collection $agEventStaffShift
 * 
 * @method integer                 getId()                                   Returns the current record's "id" value
 * @method integer                 getEventFacilityResourceId()              Returns the current record's "event_facility_resource_id" value
 * @method integer                 getStaffResourceTypeId()                  Returns the current record's "staff_resource_type_id" value
 * @method integer                 getMinimumStaff()                         Returns the current record's "minimum_staff" value
 * @method integer                 getMaximumStaff()                         Returns the current record's "maximum_staff" value
 * @method integer                 getMinutesStartToFacilityActivation()     Returns the current record's "minutes_start_to_facility_activation" value
 * @method integer                 getTaskLengthMinutes()                    Returns the current record's "task_length_minutes" value
 * @method integer                 getBreakLengthMinutes()                   Returns the current record's "break_length_minutes" value
 * @method integer                 getTaskId()                               Returns the current record's "task_id" value
 * @method integer                 getShiftStatusId()                        Returns the current record's "shift_status_id" value
 * @method integer                 getStaffWave()                            Returns the current record's "staff_wave" value
 * @method integer                 getDeploymentAlgorithmId()                Returns the current record's "deployment_algorithm_id" value
 * @method agStaffResourceType     getAgStaffResourceType()                  Returns the current record's "agStaffResourceType" value
 * @method agTask                  getAgShiftTask()                          Returns the current record's "agShiftTask" value
 * @method agEventFacilityResource getAgEventFacilityResource()              Returns the current record's "agEventFacilityResource" value
 * @method agShiftStatus           getAgShiftStatus()                        Returns the current record's "agShiftStatus" value
 * @method agDeploymentAlgorithm   getAgDeploymentAlgorithm()                Returns the current record's "agDeploymentAlgorithm" value
 * @method Doctrine_Collection     getAgStaffEvent()                         Returns the current record's "agStaffEvent" collection
 * @method Doctrine_Collection     getAgEventStaffShift()                    Returns the current record's "agEventStaffShift" collection
 * @method agEventShift            setId()                                   Sets the current record's "id" value
 * @method agEventShift            setEventFacilityResourceId()              Sets the current record's "event_facility_resource_id" value
 * @method agEventShift            setStaffResourceTypeId()                  Sets the current record's "staff_resource_type_id" value
 * @method agEventShift            setMinimumStaff()                         Sets the current record's "minimum_staff" value
 * @method agEventShift            setMaximumStaff()                         Sets the current record's "maximum_staff" value
 * @method agEventShift            setMinutesStartToFacilityActivation()     Sets the current record's "minutes_start_to_facility_activation" value
 * @method agEventShift            setTaskLengthMinutes()                    Sets the current record's "task_length_minutes" value
 * @method agEventShift            setBreakLengthMinutes()                   Sets the current record's "break_length_minutes" value
 * @method agEventShift            setTaskId()                               Sets the current record's "task_id" value
 * @method agEventShift            setShiftStatusId()                        Sets the current record's "shift_status_id" value
 * @method agEventShift            setStaffWave()                            Sets the current record's "staff_wave" value
 * @method agEventShift            setDeploymentAlgorithmId()                Sets the current record's "deployment_algorithm_id" value
 * @method agEventShift            setAgStaffResourceType()                  Sets the current record's "agStaffResourceType" value
 * @method agEventShift            setAgShiftTask()                          Sets the current record's "agShiftTask" value
 * @method agEventShift            setAgEventFacilityResource()              Sets the current record's "agEventFacilityResource" value
 * @method agEventShift            setAgShiftStatus()                        Sets the current record's "agShiftStatus" value
 * @method agEventShift            setAgDeploymentAlgorithm()                Sets the current record's "agDeploymentAlgorithm" value
 * @method agEventShift            setAgStaffEvent()                         Sets the current record's "agStaffEvent" collection
 * @method agEventShift            setAgEventStaffShift()                    Sets the current record's "agEventStaffShift" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEventShift extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_event_shift');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('event_facility_resource_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('staff_resource_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('minimum_staff', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 1,
             ));
        $this->hasColumn('maximum_staff', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 1,
             ));
        $this->hasColumn('minutes_start_to_facility_activation', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('task_length_minutes', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('break_length_minutes', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('task_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('shift_status_id', 'integer', 2, array(
             'type' => 'integer',
             'length' => 2,
             ));
        $this->hasColumn('staff_wave', 'integer', 2, array(
             'type' => 'integer',
             'length' => 2,
             ));
        $this->hasColumn('deployment_algorithm_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agStaffResourceType', array(
             'local' => 'staff_resource_type_id',
             'foreign' => 'id'));

        $this->hasOne('agTask as agShiftTask', array(
             'local' => 'task_id',
             'foreign' => 'id'));

        $this->hasOne('agEventFacilityResource', array(
             'local' => 'event_facility_resource_id',
             'foreign' => 'id'));

        $this->hasOne('agShiftStatus', array(
             'local' => 'shift_status_id',
             'foreign' => 'id'));

        $this->hasOne('agDeploymentAlgorithm', array(
             'local' => 'deployment_algorithm_id',
             'foreign' => 'id'));

        $this->hasMany('agEventStaff as agStaffEvent', array(
             'refClass' => 'agEventStaffShift',
             'local' => 'event_shift_id',
             'foreign' => 'event_staff_id'));

        $this->hasMany('agEventStaffShift', array(
             'local' => 'id',
             'foreign' => 'event_shift_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}