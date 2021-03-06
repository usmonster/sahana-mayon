<?php

/**
 * BaseagFacilityStaffResource
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $scenario_facility_resource_id
 * @property integer $staff_resource_type_id
 * @property integer $minimum_staff
 * @property integer $maximum_staff
 * @property agScenarioFacilityResource $agScenarioFacilityResource
 * @property agStaffResourceType $agStaffResourceType
 * 
 * @method integer                    getId()                            Returns the current record's "id" value
 * @method integer                    getScenarioFacilityResourceId()    Returns the current record's "scenario_facility_resource_id" value
 * @method integer                    getStaffResourceTypeId()           Returns the current record's "staff_resource_type_id" value
 * @method integer                    getMinimumStaff()                  Returns the current record's "minimum_staff" value
 * @method integer                    getMaximumStaff()                  Returns the current record's "maximum_staff" value
 * @method agScenarioFacilityResource getAgScenarioFacilityResource()    Returns the current record's "agScenarioFacilityResource" value
 * @method agStaffResourceType        getAgStaffResourceType()           Returns the current record's "agStaffResourceType" value
 * @method agFacilityStaffResource    setId()                            Sets the current record's "id" value
 * @method agFacilityStaffResource    setScenarioFacilityResourceId()    Sets the current record's "scenario_facility_resource_id" value
 * @method agFacilityStaffResource    setStaffResourceTypeId()           Sets the current record's "staff_resource_type_id" value
 * @method agFacilityStaffResource    setMinimumStaff()                  Sets the current record's "minimum_staff" value
 * @method agFacilityStaffResource    setMaximumStaff()                  Sets the current record's "maximum_staff" value
 * @method agFacilityStaffResource    setAgScenarioFacilityResource()    Sets the current record's "agScenarioFacilityResource" value
 * @method agFacilityStaffResource    setAgStaffResourceType()           Sets the current record's "agStaffResourceType" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagFacilityStaffResource extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_facility_staff_resource');
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


        $this->index('agFacilityStaffResource_unq', array(
             'fields' => 
             array(
              0 => 'scenario_facility_resource_id',
              1 => 'staff_resource_type_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agScenarioFacilityResource', array(
             'local' => 'scenario_facility_resource_id',
             'foreign' => 'id'));

        $this->hasOne('agStaffResourceType', array(
             'local' => 'staff_resource_type_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}