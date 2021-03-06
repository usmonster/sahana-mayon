<?php

/**
 * BaseagPetNote
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $pet_id
 * @property timestamp $time_stamp
 * @property integer $event_staff_id
 * @property gzip $pet_note
 * @property agPet $agPet
 * @property agEventStaff $agEventStaff
 * 
 * @method integer      getId()             Returns the current record's "id" value
 * @method integer      getPetId()          Returns the current record's "pet_id" value
 * @method timestamp    getTimeStamp()      Returns the current record's "time_stamp" value
 * @method integer      getEventStaffId()   Returns the current record's "event_staff_id" value
 * @method gzip         getPetNote()        Returns the current record's "pet_note" value
 * @method agPet        getAgPet()          Returns the current record's "agPet" value
 * @method agEventStaff getAgEventStaff()   Returns the current record's "agEventStaff" value
 * @method agPetNote    setId()             Sets the current record's "id" value
 * @method agPetNote    setPetId()          Sets the current record's "pet_id" value
 * @method agPetNote    setTimeStamp()      Sets the current record's "time_stamp" value
 * @method agPetNote    setEventStaffId()   Sets the current record's "event_staff_id" value
 * @method agPetNote    setPetNote()        Sets the current record's "pet_note" value
 * @method agPetNote    setAgPet()          Sets the current record's "agPet" value
 * @method agPetNote    setAgEventStaff()   Sets the current record's "agEventStaff" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPetNote extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_pet_note');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('pet_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('time_stamp', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => true,
             ));
        $this->hasColumn('event_staff_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('pet_note', 'gzip', null, array(
             'type' => 'gzip',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agPet', array(
             'local' => 'pet_id',
             'foreign' => 'id'));

        $this->hasOne('agEventStaff', array(
             'local' => 'event_staff_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}