<?php

/**
 * BaseagMessageTemplateElement
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $message_template_id
 * @property integer $message_element_type_id
 * @property blob $value
 * @property agMessageTemplate $agMessageTemplate
 * @property agMessageElementType $agMessageElementType
 * 
 * @method integer                  getId()                      Returns the current record's "id" value
 * @method integer                  getMessageTemplateId()       Returns the current record's "message_template_id" value
 * @method integer                  getMessageElementTypeId()    Returns the current record's "message_element_type_id" value
 * @method blob                     getValue()                   Returns the current record's "value" value
 * @method agMessageTemplate        getAgMessageTemplate()       Returns the current record's "agMessageTemplate" value
 * @method agMessageElementType     getAgMessageElementType()    Returns the current record's "agMessageElementType" value
 * @method agMessageTemplateElement setId()                      Sets the current record's "id" value
 * @method agMessageTemplateElement setMessageTemplateId()       Sets the current record's "message_template_id" value
 * @method agMessageTemplateElement setMessageElementTypeId()    Sets the current record's "message_element_type_id" value
 * @method agMessageTemplateElement setValue()                   Sets the current record's "value" value
 * @method agMessageTemplateElement setAgMessageTemplate()       Sets the current record's "agMessageTemplate" value
 * @method agMessageTemplateElement setAgMessageElementType()    Sets the current record's "agMessageElementType" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagMessageTemplateElement extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_message_template_element');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('message_template_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('message_element_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('value', 'blob', null, array(
             'type' => 'blob',
             'notnull' => true,
             ));


        $this->index('agMessageTemplateElement_unq', array(
             'fields' => 
             array(
              0 => 'message_template_id',
              1 => 'message_element_type_id',
             ),
             'type' => 'unique',
             ));
        $this->index('agMessageTemplateElement_messageTemplateId', array(
             'fields' => 
             array(
              0 => 'message_template_id',
             ),
             ));
        $this->index('agMessageTemplateElement_messageElementTypeId', array(
             'fields' => 
             array(
              0 => 'message_element_type_id',
             ),
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agMessageTemplate', array(
             'local' => 'message_template_id',
             'foreign' => 'id'));

        $this->hasOne('agMessageElementType', array(
             'local' => 'message_element_type_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}