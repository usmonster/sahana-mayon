<?php

/**
 * agEventStaffSignIn form base class.
 *
 * @method agEventStaffSignIn getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventStaffSignInForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'event_staff_shift_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventStaffShift'), 'add_empty' => false)),
      'signin'               => new sfWidgetFormDateTime(),
      'signout'              => new sfWidgetFormDateTime(),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_at'           => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_staff_shift_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventStaffShift'))),
      'signin'               => new sfValidatorDateTime(),
      'signout'              => new sfValidatorDateTime(array('required' => false)),
      'created_at'           => new sfValidatorDateTime(),
      'updated_at'           => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventStaffSignIn', 'column' => array('event_staff_shift_id', 'signin')))
    );

    $this->widgetSchema->setNameFormat('ag_event_staff_sign_in[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventStaffSignIn';
  }

}
