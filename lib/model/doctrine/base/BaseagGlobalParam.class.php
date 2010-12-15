<?php

/**
 * BaseagGlobalParam
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $host_id
 * @property string $datapoint
 * @property string $value
 * @property agHost $agHost
 * 
 * @method integer       getId()        Returns the current record's "id" value
 * @method integer       getHostId()    Returns the current record's "host_id" value
 * @method string        getDatapoint() Returns the current record's "datapoint" value
 * @method string        getValue()     Returns the current record's "value" value
 * @method agHost        getAgHost()    Returns the current record's "agHost" value
 * @method agGlobalParam setId()        Sets the current record's "id" value
 * @method agGlobalParam setHostId()    Sets the current record's "host_id" value
 * @method agGlobalParam setDatapoint() Sets the current record's "datapoint" value
 * @method agGlobalParam setValue()     Sets the current record's "value" value
 * @method agGlobalParam setAgHost()    Sets the current record's "agHost" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagGlobalParam extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_global_param');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('host_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('datapoint', 'string', 128, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 128,
             ));
        $this->hasColumn('value', 'string', 128, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 128,
             ));


        $this->index('agGlobalParam_unq', array(
             'fields' => 
             array(
              0 => 'host_id',
              1 => 'datapoint',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agHost', array(
             'local' => 'host_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}