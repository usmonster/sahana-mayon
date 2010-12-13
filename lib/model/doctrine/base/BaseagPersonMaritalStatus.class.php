<?php

/**
 * BaseagPersonMaritalStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $person_id
 * @property integer $marital_status_id
 * @property agPerson $agPerson
 * @property agMaritalStatus $agMaritalStatus
 * 
 * @method integer               getId()                Returns the current record's "id" value
 * @method integer               getPersonId()          Returns the current record's "person_id" value
 * @method integer               getMaritalStatusId()   Returns the current record's "marital_status_id" value
 * @method agPerson              getAgPerson()          Returns the current record's "agPerson" value
 * @method agMaritalStatus       getAgMaritalStatus()   Returns the current record's "agMaritalStatus" value
 * @method agPersonMaritalStatus setId()                Sets the current record's "id" value
 * @method agPersonMaritalStatus setPersonId()          Sets the current record's "person_id" value
 * @method agPersonMaritalStatus setMaritalStatusId()   Sets the current record's "marital_status_id" value
 * @method agPersonMaritalStatus setAgPerson()          Sets the current record's "agPerson" value
 * @method agPersonMaritalStatus setAgMaritalStatus()   Sets the current record's "agMaritalStatus" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPersonMaritalStatus extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_person_marital_status');
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
        $this->hasColumn('marital_status_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));


        $this->index('UX_agPersonMaritalStatus', array(
             'fields' => 
             array(
              0 => 'person_id',
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

        $this->hasOne('agMaritalStatus', array(
             'local' => 'marital_status_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}