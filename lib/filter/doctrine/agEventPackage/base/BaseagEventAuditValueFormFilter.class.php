<?php

/**
 * agEventAuditValue filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagEventAuditValueFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'event_audit_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventAuditSql'), 'add_empty' => true)),
      'edit_table'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'edit_field'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'value'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'event_audit_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEventAuditSql'), 'column' => 'id')),
      'edit_table'     => new sfValidatorPass(array('required' => false)),
      'edit_field'     => new sfValidatorPass(array('required' => false)),
      'value'          => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_event_audit_value_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventAuditValue';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'event_audit_id' => 'ForeignKey',
      'edit_table'     => 'Text',
      'edit_field'     => 'Text',
      'value'          => 'Text',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
    );
  }
}
