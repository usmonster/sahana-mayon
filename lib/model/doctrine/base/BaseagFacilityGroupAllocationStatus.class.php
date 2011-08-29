<?php

/**
 * BaseagFacilityGroupAllocationStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $facility_group_allocation_status
 * @property string $description
 * @property boolean $allocatable
 * @property boolean $standby
 * @property boolean $active
 * @property boolean $app_display
 * @property Doctrine_Collection $agEventFacilityGroupStatus
 * @property Doctrine_Collection $agScenarioFacilityGroup
 * 
 * @method integer                         getId()                               Returns the current record's "id" value
 * @method string                          getFacilityGroupAllocationStatus()    Returns the current record's "facility_group_allocation_status" value
 * @method string                          getDescription()                      Returns the current record's "description" value
 * @method boolean                         getAllocatable()                      Returns the current record's "allocatable" value
 * @method boolean                         getStandby()                          Returns the current record's "standby" value
 * @method boolean                         getActive()                           Returns the current record's "active" value
 * @method boolean                         getAppDisplay()                       Returns the current record's "app_display" value
 * @method Doctrine_Collection             getAgEventFacilityGroupStatus()       Returns the current record's "agEventFacilityGroupStatus" collection
 * @method Doctrine_Collection             getAgScenarioFacilityGroup()          Returns the current record's "agScenarioFacilityGroup" collection
 * @method agFacilityGroupAllocationStatus setId()                               Sets the current record's "id" value
 * @method agFacilityGroupAllocationStatus setFacilityGroupAllocationStatus()    Sets the current record's "facility_group_allocation_status" value
 * @method agFacilityGroupAllocationStatus setDescription()                      Sets the current record's "description" value
 * @method agFacilityGroupAllocationStatus setAllocatable()                      Sets the current record's "allocatable" value
 * @method agFacilityGroupAllocationStatus setStandby()                          Sets the current record's "standby" value
 * @method agFacilityGroupAllocationStatus setActive()                           Sets the current record's "active" value
 * @method agFacilityGroupAllocationStatus setAppDisplay()                       Sets the current record's "app_display" value
 * @method agFacilityGroupAllocationStatus setAgEventFacilityGroupStatus()       Sets the current record's "agEventFacilityGroupStatus" collection
 * @method agFacilityGroupAllocationStatus setAgScenarioFacilityGroup()          Sets the current record's "agScenarioFacilityGroup" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagFacilityGroupAllocationStatus extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_facility_group_allocation_status');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('facility_group_allocation_status', 'string', 30, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 30,
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('allocatable', 'boolean', null, array(
             'default' => 1,
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('standby', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('active', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('app_display', 'boolean', null, array(
             'default' => 1,
             'type' => 'boolean',
             'notnull' => true,
             ));


        $this->index('UX_agFacilityGroupAllocationStatus', array(
             'fields' => 
             array(
              0 => 'facility_group_allocation_status',
             ),
             'type' => 'unique',
             ));
        $this->index('idx_facility_group_allocation_status_allocatable', array(
             'fields' => 
             array(
              0 => 'allocatable',
             ),
             ));
        $this->index('idx_facility_group_allocation_status_standby', array(
             'fields' => 
             array(
              0 => 'standby',
             ),
             ));
        $this->index('idx_facility_group_allocation_status_active', array(
             'fields' => 
             array(
              0 => 'active',
             ),
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agEventFacilityGroupStatus', array(
             'local' => 'id',
             'foreign' => 'facility_group_allocation_status_id'));

        $this->hasMany('agScenarioFacilityGroup', array(
             'local' => 'id',
             'foreign' => 'facility_group_allocation_status_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}