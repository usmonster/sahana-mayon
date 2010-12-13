<?php

/**
 * BaseagCountryMjAgIso31662
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $country_id
 * @property integer $iso_31662_id
 * @property agCountry $agCountry
 * @property agIso31662 $agIso31662
 * 
 * @method integer               getCountryId()    Returns the current record's "country_id" value
 * @method integer               getIso31662Id()   Returns the current record's "iso_31662_id" value
 * @method agCountry             getAgCountry()    Returns the current record's "agCountry" value
 * @method agIso31662            getAgIso31662()   Returns the current record's "agIso31662" value
 * @method agCountryMjAgIso31662 setCountryId()    Sets the current record's "country_id" value
 * @method agCountryMjAgIso31662 setIso31662Id()   Sets the current record's "iso_31662_id" value
 * @method agCountryMjAgIso31662 setAgCountry()    Sets the current record's "agCountry" value
 * @method agCountryMjAgIso31662 setAgIso31662()   Sets the current record's "agIso31662" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagCountryMjAgIso31662 extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_country_mj_ag_iso31662');
        $this->hasColumn('country_id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'length' => 2,
             ));
        $this->hasColumn('iso_31662_id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'length' => 2,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agCountry', array(
             'local' => 'country_id',
             'foreign' => 'id'));

        $this->hasOne('agIso31662', array(
             'local' => 'iso_31662_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}