<?php

/**
 * BaseagDeploymentAlgorithm
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $deployment_algorithm
 * @property string $description
 * @property boolean $app_display
 * @property Doctrine_Collection $agEventShift
 * @property Doctrine_Collection $agScenarioShift
 * @property Doctrine_Collection $agShiftTemplate
 * 
 * @method integer               getId()                   Returns the current record's "id" value
 * @method string                getDeploymentAlgorithm()  Returns the current record's "deployment_algorithm" value
 * @method string                getDescription()          Returns the current record's "description" value
 * @method boolean               getAppDisplay()           Returns the current record's "app_display" value
 * @method Doctrine_Collection   getAgEventShift()         Returns the current record's "agEventShift" collection
 * @method Doctrine_Collection   getAgScenarioShift()      Returns the current record's "agScenarioShift" collection
 * @method Doctrine_Collection   getAgShiftTemplate()      Returns the current record's "agShiftTemplate" collection
 * @method agDeploymentAlgorithm setId()                   Sets the current record's "id" value
 * @method agDeploymentAlgorithm setDeploymentAlgorithm()  Sets the current record's "deployment_algorithm" value
 * @method agDeploymentAlgorithm setDescription()          Sets the current record's "description" value
 * @method agDeploymentAlgorithm setAppDisplay()           Sets the current record's "app_display" value
 * @method agDeploymentAlgorithm setAgEventShift()         Sets the current record's "agEventShift" collection
 * @method agDeploymentAlgorithm setAgScenarioShift()      Sets the current record's "agScenarioShift" collection
 * @method agDeploymentAlgorithm setAgShiftTemplate()      Sets the current record's "agShiftTemplate" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagDeploymentAlgorithm extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_deployment_algorithm');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('deployment_algorithm', 'string', 30, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 30,
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('app_display', 'boolean', null, array(
             'default' => 1,
             'type' => 'boolean',
             'notnull' => true,
             ));


        $this->index('agDeploymentAlgorithm_unq', array(
             'fields' => 
             array(
              0 => 'deployment_algorithm',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agEventShift', array(
             'local' => 'id',
             'foreign' => 'deployment_algorithm_id'));

        $this->hasMany('agScenarioShift', array(
             'local' => 'id',
             'foreign' => 'deployment_algorithm_id'));

        $this->hasMany('agShiftTemplate', array(
             'local' => 'id',
             'foreign' => 'deployment_algorithm_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}