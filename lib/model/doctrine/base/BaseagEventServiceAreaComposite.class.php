<?php

/**
 * BaseagEventServiceAreaComposite
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $event_service_area_id
 * @property integer $geo_id
 * @property agEventServiceArea $agEventServiceArea
 * @property agGeo $agGeo
 * 
 * @method integer                     getId()                    Returns the current record's "id" value
 * @method integer                     getEventServiceAreaId()    Returns the current record's "event_service_area_id" value
 * @method integer                     getGeoId()                 Returns the current record's "geo_id" value
 * @method agEventServiceArea          getAgEventServiceArea()    Returns the current record's "agEventServiceArea" value
 * @method agGeo                       getAgGeo()                 Returns the current record's "agGeo" value
 * @method agEventServiceAreaComposite setId()                    Sets the current record's "id" value
 * @method agEventServiceAreaComposite setEventServiceAreaId()    Sets the current record's "event_service_area_id" value
 * @method agEventServiceAreaComposite setGeoId()                 Sets the current record's "geo_id" value
 * @method agEventServiceAreaComposite setAgEventServiceArea()    Sets the current record's "agEventServiceArea" value
 * @method agEventServiceAreaComposite setAgGeo()                 Sets the current record's "agGeo" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEventServiceAreaComposite extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_event_service_area_composite');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('event_service_area_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('geo_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));


        $this->index('UX_agServiceAreaComposite', array(
             'fields' => 
             array(
              0 => 'event_service_area_id',
              1 => 'geo_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agEventServiceArea', array(
             'local' => 'event_service_area_id',
             'foreign' => 'id'));

        $this->hasOne('agGeo', array(
             'local' => 'geo_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}