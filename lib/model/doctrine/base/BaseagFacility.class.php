<?php

/**
 * BaseagFacility
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $site_id
 * @property string $facility_name
 * @property string $facility_code
 * @property agSite $agSite
 * @property Doctrine_Collection $agFacilityResource
 * 
 * @method integer             getId()                     Returns the current record's "id" value
 * @method integer             getSiteId()                 Returns the current record's "site_id" value
 * @method string              getFacilityName()           Returns the current record's "facility_name" value
 * @method string              getFacilityCode()           Returns the current record's "facility_code" value
 * @method agSite              getAgSite()                 Returns the current record's "agSite" value
 * @method Doctrine_Collection getAgFacilityResourceType() Returns the current record's "agFacilityResourceType" collection
 * @method Doctrine_Collection getAgFacilityResource()     Returns the current record's "agFacilityResource" collection
 * @method agFacility          setId()                     Sets the current record's "id" value
 * @method agFacility          setSiteId()                 Sets the current record's "site_id" value
 * @method agFacility          setFacilityName()           Sets the current record's "facility_name" value
 * @method agFacility          setFacilityCode()           Sets the current record's "facility_code" value
 * @method agFacility          setAgSite()                 Sets the current record's "agSite" value
 * @method agFacility          setAgFacilityResourceType() Sets the current record's "agFacilityResourceType" collection
 * @method agFacility          setAgFacilityResource()     Sets the current record's "agFacilityResource" collectionType
 * @property Doctrine_Collection $agFacilityResource
 * 
 * @method integer             getId()                     Returns the current record's "id" value
 * @method integer             getSiteId()                 Returns the current record's "site_id" value
 * @method string              getFacilityName()           Returns the current record's "facility_name" value
 * @method string              getFacilityCode()           Returns the current record's "facility_code" value
 * @method agSite              getAgSite()                 Returns the current record's "agSite" value
 * @method Doctrine_Collection getAgFacilityResourceType() Returns the current record's "agFacilityResourceType" collection
 * @method Doctrine_Collection getAgFacilityResource()     Returns the current record's "agFacilityResource" collection
 * @method agFacility          setId()                     Sets the current record's "id" value
 * @method agFacility          setSiteId()                 Sets the current record's "site_id" value
 * @method agFacility          setFacilityName()           Sets the current record's "facility_name" value
 * @method agFacility          setFacilityCode()           Sets the current record's "facility_code" value
 * @method agFacility          setAgSite()                 Sets the current record's "agSite" value
 * @method agFacility          setAgFacilityResourceType() Sets the current record's "agFacilityResourceType" collection
 * @method agFacility          setAgFacilityResource()     Sets the current record's "agFacilityResource" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagFacility extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_facility');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('site_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('facility_name', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('facility_code', 'string', 10, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 10,
             ));


        $this->index('agFacility_siteId_unq', array(
             'fields' => 
             array(
              0 => 'site_id',
             ),
             'type' => 'unique',
             ));
        $this->index('agFacility_facilityName_unq', array(
             'fields' => 
             array(
              0 => 'facility_name',
             ),
             'type' => 'unique',
             ));
        $this->index('agFacility_facilityCode_unq', array(
             'fields' => 
             array(
              0 => 'facility_code',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agSite', array(
             'local' => 'site_id',
             'foreign' => 'id'));

        $this->hasMany('agFacilityResourceType', array(
             'refClass' => 'agFacilityResource',
             'local' => 'facility_id',
             'foreign' => 'facility_resource_type_id'));

        $this->hasMany('agFacilityResource', array(
             'local' => 'id',
             'foreign' => 'facility_id'));

        $luceneable0 = new Luceneable(array(
             ));
        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($luceneable0);
        $this->actAs($timestampable0);
    }
}