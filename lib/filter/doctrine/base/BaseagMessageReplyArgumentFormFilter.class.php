<?php

/**
 * agMessageReplyArgument filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagMessageReplyArgumentFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'reply_argument'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'            => new sfWidgetFormFilterInput(),
      'created_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_batch_template_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agBatchTemplate')),
      'ag_message_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessage')),
    ));

    $this->setValidators(array(
      'reply_argument'         => new sfValidatorPass(array('required' => false)),
      'description'            => new sfValidatorPass(array('required' => false)),
      'created_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_batch_template_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agBatchTemplate', 'required' => false)),
      'ag_message_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessage', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_message_reply_argument_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgBatchTemplateListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agBatchTemplateReplyArgument agBatchTemplateReplyArgument')
      ->andWhereIn('agBatchTemplateReplyArgument.batch_template_id', $values)
    ;
  }

  public function addAgMessageListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agMessageReply.message_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agMessageReplyArgument';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'reply_argument'         => 'Text',
      'description'            => 'Text',
      'created_at'             => 'Date',
      'updated_at'             => 'Date',
      'ag_batch_template_list' => 'ManyKey',
      'ag_message_list'        => 'ManyKey',
    );
  }
}
