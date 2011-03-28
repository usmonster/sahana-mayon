<?php

/**
 * BaseagStaff
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $person_id
 * @property integer $staff_status_id
 * @property agPerson $agPerson
 * @property agStaffStatus $agStaffStatus
 * @property Doctrine_Collection $agStaffResourceType
 * @property Doctrine_Collection $agStaffResource
 * @property Doctrine_Collection $agEventAudit
 * 
 * @method integer             getId()                  Returns the current record's "id" value
 * @method integer             getPersonId()            Returns the current record's "person_id" value
 * @method integer             getStaffStatusId()       Returns the current record's "staff_status_id" value
 * @method agPerson            getAgPerson()            Returns the current record's "agPerson" value
 * @method agStaffStatus       getAgStaffStatus()       Returns the current record's "agStaffStatus" value
 * @method Doctrine_Collection getAgStaffResourceType() Returns the current record's "agStaffResourceType" collection
 * @method Doctrine_Collection getAgStaffResource()     Returns the current record's "agStaffResource" collection
 * @method Doctrine_Collection getAgEventAudit()        Returns the current record's "agEventAudit" collection
 * @method agStaff             setId()                  Sets the current record's "id" value
 * @method agStaff             setPersonId()            Sets the current record's "person_id" value
 * @method agStaff             setStaffStatusId()       Sets the current record's "staff_status_id" value
 * @method agStaff             setAgPerson()            Sets the current record's "agPerson" value
 * @method agStaff             setAgStaffStatus()       Sets the current record's "agStaffStatus" value
 * @method agStaff             setAgStaffResourceType() Sets the current record's "agStaffResourceType" collection
 * @method agStaff             setAgStaffResource()     Sets the current record's "agStaffResource" collection
 * @method agStaff             setAgEventAudit()        Sets the current record's "agEventAudit" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagStaff extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_staff');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('person_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('staff_status_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));


        $this->index('agStaff_unq', array(
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

        $this->hasOne('agStaffStatus', array(
             'local' => 'staff_status_id',
             'foreign' => 'id'));

        $this->hasMany('agStaffResourceType', array(
             'refClass' => 'agStaffResource',
             'local' => 'staff_id',
             'foreign' => 'staff_resource_type_id'));

        $this->hasMany('agStaffResource', array(
             'local' => 'id',
             'foreign' => 'staff_id'));

        $this->hasMany('agEventAudit', array(
             'local' => 'id',
             'foreign' => 'updated_by'));

        $luceneable0 = new Luceneable(array(
             ));
        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($luceneable0);
        $this->actAs($timestampable0);
    }
}