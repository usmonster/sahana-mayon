<?php

/**
 * BaseagAddressValue
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $address_element_id
 * @property string $value
 * @property agAddressElement $agAddressElement
 * @property Doctrine_Collection $agAddress
 * @property Doctrine_Collection $agAddressAlias
 * @property Doctrine_Collection $agAddressMjAgAddressValue
 * 
 * @method integer             getId()                        Returns the current record's "id" value
 * @method integer             getAddressElementId()          Returns the current record's "address_element_id" value
 * @method string              getValue()                     Returns the current record's "value" value
 * @method agAddressElement    getAgAddressElement()          Returns the current record's "agAddressElement" value
 * @method Doctrine_Collection getAgAddress()                 Returns the current record's "agAddress" collection
 * @method Doctrine_Collection getAgAddressAlias()            Returns the current record's "agAddressAlias" collection
 * @method Doctrine_Collection getAgAddressMjAgAddressValue() Returns the current record's "agAddressMjAgAddressValue" collection
 * @method agAddressValue      setId()                        Sets the current record's "id" value
 * @method agAddressValue      setAddressElementId()          Sets the current record's "address_element_id" value
 * @method agAddressValue      setValue()                     Sets the current record's "value" value
 * @method agAddressValue      setAgAddressElement()          Sets the current record's "agAddressElement" value
 * @method agAddressValue      setAgAddress()                 Sets the current record's "agAddress" collection
 * @method agAddressValue      setAgAddressAlias()            Sets the current record's "agAddressAlias" collection
 * @method agAddressValue      setAgAddressMjAgAddressValue() Sets the current record's "agAddressMjAgAddressValue" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagAddressValue extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_address_value');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('address_element_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('value', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));


        $this->index('agAddressValue_addressElementId', array(
             'fields' => 
             array(
              0 => 'address_element_id',
             ),
             ));
        $this->index('agAddressValue_unq', array(
             'fields' => 
             array(
              0 => 'value',
              1 => 'address_element_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agAddressElement', array(
             'local' => 'address_element_id',
             'foreign' => 'id'));

        $this->hasMany('agAddress', array(
             'refClass' => 'agAddressMjAgAddressValue',
             'local' => 'address_value_id',
             'foreign' => 'address_id'));

        $this->hasMany('agAddressAlias', array(
             'local' => 'id',
             'foreign' => 'address_value_id'));

        $this->hasMany('agAddressMjAgAddressValue', array(
             'local' => 'id',
             'foreign' => 'address_value_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}