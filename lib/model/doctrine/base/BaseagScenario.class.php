<?php

/**
 * BaseagScenario
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $scenario
 * @property string $description
 * @property Doctrine_Collection $agEvent
 * @property Doctrine_Collection $agEventScenario
 * @property Doctrine_Collection $agScenarioAffectedArea
 * @property Doctrine_Collection $agDefaultScenarioFacilityResourceType
 * @property Doctrine_Collection $agDefaultScenarioStaffResourceType
 * @property Doctrine_Collection $agScenarioFacilityGroup
 * @property Doctrine_Collection $agShiftTemplate
 * @property Doctrine_Collection $agScenarioStaffResource
 * @property Doctrine_Collection $agScenarioStaffGenerator
 * 
 * @method integer             getId()                                    Returns the current record's "id" value
 * @method string              getScenario()                              Returns the current record's "scenario" value
 * @method string              getDescription()                           Returns the current record's "description" value
 * @method Doctrine_Collection getAgEvent()                               Returns the current record's "agEvent" collection
 * @method Doctrine_Collection getAgEventScenario()                       Returns the current record's "agEventScenario" collection
 * @method Doctrine_Collection getAgScenarioAffectedArea()                Returns the current record's "agScenarioAffectedArea" collection
 * @method Doctrine_Collection getAgDefaultScenarioFacilityResourceType() Returns the current record's "agDefaultScenarioFacilityResourceType" collection
 * @method Doctrine_Collection getAgDefaultScenarioStaffResourceType()    Returns the current record's "agDefaultScenarioStaffResourceType" collection
 * @method Doctrine_Collection getAgScenarioFacilityGroup()               Returns the current record's "agScenarioFacilityGroup" collection
 * @method Doctrine_Collection getAgShiftTemplate()                       Returns the current record's "agShiftTemplate" collection
 * @method Doctrine_Collection getAgScenarioStaffResource()               Returns the current record's "agScenarioStaffResource" collection
 * @method Doctrine_Collection getAgScenarioStaffGenerator()              Returns the current record's "agScenarioStaffGenerator" collection
 * @method agScenario          setId()                                    Sets the current record's "id" value
 * @method agScenario          setScenario()                              Sets the current record's "scenario" value
 * @method agScenario          setDescription()                           Sets the current record's "description" value
 * @method agScenario          setAgEvent()                               Sets the current record's "agEvent" collection
 * @method agScenario          setAgEventScenario()                       Sets the current record's "agEventScenario" collection
 * @method agScenario          setAgScenarioAffectedArea()                Sets the current record's "agScenarioAffectedArea" collection
 * @method agScenario          setAgDefaultScenarioFacilityResourceType() Sets the current record's "agDefaultScenarioFacilityResourceType" collection
 * @method agScenario          setAgDefaultScenarioStaffResourceType()    Sets the current record's "agDefaultScenarioStaffResourceType" collection
 * @method agScenario          setAgScenarioFacilityGroup()               Sets the current record's "agScenarioFacilityGroup" collection
 * @method agScenario          setAgShiftTemplate()                       Sets the current record's "agShiftTemplate" collection
 * @method agScenario          setAgScenarioStaffResource()               Sets the current record's "agScenarioStaffResource" collection
 * @method agScenario          setAgScenarioStaffGenerator()              Sets the current record's "agScenarioStaffGenerator" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagScenario extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_scenario');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('scenario', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));


        $this->index('agScenario_unq', array(
             'fields' => 
             array(
              0 => 'scenario',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agEvent', array(
             'refClass' => 'agEventScenario',
             'local' => 'scenario_id',
             'foreign' => 'event_id'));

        $this->hasMany('agEventScenario', array(
             'local' => 'id',
             'foreign' => 'scenario_id'));

        $this->hasMany('agScenarioAffectedArea', array(
             'local' => 'id',
             'foreign' => 'scenario_id'));

        $this->hasMany('agDefaultScenarioFacilityResourceType', array(
             'local' => 'id',
             'foreign' => 'scenario_id'));

        $this->hasMany('agDefaultScenarioStaffResourceType', array(
             'local' => 'id',
             'foreign' => 'scenario_id'));

        $this->hasMany('agScenarioFacilityGroup', array(
             'local' => 'id',
             'foreign' => 'scenario_id'));

        $this->hasMany('agShiftTemplate', array(
             'local' => 'id',
             'foreign' => 'scenario_id'));

        $this->hasMany('agScenarioStaffResource', array(
             'local' => 'id',
             'foreign' => 'scenario_id'));

        $this->hasMany('agScenarioStaffGenerator', array(
             'local' => 'id',
             'foreign' => 'scenario_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}