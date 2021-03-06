<?php

/**
 * BaseagPersonMjAgProfession
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $person_id
 * @property integer $profession_id
 * @property string $title
 * @property agPerson $agPerson
 * @property agProfession $agProfession
 * 
 * @method integer                getId()            Returns the current record's "id" value
 * @method integer                getPersonId()      Returns the current record's "person_id" value
 * @method integer                getProfessionId()  Returns the current record's "profession_id" value
 * @method string                 getTitle()         Returns the current record's "title" value
 * @method agPerson               getAgPerson()      Returns the current record's "agPerson" value
 * @method agProfession           getAgProfession()  Returns the current record's "agProfession" value
 * @method agPersonMjAgProfession setId()            Sets the current record's "id" value
 * @method agPersonMjAgProfession setPersonId()      Sets the current record's "person_id" value
 * @method agPersonMjAgProfession setProfessionId()  Sets the current record's "profession_id" value
 * @method agPersonMjAgProfession setTitle()         Sets the current record's "title" value
 * @method agPersonMjAgProfession setAgPerson()      Sets the current record's "agPerson" value
 * @method agPersonMjAgProfession setAgProfession()  Sets the current record's "agProfession" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPersonMjAgProfession extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_person_mj_ag_profession');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('person_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('profession_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('title', 'string', 128, array(
             'type' => 'string',
             'length' => 128,
             ));


        $this->index('UX_ag_person_mj_ag_profession', array(
             'fields' => 
             array(
              0 => 'person_id',
              1 => 'profession_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agPerson', array(
             'local' => 'person_id',
             'foreign' => 'id'));

        $this->hasOne('agProfession', array(
             'local' => 'profession_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}