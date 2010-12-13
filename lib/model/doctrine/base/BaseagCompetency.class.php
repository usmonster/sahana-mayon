<?php

/**
 * BaseagCompetency
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $competency
 * @property integer $priority
 * @property string $description
 * @property boolean $app_display
 * @property Doctrine_Collection $agPersonSkill
 * @property Doctrine_Collection $agCertifiedSkill
 * 
 * @method integer             getId()               Returns the current record's "id" value
 * @method string              getCompetency()       Returns the current record's "competency" value
 * @method integer             getPriority()         Returns the current record's "priority" value
 * @method string              getDescription()      Returns the current record's "description" value
 * @method boolean             getAppDisplay()       Returns the current record's "app_display" value
 * @method Doctrine_Collection getAgPersonSkill()    Returns the current record's "agPersonSkill" collection
 * @method Doctrine_Collection getAgCertifiedSkill() Returns the current record's "agCertifiedSkill" collection
 * @method agCompetency        setId()               Sets the current record's "id" value
 * @method agCompetency        setCompetency()       Sets the current record's "competency" value
 * @method agCompetency        setPriority()         Sets the current record's "priority" value
 * @method agCompetency        setDescription()      Sets the current record's "description" value
 * @method agCompetency        setAppDisplay()       Sets the current record's "app_display" value
 * @method agCompetency        setAgPersonSkill()    Sets the current record's "agPersonSkill" collection
 * @method agCompetency        setAgCertifiedSkill() Sets the current record's "agCertifiedSkill" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagCompetency extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_competency');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('competency', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('priority', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 1,
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


        $this->index('agCompetency_unq', array(
             'fields' => 
             array(
              0 => 'competency',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agPersonSkill', array(
             'local' => 'id',
             'foreign' => 'competency_id'));

        $this->hasMany('agCertifiedSkill', array(
             'local' => 'id',
             'foreign' => 'competency_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}