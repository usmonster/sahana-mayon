<?php

/**
 * BaseagPetCaretaker
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $pet_id
 * @property integer $entity_id
 * @property agPet $agPet
 * @property agEntity $agEntity
 * 
 * @method integer        getId()        Returns the current record's "id" value
 * @method integer        getPetId()     Returns the current record's "pet_id" value
 * @method integer        getEntityId()  Returns the current record's "entity_id" value
 * @method agPet          getAgPet()     Returns the current record's "agPet" value
 * @method agEntity       getAgEntity()  Returns the current record's "agEntity" value
 * @method agPetCaretaker setId()        Sets the current record's "id" value
 * @method agPetCaretaker setPetId()     Sets the current record's "pet_id" value
 * @method agPetCaretaker setEntityId()  Sets the current record's "entity_id" value
 * @method agPetCaretaker setAgPet()     Sets the current record's "agPet" value
 * @method agPetCaretaker setAgEntity()  Sets the current record's "agEntity" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPetCaretaker extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_pet_caretaker');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('pet_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('entity_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));


        $this->index('UX_agPetCaretaker', array(
             'fields' => 
             array(
              0 => 'pet_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agPet', array(
             'local' => 'pet_id',
             'foreign' => 'id'));

        $this->hasOne('agEntity', array(
             'local' => 'entity_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}