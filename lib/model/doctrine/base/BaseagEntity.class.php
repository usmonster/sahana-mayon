<?php

/**
 * BaseagEntity
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property Doctrine_Collection $agImportEntityHash
 * @property Doctrine_Collection $agClientGroup
 * @property Doctrine_Collection $agPhoneContact
 * @property Doctrine_Collection $agEmailContact
 * @property Doctrine_Collection $agEntityPhoneContact
 * @property Doctrine_Collection $agEntityEmailContact
 * @property Doctrine_Collection $agEntityAddressContact
 * @property Doctrine_Collection $agMessageBatch
 * @property Doctrine_Collection $agMessage
 * @property Doctrine_Collection $agOrganization
 * @property Doctrine_Collection $agPerson
 * @property Doctrine_Collection $agPetCaretaker
 * @property Doctrine_Collection $agSite
 * @property Doctrine_Collection $agEntityRelationship
 * 
 * @method integer             getId()                     Returns the current record's "id" value
 * @method Doctrine_Collection getAgImportEntityHash()     Returns the current record's "agImportEntityHash" collection
 * @method Doctrine_Collection getAgClientGroup()          Returns the current record's "agClientGroup" collection
 * @method Doctrine_Collection getAgPhoneContact()         Returns the current record's "agPhoneContact" collection
 * @method Doctrine_Collection getAgEmailContact()         Returns the current record's "agEmailContact" collection
 * @method Doctrine_Collection getAgEntityPhoneContact()   Returns the current record's "agEntityPhoneContact" collection
 * @method Doctrine_Collection getAgEntityEmailContact()   Returns the current record's "agEntityEmailContact" collection
 * @method Doctrine_Collection getAgEntityAddressContact() Returns the current record's "agEntityAddressContact" collection
 * @method Doctrine_Collection getAgMessageBatch()         Returns the current record's "agMessageBatch" collection
 * @method Doctrine_Collection getAgMessage()              Returns the current record's "agMessage" collection
 * @method Doctrine_Collection getAgOrganization()         Returns the current record's "agOrganization" collection
 * @method Doctrine_Collection getAgPerson()               Returns the current record's "agPerson" collection
 * @method Doctrine_Collection getAgPetCaretaker()         Returns the current record's "agPetCaretaker" collection
 * @method Doctrine_Collection getAgSite()                 Returns the current record's "agSite" collection
 * @method Doctrine_Collection getAgEntityRelationship()   Returns the current record's "agEntityRelationship" collection
 * @method agEntity            setId()                     Sets the current record's "id" value
 * @method agEntity            setAgImportEntityHash()     Sets the current record's "agImportEntityHash" collection
 * @method agEntity            setAgClientGroup()          Sets the current record's "agClientGroup" collection
 * @method agEntity            setAgPhoneContact()         Sets the current record's "agPhoneContact" collection
 * @method agEntity            setAgEmailContact()         Sets the current record's "agEmailContact" collection
 * @method agEntity            setAgEntityPhoneContact()   Sets the current record's "agEntityPhoneContact" collection
 * @method agEntity            setAgEntityEmailContact()   Sets the current record's "agEntityEmailContact" collection
 * @method agEntity            setAgEntityAddressContact() Sets the current record's "agEntityAddressContact" collection
 * @method agEntity            setAgMessageBatch()         Sets the current record's "agMessageBatch" collection
 * @method agEntity            setAgMessage()              Sets the current record's "agMessage" collection
 * @method agEntity            setAgOrganization()         Sets the current record's "agOrganization" collection
 * @method agEntity            setAgPerson()               Sets the current record's "agPerson" collection
 * @method agEntity            setAgPetCaretaker()         Sets the current record's "agPetCaretaker" collection
 * @method agEntity            setAgSite()                 Sets the current record's "agSite" collection
 * @method agEntity            setAgEntityRelationship()   Sets the current record's "agEntityRelationship" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEntity extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_entity');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agImportEntityHash', array(
             'local' => 'id',
             'foreign' => 'entity_id'));

        $this->hasMany('agClientGroup', array(
             'local' => 'id',
             'foreign' => 'entity_id'));

        $this->hasMany('agPhoneContact', array(
             'refClass' => 'agEntityPhoneContact',
             'local' => 'entity_id',
             'foreign' => 'phone_contact_id'));

        $this->hasMany('agEmailContact', array(
             'refClass' => 'agEntityEmailContact',
             'local' => 'entity_id',
             'foreign' => 'email_contact_id'));

        $this->hasMany('agEntityPhoneContact', array(
             'local' => 'id',
             'foreign' => 'entity_id'));

        $this->hasMany('agEntityEmailContact', array(
             'local' => 'id',
             'foreign' => 'entity_id'));

        $this->hasMany('agEntityAddressContact', array(
             'local' => 'id',
             'foreign' => 'entity_id'));

        $this->hasMany('agMessageBatch', array(
             'refClass' => 'agMessage',
             'local' => 'recipient_id',
             'foreign' => 'message_batch_id'));

        $this->hasMany('agMessage', array(
             'local' => 'id',
             'foreign' => 'recipient_id'));

        $this->hasMany('agOrganization', array(
             'local' => 'id',
             'foreign' => 'entity_id'));

        $this->hasMany('agPerson', array(
             'local' => 'id',
             'foreign' => 'entity_id'));

        $this->hasMany('agPetCaretaker', array(
             'local' => 'id',
             'foreign' => 'entity_id'));

        $this->hasMany('agSite', array(
             'local' => 'id',
             'foreign' => 'entity_id'));

        $this->hasMany('agEntityRelationship', array(
             'local' => 'id',
             'foreign' => 'entity_id1'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}