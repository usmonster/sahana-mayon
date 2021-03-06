<?php

/**
 * BaseagResidentialStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $residential_status
 * @property string $description
 * @property boolean $app_display
 * @property Doctrine_Collection $agPerson
 * @property Doctrine_Collection $agCountry
 * @property Doctrine_Collection $agPersonResidentialStatus
 * 
 * @method integer             getId()                        Returns the current record's "id" value
 * @method string              getResidentialStatus()         Returns the current record's "residential_status" value
 * @method string              getDescription()               Returns the current record's "description" value
 * @method boolean             getAppDisplay()                Returns the current record's "app_display" value
 * @method Doctrine_Collection getAgPerson()                  Returns the current record's "agPerson" collection
 * @method Doctrine_Collection getAgCountry()                 Returns the current record's "agCountry" collection
 * @method Doctrine_Collection getAgPersonResidentialStatus() Returns the current record's "agPersonResidentialStatus" collection
 * @method agResidentialStatus setId()                        Sets the current record's "id" value
 * @method agResidentialStatus setResidentialStatus()         Sets the current record's "residential_status" value
 * @method agResidentialStatus setDescription()               Sets the current record's "description" value
 * @method agResidentialStatus setAppDisplay()                Sets the current record's "app_display" value
 * @method agResidentialStatus setAgPerson()                  Sets the current record's "agPerson" collection
 * @method agResidentialStatus setAgCountry()                 Sets the current record's "agCountry" collection
 * @method agResidentialStatus setAgPersonResidentialStatus() Sets the current record's "agPersonResidentialStatus" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagResidentialStatus extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_residential_status');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('residential_status', 'string', 30, array(
             'unique' => true,
             'type' => 'string',
             'notnull' => true,
             'length' => 30,
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
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agPerson', array(
             'refClass' => 'agPersonResidentialStatus',
             'local' => 'residential_status_id',
             'foreign' => 'person_id'));

        $this->hasMany('agCountry', array(
             'refClass' => 'agPersonResidentialStatus',
             'local' => 'residential_status_id',
             'foreign' => 'country_id'));

        $this->hasMany('agPersonResidentialStatus', array(
             'local' => 'id',
             'foreign' => 'residential_status_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}