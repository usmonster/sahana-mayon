<?php

/**
 * agEventStaffShift form base class.
 *
 * @method agEventStaffShift getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventStaffShiftForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'event_staff_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventStaff'), 'add_empty' => false)),
      'event_shift_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventShift'), 'add_empty' => false)),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_staff_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventStaff'))),
      'event_shift_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventShift'))),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventStaffShift', 'column' => array('event_staff_id', 'event_shift_id')))
    );

    $this->widgetSchema->setNameFormat('ag_event_staff_shift[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventStaffShift';
  }

}