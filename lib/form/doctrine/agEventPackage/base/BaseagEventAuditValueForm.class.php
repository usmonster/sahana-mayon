<?php

/**
 * agEventAuditValue form base class.
 *
 * @method agEventAuditValue getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventAuditValueForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'event_audit_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventAuditSql'), 'add_empty' => false)),
      'edit_table'     => new sfWidgetFormInputText(),
      'edit_field'     => new sfWidgetFormInputText(),
      'value'          => new sfWidgetFormInputText(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_audit_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventAuditSql'))),
      'edit_table'     => new sfValidatorString(array('max_length' => 64)),
      'edit_field'     => new sfValidatorString(array('max_length' => 64)),
      'value'          => new sfValidatorPass(),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventAuditValue', 'column' => array('event_audit_id', 'edit_table', 'edit_field')))
    );

    $this->widgetSchema->setNameFormat('ag_event_audit_value[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventAuditValue';
  }

}