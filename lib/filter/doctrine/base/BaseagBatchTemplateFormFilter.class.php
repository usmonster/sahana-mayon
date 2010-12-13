<?php

/**
 * agBatchTemplate filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagBatchTemplateFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'batch_template'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'                   => new sfWidgetFormFilterInput(),
      'app_display'                   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'reply_expected'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'                    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_message_template_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageTemplate')),
      'ag_mesage_reply_argument_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageReplyArgument')),
    ));

    $this->setValidators(array(
      'batch_template'                => new sfValidatorPass(array('required' => false)),
      'description'                   => new sfValidatorPass(array('required' => false)),
      'app_display'                   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'reply_expected'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'                    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_message_template_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageTemplate', 'required' => false)),
      'ag_mesage_reply_argument_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageReplyArgument', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_batch_template_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgMessageTemplateListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agMessageBatchTemplate agMessageBatchTemplate')
      ->andWhereIn('agMessageBatchTemplate.message_template_id', $values)
    ;
  }

  public function addAgMesageReplyArgumentListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agBatchTemplateReplyArgument.message_reply_argument_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agBatchTemplate';
  }

  public function getFields()
  {
    return array(
      'id'                            => 'Number',
      'batch_template'                => 'Text',
      'description'                   => 'Text',
      'app_display'                   => 'Boolean',
      'reply_expected'                => 'Boolean',
      'created_at'                    => 'Date',
      'updated_at'                    => 'Date',
      'ag_message_template_list'      => 'ManyKey',
      'ag_mesage_reply_argument_list' => 'ManyKey',
    );
  }
}
