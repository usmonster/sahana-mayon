<?php

/**
 * BaseagScenarioFacilityGroup
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $scenario_id
 * @property string $scenario_facility_group
 * @property integer $facility_group_type_id
 * @property integer $facility_group_allocation_status_id
 * @property integer $activation_sequence
 * @property agScenario $agScenario
 * @property agFacilityGroupType $agFacilityGroupType
 * @property agFacilityGroupAllocationStatus $agFacilityGroupAllocationStatus
 * @property Doctrine_Collection $agFacilityResource
 * @property Doctrine_Collection $agScenarioFacilityDistribution
 * @property Doctrine_Collection $agScenarioFacilityResource
 * 
 * @method integer                         getId()                                  Returns the current record's "id" value
 * @method integer                         getScenarioId()                          Returns the current record's "scenario_id" value
 * @method string                          getScenarioFacilityGroup()               Returns the current record's "scenario_facility_group" value
 * @method integer                         getFacilityGroupTypeId()                 Returns the current record's "facility_group_type_id" value
 * @method integer                         getFacilityGroupAllocationStatusId()     Returns the current record's "facility_group_allocation_status_id" value
 * @method integer                         getActivationSequence()                  Returns the current record's "activation_sequence" value
 * @method agScenario                      getAgScenario()                          Returns the current record's "agScenario" value
 * @method agFacilityGroupType             getAgFacilityGroupType()                 Returns the current record's "agFacilityGroupType" value
 * @method agFacilityGroupAllocationStatus getAgFacilityGroupAllocationStatus()     Returns the current record's "agFacilityGroupAllocationStatus" value
 * @method Doctrine_Collection             getAgFacilityResource()                  Returns the current record's "agFacilityResource" collection
 * @method Doctrine_Collection             getAgScenarioFacilityDistribution()      Returns the current record's "agScenarioFacilityDistribution" collection
 * @method Doctrine_Collection             getAgScenarioFacilityResource()          Returns the current record's "agScenarioFacilityResource" collection
 * @method agScenarioFacilityGroup         setId()                                  Sets the current record's "id" value
 * @method agScenarioFacilityGroup         setScenarioId()                          Sets the current record's "scenario_id" value
 * @method agScenarioFacilityGroup         setScenarioFacilityGroup()               Sets the current record's "scenario_facility_group" value
 * @method agScenarioFacilityGroup         setFacilityGroupTypeId()                 Sets the current record's "facility_group_type_id" value
 * @method agScenarioFacilityGroup         setFacilityGroupAllocationStatusId()     Sets the current record's "facility_group_allocation_status_id" value
 * @method agScenarioFacilityGroup         setActivationSequence()                  Sets the current record's "activation_sequence" value
 * @method agScenarioFacilityGroup         setAgScenario()                          Sets the current record's "agScenario" value
 * @method agScenarioFacilityGroup         setAgFacilityGroupType()                 Sets the current record's "agFacilityGroupType" value
 * @method agScenarioFacilityGroup         setAgFacilityGroupAllocationStatus()     Sets the current record's "agFacilityGroupAllocationStatus" value
 * @method agScenarioFacilityGroup         setAgFacilityResource()                  Sets the current record's "agFacilityResource" collection
 * @method agScenarioFacilityGroup         setAgScenarioFacilityDistribution()      Sets the current record's "agScenarioFacilityDistribution" collection
 * @method agScenarioFacilityGroup         setAgScenarioFacilityResource()          Sets the current record's "agScenarioFacilityResource" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagScenarioFacilityGroup extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_scenario_facility_group');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'unique' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('scenario_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('scenario_facility_group', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('facility_group_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('facility_group_allocation_status_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('activation_sequence', 'integer', 1, array(
             'default' => 100,
             'unsigned' => true,
             'type' => 'integer',
             'notnull' => true,
             'length' => 1,
             ));


        $this->index('UX_agScenarioFacilityGroup', array(
             'fields' => 
             array(
              0 => 'scenario_id',
              1 => 'scenario_facility_group',
             ),
             'type' => 'unique',
             ));
        $this->index('IX_agScenarioFacilityGroup_scenarioFacilityGroup', array(
             'fields' => 
             array(
              0 => 'scenario_facility_group',
             ),
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agScenario', array(
             'local' => 'scenario_id',
             'foreign' => 'id'));

        $this->hasOne('agFacilityGroupType', array(
             'local' => 'facility_group_type_id',
             'foreign' => 'id'));

        $this->hasOne('agFacilityGroupAllocationStatus', array(
             'local' => 'facility_group_allocation_status_id',
             'foreign' => 'id'));

        $this->hasMany('agFacilityResource', array(
             'refClass' => 'agScenarioFacilityResource',
             'local' => 'scenario_facility_group_id',
             'foreign' => 'facility_resource_id'));

        $this->hasMany('agScenarioFacilityDistribution', array(
             'local' => 'id',
             'foreign' => 'scenario_facility_group_id'));

        $this->hasMany('agScenarioFacilityResource', array(
             'local' => 'id',
             'foreign' => 'scenario_facility_group_id'));

        $luceneable0 = new Luceneable(array(
             ));
        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($luceneable0);
        $this->actAs($timestampable0);
    }
}