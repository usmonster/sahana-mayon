<?php

/**
 * BaseagScenarioServiceAreaComposite
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $scenario_service_area_id
 * @property integer $geo_id
 * @property agScenarioServiceArea $agScenarioServiceArea
 * @property agGeo $agGeo
 * 
 * @method integer                        getId()                       Returns the current record's "id" value
 * @method integer                        getScenarioServiceAreaId()    Returns the current record's "scenario_service_area_id" value
 * @method integer                        getGeoId()                    Returns the current record's "geo_id" value
 * @method agScenarioServiceArea          getAgScenarioServiceArea()    Returns the current record's "agScenarioServiceArea" value
 * @method agGeo                          getAgGeo()                    Returns the current record's "agGeo" value
 * @method agScenarioServiceAreaComposite setId()                       Sets the current record's "id" value
 * @method agScenarioServiceAreaComposite setScenarioServiceAreaId()    Sets the current record's "scenario_service_area_id" value
 * @method agScenarioServiceAreaComposite setGeoId()                    Sets the current record's "geo_id" value
 * @method agScenarioServiceAreaComposite setAgScenarioServiceArea()    Sets the current record's "agScenarioServiceArea" value
 * @method agScenarioServiceAreaComposite setAgGeo()                    Sets the current record's "agGeo" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagScenarioServiceAreaComposite extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_scenario_service_area_composite');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('scenario_service_area_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('geo_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));


        $this->index('UX_agScenarioServiceAreaComposite', array(
             'fields' => 
             array(
              0 => 'scenario_service_area_id',
             ),
             'type' => 'unique',
             ));
        $this->index('agScenarioServiceAreaComposite_prepServiceAreaId', array(
             'fields' => 
             array(
              0 => 'scenario_service_area_id',
             ),
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agScenarioServiceArea', array(
             'local' => 'scenario_service_area_id',
             'foreign' => 'id'));

        $this->hasOne('agGeo', array(
             'local' => 'geo_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}