<?php

/**
 * BaseagEthnicity
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $ethnicity
 * @property boolean $app_display
 * @property Doctrine_Collection $agPerson
 * @property Doctrine_Collection $agPersonEthnicity
 * 
 * @method integer             getId()                Returns the current record's "id" value
 * @method string              getEthnicity()         Returns the current record's "ethnicity" value
 * @method boolean             getAppDisplay()        Returns the current record's "app_display" value
 * @method Doctrine_Collection getAgPerson()          Returns the current record's "agPerson" collection
 * @method Doctrine_Collection getAgPersonEthnicity() Returns the current record's "agPersonEthnicity" collection
 * @method agEthnicity         setId()                Sets the current record's "id" value
 * @method agEthnicity         setEthnicity()         Sets the current record's "ethnicity" value
 * @method agEthnicity         setAppDisplay()        Sets the current record's "app_display" value
 * @method agEthnicity         setAgPerson()          Sets the current record's "agPerson" collection
 * @method agEthnicity         setAgPersonEthnicity() Sets the current record's "agPersonEthnicity" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEthnicity extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_ethnicity');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('ethnicity', 'string', 64, array(
             'unique' => true,
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('app_display', 'boolean', null, array(
             'default' => 1,
             'type' => 'boolean',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agPerson', array(
             'refClass' => 'agPersonEthnicity',
             'local' => 'ethnicity_id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonEthnicity', array(
             'local' => 'id',
             'foreign' => 'ethnicity_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}