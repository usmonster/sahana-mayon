<?php

/**
 * agEventStatusType form base class.
 *
 * @method agEventStatusType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventStatusTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'event_status_type' => new sfWidgetFormInputText(),
      'description'       => new sfWidgetFormInputText(),
      'active'            => new sfWidgetFormInputCheckbox(),
      'app_display'       => new sfWidgetFormInputCheckbox(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_status_type' => new sfValidatorString(array('max_length' => 30)),
      'description'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'active'            => new sfValidatorBoolean(array('required' => false)),
      'app_display'       => new sfValidatorBoolean(array('required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventStatusType', 'column' => array('event_status_type')))
    );

    $this->widgetSchema->setNameFormat('ag_event_status_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventStatusType';
  }

}