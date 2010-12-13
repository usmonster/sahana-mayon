<?php

/**
 * BaseagMessage
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $message_batch_id
 * @property integer $recipient_id
 * @property agEntity $agEntity
 * @property agMessageBatch $agMessageBatch
 * @property Doctrine_Collection $agMesssageReplyArgument
 * @property Doctrine_Collection $agMessageElement
 * 
 * @method integer             getId()                      Returns the current record's "id" value
 * @method integer             getMessageBatchId()          Returns the current record's "message_batch_id" value
 * @method integer             getRecipientId()             Returns the current record's "recipient_id" value
 * @method agEntity            getAgEntity()                Returns the current record's "agEntity" value
 * @method agMessageBatch      getAgMessageBatch()          Returns the current record's "agMessageBatch" value
 * @method Doctrine_Collection getAgMesssageReplyArgument() Returns the current record's "agMesssageReplyArgument" collection
 * @method Doctrine_Collection getAgMessageElementType()    Returns the current record's "agMessageElementType" collection
 * @method Doctrine_Collection getAgMessageDetail()         Returns the current record's "agMessageDetail" collection
 * @method Doctrine_Collection getAgMessageReply()          Returns the current record's "agMessageReply" collection
 * @method Doctrine_Collection getAgMessageElement()        Returns the current record's "agMessageElement" collection
 * @method agMessage           setId()                      Sets the current record's "id" value
 * @method agMessage           setMessageBatchId()          Sets the current record's "message_batch_id" value
 * @method agMessage           setRecipientId()             Sets the current record's "recipient_id" value
 * @method agMessage           setAgEntity()                Sets the current record's "agEntity" value
 * @method agMessage           setAgMessageBatch()          Sets the current record's "agMessageBatch" value
 * @method agMessage           setAgMesssageReplyArgument() Sets the current record's "agMesssageReplyArgument" collection
 * @method agMessage           setAgMessageElementType()    Sets the current record's "agMessageElementType" collection
 * @method agMessage           setAgMessageDetail()         Sets the current record's "agMessageDetail" collection
 * @method agMessage           setAgMessageReply()          Sets the current record's "agMessageReply" collection
 * @method agMessage           setAgMessageElement()        Sets the current record's "agMessageElement" collectionType
 * @property Doctrine_Collection $agMessageDetail
 * @property Doctrine_Collection $agMessageReply
 * @property Doctrine_Collection $agMessageElement
 * 
 * @method integer             getId()                      Returns the current record's "id" value
 * @method integer             getMessageBatchId()          Returns the current record's "message_batch_id" value
 * @method integer             getRecipientId()             Returns the current record's "recipient_id" value
 * @method agEntity            getAgEntity()                Returns the current record's "agEntity" value
 * @method agMessageBatch      getAgMessageBatch()          Returns the current record's "agMessageBatch" value
 * @method Doctrine_Collection getAgMesssageReplyArgument() Returns the current record's "agMesssageReplyArgument" collection
 * @method Doctrine_Collection getAgMessageElementType()    Returns the current record's "agMessageElementType" collection
 * @method Doctrine_Collection getAgMessageDetail()         Returns the current record's "agMessageDetail" collection
 * @method Doctrine_Collection getAgMessageReply()          Returns the current record's "agMessageReply" collection
 * @method Doctrine_Collection getAgMessageElement()        Returns the current record's "agMessageElement" collection
 * @method agMessage           setId()                      Sets the current record's "id" value
 * @method agMessage           setMessageBatchId()          Sets the current record's "message_batch_id" value
 * @method agMessage           setRecipientId()             Sets the current record's "recipient_id" value
 * @method agMessage           setAgEntity()                Sets the current record's "agEntity" value
 * @method agMessage           setAgMessageBatch()          Sets the current record's "agMessageBatch" value
 * @method agMessage           setAgMesssageReplyArgument() Sets the current record's "agMesssageReplyArgument" collection
 * @method agMessage           setAgMessageElementType()    Sets the current record's "agMessageElementType" collection
 * @method agMessage           setAgMessageDetail()         Sets the current record's "agMessageDetail" collection
 * @method agMessage           setAgMessageReply()          Sets the current record's "agMessageReply" collection
 * @method agMessage           setAgMessageElement()        Sets the current record's "agMessageElement" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagMessage extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_message');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('message_batch_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('recipient_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));


        $this->index('UX_agMessage', array(
             'fields' => 
             array(
              0 => 'message_batch_id',
              1 => 'recipient_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agEntity', array(
             'local' => 'recipient_id',
             'foreign' => 'id'));

        $this->hasOne('agMessageBatch', array(
             'local' => 'message_batch_id',
             'foreign' => 'id'));

        $this->hasMany('agMessageReplyArgument as agMesssageReplyArgument', array(
             'refClass' => 'agMessageReply',
             'local' => 'message_id',
             'foreign' => 'message_reply_argument_id'));

        $this->hasMany('agMessageElementType', array(
             'refClass' => 'agMessageElement',
             'local' => 'message_id',
             'foreign' => 'message_element_type_id'));

        $this->hasMany('agMessageDetail', array(
             'local' => 'id',
             'foreign' => 'message_id'));

        $this->hasMany('agMessageReply', array(
             'local' => 'id',
             'foreign' => 'message_id'));

        $this->hasMany('agMessageElement', array(
             'local' => 'id',
             'foreign' => 'message_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}