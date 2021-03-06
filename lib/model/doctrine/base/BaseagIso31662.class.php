<?php

/**
 * BaseagIso31662
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $value
 * @property Doctrine_Collection $agCountry
 * @property Doctrine_Collection $agCountryMjAgIso31662
 * 
 * @method integer             getId()                    Returns the current record's "id" value
 * @method string              getValue()                 Returns the current record's "value" value
 * @method Doctrine_Collection getAgCountry()             Returns the current record's "agCountry" collection
 * @method Doctrine_Collection getAgCountryMjAgIso31662() Returns the current record's "agCountryMjAgIso31662" collection
 * @method agIso31662          setId()                    Sets the current record's "id" value
 * @method agIso31662          setValue()                 Sets the current record's "value" value
 * @method agIso31662          setAgCountry()             Sets the current record's "agCountry" collection
 * @method agIso31662          setAgCountryMjAgIso31662() Sets the current record's "agCountryMjAgIso31662" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagIso31662 extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_iso31662');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('value', 'string', 20, array(
             'unique' => true,
             'type' => 'string',
             'notnull' => true,
             'length' => 20,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agCountry', array(
             'refClass' => 'agCountryMjAgIso31662',
             'local' => 'iso_31662_id',
             'foreign' => 'country_id'));

        $this->hasMany('agCountryMjAgIso31662', array(
             'local' => 'id',
             'foreign' => 'iso_31662_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}