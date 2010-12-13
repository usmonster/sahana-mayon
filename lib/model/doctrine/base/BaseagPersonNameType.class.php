<?php

/**
 * BaseagPersonNameType
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $person_name_type
 * @property boolean $app_display
 * @property Doctrine_Collection $agPerson
 * @property Doctrine_Collection $agPersonName
 * @property Doctrine_Collection $agPersonMjAgPersonName
 * 
 * @method integer             getId()                     Returns the current record's "id" value
 * @method string              getPersonNameType()         Returns the current record's "person_name_type" value
 * @method boolean             getAppDisplay()             Returns the current record's "app_display" value
 * @method Doctrine_Collection getAgPerson()               Returns the current record's "agPerson" collection
 * @method Doctrine_Collection getAgPersonName()           Returns the current record's "agPersonName" collection
 * @method Doctrine_Collection getAgPersonMjAgPersonName() Returns the current record's "agPersonMjAgPersonName" collection
 * @method agPersonNameType    setId()                     Sets the current record's "id" value
 * @method agPersonNameType    setPersonNameType()         Sets the current record's "person_name_type" value
 * @method agPersonNameType    setAppDisplay()             Sets the current record's "app_display" value
 * @method agPersonNameType    setAgPerson()               Sets the current record's "agPerson" collection
 * @method agPersonNameType    setAgPersonName()           Sets the current record's "agPersonName" collection
 * @method agPersonNameType    setAgPersonMjAgPersonName() Sets the current record's "agPersonMjAgPersonName" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPersonNameType extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_person_name_type');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('person_name_type', 'string', 30, array(
             'unique' => true,
             'type' => 'string',
             'notnull' => true,
             'length' => 30,
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
             'refClass' => 'agPersonMjAgPersonName',
             'local' => 'person_name_type_id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonName', array(
             'refClass' => 'agPersonMjAgPersonName',
             'local' => 'person_name_type_id',
             'foreign' => 'person_name_id'));

        $this->hasMany('agPersonMjAgPersonName', array(
             'local' => 'id',
             'foreign' => 'person_name_type_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}