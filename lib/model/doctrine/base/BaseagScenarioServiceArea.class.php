<?php

/**
 * BaseagScenarioServiceArea
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property Doctrine_Collection $agScenarioFacilityDistribution
 * @property Doctrine_Collection $agScenarioServiceAreaComposite
 * 
 * @method integer               getId()                             Returns the current record's "id" value
 * @method Doctrine_Collection   getAgScenarioFacilityDistribution() Returns the current record's "agScenarioFacilityDistribution" collection
 * @method Doctrine_Collection   getAgScenarioServiceAreaComposite() Returns the current record's "agScenarioServiceAreaComposite" collection
 * @method agScenarioServiceArea setId()                             Sets the current record's "id" value
 * @method agScenarioServiceArea setAgScenarioFacilityDistribution() Sets the current record's "agScenarioFacilityDistribution" collection
 * @method agScenarioServiceArea setAgScenarioServiceAreaComposite() Sets the current record's "agScenarioServiceAreaComposite" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagScenarioServiceArea extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_scenario_service_area');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agScenarioFacilityDistribution', array(
             'local' => 'id',
             'foreign' => 'scenario_service_area_id'));

        $this->hasMany('agScenarioServiceAreaComposite', array(
             'local' => 'id',
             'foreign' => 'scenario_service_area_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}