<?php

/**
 * BaseagEntityRelationship
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $entity_id1
 * @property integer $entity_id2
 * @property integer $entity_relationship_type_id
 * @property boolean $by_marriage
 * @property boolean $ex_relation
 * @property agEntity $entity1
 * @property agEntity $entity2
 * @property agEntityRelationshipType $agEntityRelationshipType
 * 
 * @method integer                  getId()                          Returns the current record's "id" value
 * @method integer                  getEntityId1()                   Returns the current record's "entity_id1" value
 * @method integer                  getEntityId2()                   Returns the current record's "entity_id2" value
 * @method integer                  getEntityRelationshipTypeId()    Returns the current record's "entity_relationship_type_id" value
 * @method boolean                  getByMarriage()                  Returns the current record's "by_marriage" value
 * @method boolean                  getExRelation()                  Returns the current record's "ex_relation" value
 * @method agEntity                 getEntity1()                     Returns the current record's "entity1" value
 * @method agEntity                 getEntity2()                     Returns the current record's "entity2" value
 * @method agEntityRelationshipType getAgEntityRelationshipType()    Returns the current record's "agEntityRelationshipType" value
 * @method agEntityRelationship     setId()                          Sets the current record's "id" value
 * @method agEntityRelationship     setEntityId1()                   Sets the current record's "entity_id1" value
 * @method agEntityRelationship     setEntityId2()                   Sets the current record's "entity_id2" value
 * @method agEntityRelationship     setEntityRelationshipTypeId()    Sets the current record's "entity_relationship_type_id" value
 * @method agEntityRelationship     setByMarriage()                  Sets the current record's "by_marriage" value
 * @method agEntityRelationship     setExRelation()                  Sets the current record's "ex_relation" value
 * @method agEntityRelationship     setEntity1()                     Sets the current record's "entity1" value
 * @method agEntityRelationship     setEntity2()                     Sets the current record's "entity2" value
 * @method agEntityRelationship     setAgEntityRelationshipType()    Sets the current record's "agEntityRelationshipType" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEntityRelationship extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_entity_relationship');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('entity_id1', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('entity_id2', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('entity_relationship_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('by_marriage', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('ex_relation', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             'notnull' => true,
             ));


        $this->index('UX_agEntityRelationship', array(
             'fields' => 
             array(
              0 => 'entity_id1',
              1 => 'entity_id2',
              2 => 'entity_relationship_type_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agEntity as entity1', array(
             'local' => 'entity_id1',
             'foreign' => 'id'));

        $this->hasOne('agEntity as entity2', array(
             'local' => 'entity_id2',
             'foreign' => 'id'));

        $this->hasOne('agEntityRelationshipType', array(
             'local' => 'entity_relationship_type_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}