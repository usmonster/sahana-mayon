<?php

/**
 * BaseagAddressAlias
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $address_value_id
 * @property string $alias
 * @property agAddressValue $agAddressValue
 * 
 * @method integer        getId()               Returns the current record's "id" value
 * @method integer        getAddressValueId()   Returns the current record's "address_value_id" value
 * @method string         getAlias()            Returns the current record's "alias" value
 * @method agAddressValue getAgAddressValue()   Returns the current record's "agAddressValue" value
 * @method agAddressAlias setId()               Sets the current record's "id" value
 * @method agAddressAlias setAddressValueId()   Sets the current record's "address_value_id" value
 * @method agAddressAlias setAlias()            Sets the current record's "alias" value
 * @method agAddressAlias setAgAddressValue()   Sets the current record's "agAddressValue" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagAddressAlias extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_address_alias');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('address_value_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('alias', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));


        $this->index('agElementAlias_UX', array(
             'fields' => 
             array(
              0 => 'address_value_id',
              1 => 'alias',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agAddressValue', array(
             'local' => 'address_value_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}