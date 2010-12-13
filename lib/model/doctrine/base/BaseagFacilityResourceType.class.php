<?php

/**
 * BaseagFacilityResourceType
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $facility_resource_type
 * @property string $facility_resource_type_abbr
 * @property string $description
 * @property boolean $app_display
 * @property Doctrine_Collection $agFacility
 * @property Doctrine_Collection $agFacilityResource
 * @property Doctrine_Collection $agDefaultScenarioFacilityResourceType
 * @property Doctrine_Collection $agShiftTemplate
 * 
 * @method integer                getId()                                    Returns the current record's "id" value
 * @method string                 getFacilityResourceType()                  Returns the current record's "facility_resource_type" value
 * @method string                 getFacilityResourceTypeAbbr()              Returns the current record's "facility_resource_type_abbr" value
 * @method string                 getDescription()                           Returns the current record's "description" value
 * @method boolean                getAppDisplay()                            Returns the current record's "app_display" value
 * @method Doctrine_Collection    getAgFacility()                            Returns the current record's "agFacility" collection
 * @method Doctrine_Collection    getAgFacilityResource()                    Returns the current record's "agFacilityResource" collection
 * @method Doctrine_Collection    getAgDefaultScenarioFacilityResourceType() Returns the current record's "agDefaultScenarioFacilityResourceType" collection
 * @method Doctrine_Collection    getAgShiftTemplate()                       Returns the current record's "agShiftTemplate" collection
 * @method agFacilityResourceType setId()                                    Sets the current record's "id" value
 * @method agFacilityResourceType setFacilityResourceType()                  Sets the current record's "facility_resource_type" value
 * @method agFacilityResourceType setFacilityResourceTypeAbbr()              Sets the current record's "facility_resource_type_abbr" value
 * @method agFacilityResourceType setDescription()                           Sets the current record's "description" value
 * @method agFacilityResourceType setAppDisplay()                            Sets the current record's "app_display" value
 * @method agFacilityResourceType setAgFacility()                            Sets the current record's "agFacility" collection
 * @method agFacilityResourceType setAgFacilityResource()                    Sets the current record's "agFacilityResource" collection
 * @method agFacilityResourceType setAgDefaultScenarioFacilityResourceType() Sets the current record's "agDefaultScenarioFacilityResourceType" collection
 * @method agFacilityResourceType setAgShiftTemplate()                       Sets the current record's "agShiftTemplate" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagFacilityResourceType extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_facility_resource_type');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('facility_resource_type', 'string', 30, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 30,
             ));
        $this->hasColumn('facility_resource_type_abbr', 'string', 10, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 10,
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('app_display', 'boolean', null, array(
             'default' => 1,
             'type' => 'boolean',
             'notnull' => true,
             ));


        $this->index('agFacilityResourceType_unq', array(
             'fields' => 
             array(
              0 => 'facility_resource_type',
             ),
             'type' => 'unique',
             ));
        $this->index('UX_agFacilityResourceTypeAbbr', array(
             'fields' => 
             array(
              0 => 'facility_resource_type_abbr',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agFacility', array(
             'refClass' => 'agFacilityResource',
             'local' => 'facility_resource_type_id',
             'foreign' => 'facility_id'));

        $this->hasMany('agFacilityResource', array(
             'local' => 'id',
             'foreign' => 'facility_resource_type_id'));

        $this->hasMany('agDefaultScenarioFacilityResourceType', array(
             'local' => 'id',
             'foreign' => 'facility_resource_type_id'));

        $this->hasMany('agShiftTemplate', array(
             'local' => 'id',
             'foreign' => 'facility_resource_type_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}