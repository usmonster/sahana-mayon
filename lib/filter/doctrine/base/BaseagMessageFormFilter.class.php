<?php

/**
 * agMessage filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagMessageFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'message_batch_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageBatch'), 'add_empty' => true)),
      'recipient_id'                    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'), 'add_empty' => true)),
      'created_at'                      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_messsage_reply_argument_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageReplyArgument')),
      'ag_message_element_type_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageElementType')),
    ));

    $this->setValidators(array(
      'message_batch_id'                => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agMessageBatch'), 'column' => 'id')),
      'recipient_id'                    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEntity'), 'column' => 'id')),
      'created_at'                      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_messsage_reply_argument_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageReplyArgument', 'required' => false)),
      'ag_message_element_type_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageElementType', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_message_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgMesssageReplyArgumentListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agMessageReply agMessageReply')
      ->andWhereIn('agMessageReply.message_reply_argument_id', $values)
    ;
  }

  public function addAgMessageElementTypeListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agMessageElement agMessageElement')
      ->andWhereIn('agMessageElement.message_element_type_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agMessage';
  }

  public function getFields()
  {
    return array(
      'id'                              => 'Number',
      'message_batch_id'                => 'ForeignKey',
      'recipient_id'                    => 'ForeignKey',
      'created_at'                      => 'Date',
      'updated_at'                      => 'Date',
      'ag_messsage_reply_argument_list' => 'ManyKey',
      'ag_message_element_type_list'    => 'ManyKey',
    );
  }
}
