<?php

/**
 * BaseagPersonMjAgPersonName
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $person_id
 * @property integer $person_name_id
 * @property integer $person_name_type_id
 * @property integer $priority
 * @property agPerson $agPerson
 * @property agPersonName $agPersonName
 * @property agPersonNameType $agPersonNameType
 * 
 * @method integer                getId()                  Returns the current record's "id" value
 * @method integer                getPersonId()            Returns the current record's "person_id" value
 * @method integer                getPersonNameId()        Returns the current record's "person_name_id" value
 * @method integer                getPersonNameTypeId()    Returns the current record's "person_name_type_id" value
 * @method integer                getPriority()            Returns the current record's "priority" value
 * @method agPerson               getAgPerson()            Returns the current record's "agPerson" value
 * @method agPersonName           getAgPersonName()        Returns the current record's "agPersonName" value
 * @method agPersonNameType       getAgPersonNameType()    Returns the current record's "agPersonNameType" value
 * @method agPersonMjAgPersonName setId()                  Sets the current record's "id" value
 * @method agPersonMjAgPersonName setPersonId()            Sets the current record's "person_id" value
 * @method agPersonMjAgPersonName setPersonNameId()        Sets the current record's "person_name_id" value
 * @method agPersonMjAgPersonName setPersonNameTypeId()    Sets the current record's "person_name_type_id" value
 * @method agPersonMjAgPersonName setPriority()            Sets the current record's "priority" value
 * @method agPersonMjAgPersonName setAgPerson()            Sets the current record's "agPerson" value
 * @method agPersonMjAgPersonName setAgPersonName()        Sets the current record's "agPersonName" value
 * @method agPersonMjAgPersonName setAgPersonNameType()    Sets the current record's "agPersonNameType" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPersonMjAgPersonName extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_person_mj_ag_person_name');
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
        $this->hasColumn('person_name_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('person_name_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('priority', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 1,
             ));


        $this->index('UX_ag_person_mj_ag_person_name', array(
             'fields' => 
             array(
              0 => 'person_name_id',
              1 => 'person_name_type_id',
              2 => 'person_id',
             ),
             'type' => 'unique',
             ));
        $this->index('agPersonMjAgPersonName_priority_unq', array(
             'fields' => 
             array(
              0 => 'person_id',
              1 => 'person_name_type_id',
              2 => 'priority',
             ),
             'type' => 'unique',
             ));
        $this->index('IX_agPersonMjAgPersonName_personId', array(
             'fields' => 
             array(
              0 => 'person_id',
             ),
             ));
        $this->index('IX_agPersonMjAgPersonName_personNameTypeId', array(
             'fields' => 
             array(
              0 => 'person_name_type_id',
             ),
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agPerson', array(
             'local' => 'person_id',
             'foreign' => 'id'));

        $this->hasOne('agPersonName', array(
             'local' => 'person_name_id',
             'foreign' => 'id'));

        $this->hasOne('agPersonNameType', array(
             'local' => 'person_name_type_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}