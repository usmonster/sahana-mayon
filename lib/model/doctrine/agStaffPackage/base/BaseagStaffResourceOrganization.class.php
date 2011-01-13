<?php

/**
 * BaseagStaffResourceOrganization
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $staff_resource_id
 * @property integer $organization_id
 * @property agStaffResource $agStaffResource
 * @property agOrganization $agOrganization
 * 
 * @method integer                     getId()                Returns the current record's "id" value
 * @method integer                     getStaffResourceId()   Returns the current record's "staff_resource_id" value
 * @method integer                     getOrganizationId()    Returns the current record's "organization_id" value
 * @method agStaffResource             getAgStaffResource()   Returns the current record's "agStaffResource" value
 * @method agOrganization              getAgOrganization()    Returns the current record's "agOrganization" value
 * @method agStaffResourceOrganization setId()                Sets the current record's "id" value
 * @method agStaffResourceOrganization setStaffResourceId()   Sets the current record's "staff_resource_id" value
 * @method agStaffResourceOrganization setOrganizationId()    Sets the current record's "organization_id" value
 * @method agStaffResourceOrganization setAgStaffResource()   Sets the current record's "agStaffResource" value
 * @method agStaffResourceOrganization setAgOrganization()    Sets the current record's "agOrganization" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagStaffResourceOrganization extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_staff_resource_organization');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'length' => 5,
             ));
        $this->hasColumn('staff_resource_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('organization_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));


        $this->index('UX_agStaffResourceOrganization', array(
             'fields' => 
             array(
              0 => 'staff_resource_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agStaffResource', array(
             'local' => 'staff_resource_id',
             'foreign' => 'id'));

        $this->hasOne('agOrganization', array(
             'local' => 'organization_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}