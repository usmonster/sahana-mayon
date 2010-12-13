<?php

/**
 * BaseagBatchTemplateReplyArgument
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $batch_template_id
 * @property integer $message_reply_argument_id
 * @property integer $argument_sequence
 * @property agBatchTemplate $agBatchTemplate
 * @property agMessageReplyArgument $agMessageReplyArgument
 * 
 * @method integer                      getId()                        Returns the current record's "id" value
 * @method integer                      getBatchTemplateId()           Returns the current record's "batch_template_id" value
 * @method integer                      getMessageReplyArgumentId()    Returns the current record's "message_reply_argument_id" value
 * @method integer                      getArgumentSequence()          Returns the current record's "argument_sequence" value
 * @method agBatchTemplate              getAgBatchTemplate()           Returns the current record's "agBatchTemplate" value
 * @method agMessageReplyArgument       getAgMessageReplyArgument()    Returns the current record's "agMessageReplyArgument" value
 * @method agBatchTemplateReplyArgument setId()                        Sets the current record's "id" value
 * @method agBatchTemplateReplyArgument setBatchTemplateId()           Sets the current record's "batch_template_id" value
 * @method agBatchTemplateReplyArgument setMessageReplyArgumentId()    Sets the current record's "message_reply_argument_id" value
 * @method agBatchTemplateReplyArgument setArgumentSequence()          Sets the current record's "argument_sequence" value
 * @method agBatchTemplateReplyArgument setAgBatchTemplate()           Sets the current record's "agBatchTemplate" value
 * @method agBatchTemplateReplyArgument setAgMessageReplyArgument()    Sets the current record's "agMessageReplyArgument" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagBatchTemplateReplyArgument extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_batch_template_reply_argument');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('batch_template_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('message_reply_argument_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('argument_sequence', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 1,
             ));


        $this->index('agBatchTemplateReplyArgument_unq', array(
             'fields' => 
             array(
              0 => 'batch_template_id',
              1 => 'message_reply_argument_id',
             ),
             'type' => 'unique',
             ));
        $this->index('agBatchTemplateReplyArgument_sequence_unq', array(
             'fields' => 
             array(
              0 => 'batch_template_id',
              1 => 'argument_sequence',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agBatchTemplate', array(
             'local' => 'batch_template_id',
             'foreign' => 'id'));

        $this->hasOne('agMessageReplyArgument', array(
             'local' => 'message_reply_argument_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}