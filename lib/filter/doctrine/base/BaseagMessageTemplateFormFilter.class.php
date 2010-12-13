<?php

/**
 * agMessageTemplate filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagMessageTemplateFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'message_template'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'message_type_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessage'), 'add_empty' => true)),
      'created_at'                   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_message_element_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageElementType')),
      'ag_batch_template_list'       => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agBatchTemplate')),
    ));

    $this->setValidators(array(
      'message_template'             => new sfValidatorPass(array('required' => false)),
      'message_type_id'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agMessage'), 'column' => 'id')),
      'created_at'                   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_message_element_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageElementType', 'required' => false)),
      'ag_batch_template_list'       => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agBatchTemplate', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_message_template_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
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
      ->leftJoin($query->getRootAlias().'.agMessageTemplateElement agMessageTemplateElement')
      ->andWhereIn('agMessageTemplateElement.message_element_type_id', $values)
    ;
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
      ->leftJoin($query->getRootAlias().'.agMessageBatchTemplate agMessageBatchTemplate')
      ->andWhereIn('agMessageBatchTemplate.batch_template_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agMessageTemplate';
  }

  public function getFields()
  {
    return array(
      'id'                           => 'Number',
      'message_template'             => 'Text',
      'message_type_id'              => 'ForeignKey',
      'created_at'                   => 'Date',
      'updated_at'                   => 'Date',
      'ag_message_element_type_list' => 'ManyKey',
      'ag_batch_template_list'       => 'ManyKey',
    );
  }
}
