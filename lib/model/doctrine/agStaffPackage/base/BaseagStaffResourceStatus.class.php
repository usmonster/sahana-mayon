<?php

/**
 * BaseagStaffResourceStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $staff_resource_status
 * @property string $description
 * @property boolean $is_available
 * @property boolean $app_display
 * @property Doctrine_Collection $agStaffResource
 * 
 * @method integer               getId()                    Returns the current record's "id" value
 * @method string                getStaffResourceStatus()   Returns the current record's "staff_resource_status" value
 * @method string                getDescription()           Returns the current record's "description" value
 * @method boolean               getIsAvailable()           Returns the current record's "is_available" value
 * @method boolean               getAppDisplay()            Returns the current record's "app_display" value
 * @method Doctrine_Collection   getAgStaffResource()       Returns the current record's "agStaffResource" collection
 * @method agStaffResourceStatus setId()                    Sets the current record's "id" value
 * @method agStaffResourceStatus setStaffResourceStatus()   Sets the current record's "staff_resource_status" value
 * @method agStaffResourceStatus setDescription()           Sets the current record's "description" value
 * @method agStaffResourceStatus setIsAvailable()           Sets the current record's "is_available" value
 * @method agStaffResourceStatus setAppDisplay()            Sets the current record's "app_display" value
 * @method agStaffResourceStatus setAgStaffResource()       Sets the current record's "agStaffResource" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagStaffResourceStatus extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_staff_resource_status');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('staff_resource_status', 'string', 30, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 30,
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('is_available', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('app_display', 'boolean', null, array(
             'default' => 1,
             'type' => 'boolean',
             'notnull' => true,
             ));


        $this->index('agStaffResourceStatus_unq', array(
             'fields' => 
             array(
              0 => 'staff_resource_status',
             ),
             'type' => 'unique',
             ));
        $this->index('idx_staff_resource_status_is_available', array(
             'fields' => 
             array(
              0 => 'is_available',
             ),
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agStaffResource', array(
             'local' => 'id',
             'foreign' => 'staff_resource_status_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}