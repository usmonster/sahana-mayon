<?php

/**
 * agBatchTemplateReplyArgument filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagBatchTemplateReplyArgumentFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'batch_template_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agBatchTemplate'), 'add_empty' => true)),
      'message_reply_argument_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageReplyArgument'), 'add_empty' => true)),
      'argument_sequence'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'batch_template_id'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agBatchTemplate'), 'column' => 'id')),
      'message_reply_argument_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agMessageReplyArgument'), 'column' => 'id')),
      'argument_sequence'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_batch_template_reply_argument_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agBatchTemplateReplyArgument';
  }

  public function getFields()
  {
    return array(
      'id'                        => 'Number',
      'batch_template_id'         => 'ForeignKey',
      'message_reply_argument_id' => 'ForeignKey',
      'argument_sequence'         => 'Number',
      'created_at'                => 'Date',
      'updated_at'                => 'Date',
    );
  }
}
