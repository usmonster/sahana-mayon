<?php

/**
 * BaseagDefaultMessageTypeElement
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $message_type_id
 * @property integer $message_element_type_id
 * @property agMessageType $agMessageType
 * @property agMessageElementType $agMessageElementType
 * 
 * @method integer                     getId()                      Returns the current record's "id" value
 * @method integer                     getMessageTypeId()           Returns the current record's "message_type_id" value
 * @method integer                     getMessageElementTypeId()    Returns the current record's "message_element_type_id" value
 * @method agMessageType               getAgMessageType()           Returns the current record's "agMessageType" value
 * @method agMessageElementType        getAgMessageElementType()    Returns the current record's "agMessageElementType" value
 * @method agDefaultMessageTypeElement setId()                      Sets the current record's "id" value
 * @method agDefaultMessageTypeElement setMessageTypeId()           Sets the current record's "message_type_id" value
 * @method agDefaultMessageTypeElement setMessageElementTypeId()    Sets the current record's "message_element_type_id" value
 * @method agDefaultMessageTypeElement setAgMessageType()           Sets the current record's "agMessageType" value
 * @method agDefaultMessageTypeElement setAgMessageElementType()    Sets the current record's "agMessageElementType" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagDefaultMessageTypeElement extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_default_message_type_element');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('message_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('message_element_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));


        $this->index('agDefaultMessageTypeElement_unq', array(
             'fields' => 
             array(
              0 => 'message_type_id',
              1 => 'message_element_type_id',
             ),
             'type' => 'unique',
             ));
        $this->index('agDefaultMessageTypeElement_messageTypeid', array(
             'fields' => 
             array(
              0 => 'message_type_id',
             ),
             ));
        $this->index('agDefaultMessageTypeElement_messageElementTypeId', array(
             'fields' => 
             array(
              0 => 'message_element_type_id',
             ),
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agMessageType', array(
             'local' => 'message_type_id',
             'foreign' => 'id'));

        $this->hasOne('agMessageElementType', array(
             'local' => 'message_element_type_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}