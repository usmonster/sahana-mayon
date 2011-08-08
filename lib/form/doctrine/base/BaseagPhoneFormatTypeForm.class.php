<?php

/**
 * agPhoneFormatType form base class.
 *
 * @method agPhoneFormatType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPhoneFormatTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'phone_format_type'   => new sfWidgetFormInputText(),
      'app_display'         => new sfWidgetFormInputCheckbox(),
      'validation'          => new sfWidgetFormInputText(),
      'match_pattern'       => new sfWidgetFormInputText(),
      'replacement_pattern' => new sfWidgetFormInputText(),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'phone_format_type'   => new sfValidatorString(array('max_length' => 64)),
      'app_display'         => new sfValidatorBoolean(array('required' => false)),
      'validation'          => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'match_pattern'       => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'replacement_pattern' => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agPhoneFormatType', 'column' => array('phone_format_type'))),
        new sfValidatorDoctrineUnique(array('model' => 'agPhoneFormatType', 'column' => array('phone_format_type'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_phone_format_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPhoneFormatType';
  }

}
