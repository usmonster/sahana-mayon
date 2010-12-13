<?php

/**
 * BaseagBranch
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $branch
 * @property string $description
 * @property Doctrine_Collection $agOrganization
 * @property Doctrine_Collection $agOrganizationBranch
 * 
 * @method integer             getId()                   Returns the current record's "id" value
 * @method string              getBranch()               Returns the current record's "branch" value
 * @method string              getDescription()          Returns the current record's "description" value
 * @method Doctrine_Collection getAgOrganization()       Returns the current record's "agOrganization" collection
 * @method Doctrine_Collection getAgOrganizationBranch() Returns the current record's "agOrganizationBranch" collection
 * @method agBranch            setId()                   Sets the current record's "id" value
 * @method agBranch            setBranch()               Sets the current record's "branch" value
 * @method agBranch            setDescription()          Sets the current record's "description" value
 * @method agBranch            setAgOrganization()       Sets the current record's "agOrganization" collection
 * @method agBranch            setAgOrganizationBranch() Sets the current record's "agOrganizationBranch" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagBranch extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_branch');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('branch', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));


        $this->index('agBranch_unq', array(
             'fields' => 
             array(
              0 => 'branch',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agOrganization', array(
             'refClass' => 'agOrganizationBranch',
             'local' => 'branch_id',
             'foreign' => 'organization_id'));

        $this->hasMany('agOrganizationBranch', array(
             'local' => 'id',
             'foreign' => 'branch_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}