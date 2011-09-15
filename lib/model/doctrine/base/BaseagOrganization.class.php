<?php

/**
 * BaseagOrganization
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $organization
 * @property string $description
 * @property integer $entity_id
 * @property agEntity $agEntity
 * @property Doctrine_Collection $agBranch
 * @property Doctrine_Collection $agStaffResource
 * @property Doctrine_Collection $agOrganizationBranch
 * @property Doctrine_Collection $agCertification
 * 
 * @method integer             getId()                   Returns the current record's "id" value
 * @method string              getOrganization()         Returns the current record's "organization" value
 * @method string              getDescription()          Returns the current record's "description" value
 * @method integer             getEntityId()             Returns the current record's "entity_id" value
 * @method agEntity            getAgEntity()             Returns the current record's "agEntity" value
 * @method Doctrine_Collection getAgBranch()             Returns the current record's "agBranch" collection
 * @method Doctrine_Collection getAgStaffResource()      Returns the current record's "agStaffResource" collection
 * @method Doctrine_Collection getAgOrganizationBranch() Returns the current record's "agOrganizationBranch" collection
 * @method Doctrine_Collection getAgCertification()      Returns the current record's "agCertification" collection
 * @method agOrganization      setId()                   Sets the current record's "id" value
 * @method agOrganization      setOrganization()         Sets the current record's "organization" value
 * @method agOrganization      setDescription()          Sets the current record's "description" value
 * @method agOrganization      setEntityId()             Sets the current record's "entity_id" value
 * @method agOrganization      setAgEntity()             Sets the current record's "agEntity" value
 * @method agOrganization      setAgBranch()             Sets the current record's "agBranch" collection
 * @method agOrganization      setAgStaffResource()      Sets the current record's "agStaffResource" collection
 * @method agOrganization      setAgOrganizationBranch() Sets the current record's "agOrganizationBranch" collection
 * @method agOrganization      setAgCertification()      Sets the current record's "agCertification" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagOrganization extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_organization');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('organization', 'string', 128, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 128,
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('entity_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));


        $this->index('agOrganization_unq', array(
             'fields' => 
             array(
              0 => 'organization',
             ),
             'type' => 'unique',
             ));
        $this->index('agOrganization_enitty_unq', array(
             'fields' => 
             array(
              0 => 'entity_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agEntity', array(
             'local' => 'entity_id',
             'foreign' => 'id'));

        $this->hasMany('agBranch', array(
             'refClass' => 'agOrganizationBranch',
             'local' => 'organization_id',
             'foreign' => 'branch_id'));

        $this->hasMany('agStaffResource', array(
             'local' => 'id',
             'foreign' => 'organization_id'));

        $this->hasMany('agOrganizationBranch', array(
             'local' => 'id',
             'foreign' => 'organization_id'));

        $this->hasMany('agCertification', array(
             'local' => 'id',
             'foreign' => 'certifying_organization_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $luceneable0 = new Luceneable(array(
             'fields' => 
             array(
              'organization' => 'unstored',
             ),
             ));
        $this->actAs($timestampable0);
        $this->actAs($luceneable0);
    }
}