<?php

/**
 * BaseagNationality
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $nationality
 * @property string $description
 * @property boolean $app_display
 * @property Doctrine_Collection $agPerson
 * @property Doctrine_Collection $agPersonMjAgNationality
 * 
 * @method integer             getId()                      Returns the current record's "id" value
 * @method string              getNationality()             Returns the current record's "nationality" value
 * @method string              getDescription()             Returns the current record's "description" value
 * @method boolean             getAppDisplay()              Returns the current record's "app_display" value
 * @method Doctrine_Collection getAgPerson()                Returns the current record's "agPerson" collection
 * @method Doctrine_Collection getAgPersonMjAgNationality() Returns the current record's "agPersonMjAgNationality" collection
 * @method agNationality       setId()                      Sets the current record's "id" value
 * @method agNationality       setNationality()             Sets the current record's "nationality" value
 * @method agNationality       setDescription()             Sets the current record's "description" value
 * @method agNationality       setAppDisplay()              Sets the current record's "app_display" value
 * @method agNationality       setAgPerson()                Sets the current record's "agPerson" collection
 * @method agNationality       setAgPersonMjAgNationality() Sets the current record's "agPersonMjAgNationality" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagNationality extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_nationality');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('nationality', 'string', 128, array(
             'unique' => true,
             'type' => 'string',
             'notnull' => true,
             'length' => 128,
             ));
        $this->hasColumn('description', 'string', 128, array(
             'type' => 'string',
             'length' => 128,
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
             'refClass' => 'agPersonMjAgNationality',
             'local' => 'nationality_id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonMjAgNationality', array(
             'local' => 'id',
             'foreign' => 'nationality_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}