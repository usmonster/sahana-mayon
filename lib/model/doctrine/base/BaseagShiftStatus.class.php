<?php

/**
 * BaseagShiftStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $shift_status
 * @property string $description
 * @property boolean $standby
 * @property Doctrine_Collection $agEventShift
 * @property Doctrine_Collection $agScenarioShift
 * @property Doctrine_Collection $agShiftTemplate
 * 
 * @method integer             getId()              Returns the current record's "id" value
 * @method string              getShiftStatus()     Returns the current record's "shift_status" value
 * @method string              getDescription()     Returns the current record's "description" value
 * @method boolean             getStandby()         Returns the current record's "standby" value
 * @method Doctrine_Collection getAgEventShift()    Returns the current record's "agEventShift" collection
 * @method Doctrine_Collection getAgScenarioShift() Returns the current record's "agScenarioShift" collection
 * @method Doctrine_Collection getAgShiftTemplate() Returns the current record's "agShiftTemplate" collection
 * @method agShiftStatus       setId()              Sets the current record's "id" value
 * @method agShiftStatus       setShiftStatus()     Sets the current record's "shift_status" value
 * @method agShiftStatus       setDescription()     Sets the current record's "description" value
 * @method agShiftStatus       setStandby()         Sets the current record's "standby" value
 * @method agShiftStatus       setAgEventShift()    Sets the current record's "agEventShift" collection
 * @method agShiftStatus       setAgScenarioShift() Sets the current record's "agScenarioShift" collection
 * @method agShiftStatus       setAgShiftTemplate() Sets the current record's "agShiftTemplate" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagShiftStatus extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_shift_status');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('shift_status', 'string', 32, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 32,
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('standby', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             'notnull' => true,
             ));


        $this->index('UX_agShiftStatus', array(
             'fields' => 
             array(
              0 => 'shift_status',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agEventShift', array(
             'local' => 'id',
             'foreign' => 'shift_status_id'));

        $this->hasMany('agScenarioShift', array(
             'local' => 'id',
             'foreign' => 'shift_status_id'));

        $this->hasMany('agShiftTemplate', array(
             'local' => 'id',
             'foreign' => 'shift_status_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}