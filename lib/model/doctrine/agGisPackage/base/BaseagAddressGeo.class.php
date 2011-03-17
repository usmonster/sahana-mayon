<?php

/**
 * BaseagAddressGeo
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $address_id
 * @property integer $geo_id
 * @property integer $geo_match_score_id
 * @property agGeo $agGeo
 * @property agGeoMatchScore $agGeoMatchScore
 * @property agAddress $agAddress
 * 
 * @method integer         getId()                 Returns the current record's "id" value
 * @method integer         getAddressId()          Returns the current record's "address_id" value
 * @method integer         getGeoId()              Returns the current record's "geo_id" value
 * @method integer         getGeoMatchScoreId()    Returns the current record's "geo_match_score_id" value
 * @method agGeo           getAgGeo()              Returns the current record's "agGeo" value
 * @method agGeoMatchScore getAgGeoMatchScore()    Returns the current record's "agGeoMatchScore" value
 * @method agAddress       getAgAddress()          Returns the current record's "agAddress" value
 * @method agAddressGeo    setId()                 Sets the current record's "id" value
 * @method agAddressGeo    setAddressId()          Sets the current record's "address_id" value
 * @method agAddressGeo    setGeoId()              Sets the current record's "geo_id" value
 * @method agAddressGeo    setGeoMatchScoreId()    Sets the current record's "geo_match_score_id" value
 * @method agAddressGeo    setAgGeo()              Sets the current record's "agGeo" value
 * @method agAddressGeo    setAgGeoMatchScore()    Sets the current record's "agGeoMatchScore" value
 * @method agAddressGeo    setAgAddress()          Sets the current record's "agAddress" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagAddressGeo extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_address_geo');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('address_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('geo_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('geo_match_score_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));


        $this->index('UX_agAddressGeo', array(
             'fields' => 
             array(
              0 => 'address_id',
             ),
             'type' => 'unique',
             ));
        $this->index('IX_agAddressGeo_addrGeo', array(
             'fields' => 
             array(
              0 => 'address_id',
              1 => 'geo_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agGeo', array(
             'local' => 'geo_id',
             'foreign' => 'id'));

        $this->hasOne('agGeoMatchScore', array(
             'local' => 'geo_match_score_id',
             'foreign' => 'id'));

        $this->hasOne('agAddress', array(
             'local' => 'address_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}