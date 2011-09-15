<?php

/**
 * BaseagStaffAllocationStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $staff_allocation_status
 * @property string $description
 * @property boolean $allocatable
 * @property boolean $committed
 * @property boolean $standby
 * @property boolean $active
 * @property boolean $app_display
 * @property Doctrine_Collection $agEventStaffStatus
 * 
 * @method integer                 getId()                      Returns the current record's "id" value
 * @method string                  getStaffAllocationStatus()   Returns the current record's "staff_allocation_status" value
 * @method string                  getDescription()             Returns the current record's "description" value
 * @method boolean                 getAllocatable()             Returns the current record's "allocatable" value
 * @method boolean                 getCommitted()               Returns the current record's "committed" value
 * @method boolean                 getStandby()                 Returns the current record's "standby" value
 * @method boolean                 getActive()                  Returns the current record's "active" value
 * @method boolean                 getAppDisplay()              Returns the current record's "app_display" value
 * @method Doctrine_Collection     getAgEventStaffStatus()      Returns the current record's "agEventStaffStatus" collection
 * @method agStaffAllocationStatus setId()                      Sets the current record's "id" value
 * @method agStaffAllocationStatus setStaffAllocationStatus()   Sets the current record's "staff_allocation_status" value
 * @method agStaffAllocationStatus setDescription()             Sets the current record's "description" value
 * @method agStaffAllocationStatus setAllocatable()             Sets the current record's "allocatable" value
 * @method agStaffAllocationStatus setCommitted()               Sets the current record's "committed" value
 * @method agStaffAllocationStatus setStandby()                 Sets the current record's "standby" value
 * @method agStaffAllocationStatus setActive()                  Sets the current record's "active" value
 * @method agStaffAllocationStatus setAppDisplay()              Sets the current record's "app_display" value
 * @method agStaffAllocationStatus setAgEventStaffStatus()      Sets the current record's "agEventStaffStatus" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagStaffAllocationStatus extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_staff_allocation_status');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('staff_allocation_status', 'string', 30, array(
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
        $this->hasColumn('committed', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('standby', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('active', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('app_display', 'boolean', null, array(
             'default' => 1,
             'type' => 'boolean',
             'notnull' => true,
             ));


        $this->index('UX_agEventStaffStatus', array(
             'fields' => 
             array(
              0 => 'staff_allocation_status',
             ),
             'type' => 'unique',
             ));
        $this->index('idx_staff_allocation_status_allocatable', array(
             'fields' => 
             array(
              0 => 'allocatable',
             ),
             ));
        $this->index('idx_staff_allocation_status_committed', array(
             'fields' => 
             array(
              0 => 'committed',
             ),
             ));
        $this->index('idx_staff_allocation_status_standby', array(
             'fields' => 
             array(
              0 => 'standby',
             ),
             ));
        $this->index('idx_staff_allocation_status_active', array(
             'fields' => 
             array(
              0 => 'active',
             ),
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agEventStaffStatus', array(
             'local' => 'id',
             'foreign' => 'staff_allocation_status_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}