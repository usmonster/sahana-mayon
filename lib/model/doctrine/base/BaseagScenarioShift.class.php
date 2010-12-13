<?php

/**
 * BaseagScenarioShift
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $scenario_facility_resource_id
 * @property integer $staff_resource_type_id
 * @property integer $task_id
 * @property integer $task_length_minutes
 * @property integer $break_length_minutes
 * @property integer $minutes_start_to_facility_activation
 * @property integer $minimum_staff
 * @property integer $maximum_staff
 * @property integer $staff_wave
 * @property integer $shift_status_id
 * @property integer $deployment_algorithm_id
 * @property agStaffResourceType $agStaffResourceType
 * @property agTask $agTask
 * @property agShiftStatus $agShiftStatus
 * @property agDeploymentAlgorithm $agDeploymentAlgorithm
 * @property agScenarioFacilityResource $agScenarioFacilityResource
 * 
 * @method integer                    getId()                                   Returns the current record's "id" value
 * @method integer                    getScenarioFacilityResourceId()           Returns the current record's "scenario_facility_resource_id" value
 * @method integer                    getStaffResourceTypeId()                  Returns the current record's "staff_resource_type_id" value
 * @method integer                    getTaskId()                               Returns the current record's "task_id" value
 * @method integer                    getTaskLengthMinutes()                    Returns the current record's "task_length_minutes" value
 * @method integer                    getBreakLengthMinutes()                   Returns the current record's "break_length_minutes" value
 * @method integer                    getMinutesStartToFacilityActivation()     Returns the current record's "minutes_start_to_facility_activation" value
 * @method integer                    getMinimumStaff()                         Returns the current record's "minimum_staff" value
 * @method integer                    getMaximumStaff()                         Returns the current record's "maximum_staff" value
 * @method integer                    getStaffWave()                            Returns the current record's "staff_wave" value
 * @method integer                    getShiftStatusId()                        Returns the current record's "shift_status_id" value
 * @method integer                    getDeploymentAlgorithmId()                Returns the current record's "deployment_algorithm_id" value
 * @method agStaffResourceType        getAgStaffResourceType()                  Returns the current record's "agStaffResourceType" value
 * @method agTask                     getAgTask()                               Returns the current record's "agTask" value
 * @method agShiftStatus              getAgShiftStatus()                        Returns the current record's "agShiftStatus" value
 * @method agDeploymentAlgorithm      getAgDeploymentAlgorithm()                Returns the current record's "agDeploymentAlgorithm" value
 * @method agScenarioFacilityResource getAgScenarioFacilityResource()           Returns the current record's "agScenarioFacilityResource" value
 * @method agScenarioShift            setId()                                   Sets the current record's "id" value
 * @method agScenarioShift            setScenarioFacilityResourceId()           Sets the current record's "scenario_facility_resource_id" value
 * @method agScenarioShift            setStaffResourceTypeId()                  Sets the current record's "staff_resource_type_id" value
 * @method agScenarioShift            setTaskId()                               Sets the current record's "task_id" value
 * @method agScenarioShift            setTaskLengthMinutes()                    Sets the current record's "task_length_minutes" value
 * @method agScenarioShift            setBreakLengthMinutes()                   Sets the current record's "break_length_minutes" value
 * @method agScenarioShift            setMinutesStartToFacilityActivation()     Sets the current record's "minutes_start_to_facility_activation" value
 * @method agScenarioShift            setMinimumStaff()                         Sets the current record's "minimum_staff" value
 * @method agScenarioShift            setMaximumStaff()                         Sets the current record's "maximum_staff" value
 * @method agScenarioShift            setStaffWave()                            Sets the current record's "staff_wave" value
 * @method agScenarioShift            setShiftStatusId()                        Sets the current record's "shift_status_id" value
 * @method agScenarioShift            setDeploymentAlgorithmId()                Sets the current record's "deployment_algorithm_id" value
 * @method agScenarioShift            setAgStaffResourceType()                  Sets the current record's "agStaffResourceType" value
 * @method agScenarioShift            setAgTask()                               Sets the current record's "agTask" value
 * @method agScenarioShift            setAgShiftStatus()                        Sets the current record's "agShiftStatus" value
 * @method agScenarioShift            setAgDeploymentAlgorithm()                Sets the current record's "agDeploymentAlgorithm" value
 * @method agScenarioShift            setAgScenarioFacilityResource()           Sets the current record's "agScenarioFacilityResource" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagScenarioShift extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_scenario_shift');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('scenario_facility_resource_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('staff_resource_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('task_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
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
        $this->hasColumn('minutes_start_to_facility_activation', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('minimum_staff', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('maximum_staff', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('staff_wave', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('shift_status_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
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

        $this->hasOne('agTask', array(
             'local' => 'task_id',
             'foreign' => 'id'));

        $this->hasOne('agShiftStatus', array(
             'local' => 'shift_status_id',
             'foreign' => 'id'));

        $this->hasOne('agDeploymentAlgorithm', array(
             'local' => 'deployment_algorithm_id',
             'foreign' => 'id'));

        $this->hasOne('agScenarioFacilityResource', array(
             'local' => 'scenario_facility_resource_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}