<?php

/**
 * agMessageBatch filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagMessageBatchFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'event_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => true)),
      'batch_template_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agBatchTemplate'), 'add_empty' => true)),
      'execution_time'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'created_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_entity_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEntity')),
    ));

    $this->setValidators(array(
      'event_id'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEvent'), 'column' => 'id')),
      'batch_template_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agBatchTemplate'), 'column' => 'id')),
      'execution_time'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'created_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_entity_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEntity', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_message_batch_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgEntityListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agMessage agMessage')
      ->andWhereIn('agMessage.recipient_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agMessageBatch';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'event_id'          => 'ForeignKey',
      'batch_template_id' => 'ForeignKey',
      'execution_time'    => 'Date',
      'created_at'        => 'Date',
      'updated_at'        => 'Date',
      'ag_entity_list'    => 'ManyKey',
    );
  }
}
