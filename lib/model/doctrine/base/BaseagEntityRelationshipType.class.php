<?php

/**
 * BaseagEntityRelationshipType
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $entity_relationship_type
 * @property string $entity_relationship_type_desc
 * @property Doctrine_Collection $agEntityRelationship
 * @property Doctrine_Collection $agEntityRelationshipTypeInverse
 * 
 * @method integer                  getId()                              Returns the current record's "id" value
 * @method string                   getEntityRelationshipType()          Returns the current record's "entity_relationship_type" value
 * @method string                   getEntityRelationshipTypeDesc()      Returns the current record's "entity_relationship_type_desc" value
 * @method Doctrine_Collection      getAgEntityRelationship()            Returns the current record's "agEntityRelationship" collection
 * @method Doctrine_Collection      getAgEntityRelationshipTypeInverse() Returns the current record's "agEntityRelationshipTypeInverse" collection
 * @method agEntityRelationshipType setId()                              Sets the current record's "id" value
 * @method agEntityRelationshipType setEntityRelationshipType()          Sets the current record's "entity_relationship_type" value
 * @method agEntityRelationshipType setEntityRelationshipTypeDesc()      Sets the current record's "entity_relationship_type_desc" value
 * @method agEntityRelationshipType setAgEntityRelationship()            Sets the current record's "agEntityRelationship" collection
 * @method agEntityRelationshipType setAgEntityRelationshipTypeInverse() Sets the current record's "agEntityRelationshipTypeInverse" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEntityRelationshipType extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_entity_relationship_type');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'length' => 2,
             ));
        $this->hasColumn('entity_relationship_type', 'string', 32, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 32,
             ));
        $this->hasColumn('entity_relationship_type_desc', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));


        $this->index('UX_agEntityRelationshipType', array(
             'fields' => 
             array(
              0 => 'entity_relationship_type',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agEntityRelationship', array(
             'local' => 'id',
             'foreign' => 'entity_relationship_type_id'));

        $this->hasMany('agEntityRelationshipTypeInverse', array(
             'local' => 'id',
             'foreign' => 'entity_relationship_type_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}