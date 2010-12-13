<?php

/**
 * agMessageElementType filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagMessageElementTypeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'message_element_type'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'              => new sfWidgetFormFilterInput(),
      'app_display'              => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_message_type_list'     => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageType')),
      'ag_message_template_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageTemplate')),
      'ag_message_list'          => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessage')),
    ));

    $this->setValidators(array(
      'message_element_type'     => new sfValidatorPass(array('required' => false)),
      'description'              => new sfValidatorPass(array('required' => false)),
      'app_display'              => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_message_type_list'     => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageType', 'required' => false)),
      'ag_message_template_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageTemplate', 'required' => false)),
      'ag_message_list'          => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessage', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_message_element_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgMessageTypeListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agDefaultMessageTypeElement agDefaultMessageTypeElement')
      ->andWhereIn('agDefaultMessageTypeElement.message_type_id', $values)
    ;
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
      ->leftJoin($query->getRootAlias().'.agMessageTemplateElement agMessageTemplateElement')
      ->andWhereIn('agMessageTemplateElement.message_template_id', $values)
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
      ->leftJoin($query->getRootAlias().'.agMessageElement agMessageElement')
      ->andWhereIn('agMessageElement.message_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agMessageElementType';
  }

  public function getFields()
  {
    return array(
      'id'                       => 'Number',
      'message_element_type'     => 'Text',
      'description'              => 'Text',
      'app_display'              => 'Boolean',
      'created_at'               => 'Date',
      'updated_at'               => 'Date',
      'ag_message_type_list'     => 'ManyKey',
      'ag_message_template_list' => 'ManyKey',
      'ag_message_list'          => 'ManyKey',
    );
  }
}
