<?php

/**
 * BaseagTask
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $task
 * @property string $description
 * @property boolean $app_display
 * @property Doctrine_Collection $agEventFacilityShift
 * @property Doctrine_Collection $agEventRelativeShift
 * @property Doctrine_Collection $agScenarioShift
 * @property Doctrine_Collection $agShiftTemplate
 * 
 * @method integer             getId()                   Returns the current record's "id" value
 * @method string              getTask()                 Returns the current record's "task" value
 * @method string              getDescription()          Returns the current record's "description" value
 * @method boolean             getAppDisplay()           Returns the current record's "app_display" value
 * @method Doctrine_Collection getAgEventFacilityShift() Returns the current record's "agEventFacilityShift" collection
 * @method Doctrine_Collection getAgEventRelativeShift() Returns the current record's "agEventRelativeShift" collection
 * @method Doctrine_Collection getAgScenarioShift()      Returns the current record's "agScenarioShift" collection
 * @method Doctrine_Collection getAgShiftTemplate()      Returns the current record's "agShiftTemplate" collection
 * @method agTask              setId()                   Sets the current record's "id" value
 * @method agTask              setTask()                 Sets the current record's "task" value
 * @method agTask              setDescription()          Sets the current record's "description" value
 * @method agTask              setAppDisplay()           Sets the current record's "app_display" value
 * @method agTask              setAgEventFacilityShift() Sets the current record's "agEventFacilityShift" collection
 * @method agTask              setAgEventRelativeShift() Sets the current record's "agEventRelativeShift" collection
 * @method agTask              setAgScenarioShift()      Sets the current record's "agScenarioShift" collection
 * @method agTask              setAgShiftTemplate()      Sets the current record's "agShiftTemplate" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagTask extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_task');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('task', 'string', 30, array(
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


        $this->index('agShiftTask_unq', array(
             'fields' => 
             array(
              0 => 'task',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agEventShift as agEventFacilityShift', array(
             'local' => 'id',
             'foreign' => 'task_id'));

        $this->hasMany('agEventRelativeShift', array(
             'local' => 'id',
             'foreign' => 'task_id'));

        $this->hasMany('agScenarioShift', array(
             'local' => 'id',
             'foreign' => 'task_id'));

        $this->hasMany('agShiftTemplate', array(
             'local' => 'id',
             'foreign' => 'task_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}