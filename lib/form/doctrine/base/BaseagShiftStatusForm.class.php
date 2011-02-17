<?php

/**
 * agShiftStatus form base class.
 *
 * @method agShiftStatus getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagShiftStatusForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'shift_status' => new sfWidgetFormInputText(),
      'description'  => new sfWidgetFormInputText(),
      'standby'      => new sfWidgetFormInputCheckbox(),
      'disabled'     => new sfWidgetFormInputCheckbox(),
      'active'       => new sfWidgetFormInputCheckbox(),
      'assigned'     => new sfWidgetFormInputCheckbox(),
      'created_at'   => new sfWidgetFormDateTime(),
      'updated_at'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'shift_status' => new sfValidatorString(array('max_length' => 32)),
      'description'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'standby'      => new sfValidatorBoolean(array('required' => false)),
      'disabled'     => new sfValidatorBoolean(array('required' => false)),
      'active'       => new sfValidatorBoolean(array('required' => false)),
      'assigned'     => new sfValidatorBoolean(array('required' => false)),
      'created_at'   => new sfValidatorDateTime(),
      'updated_at'   => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agShiftStatus', 'column' => array('shift_status')))
    );

    $this->widgetSchema->setNameFormat('ag_shift_status[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agShiftStatus';
  }

}