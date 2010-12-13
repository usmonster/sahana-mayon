<?php

/**
 * agPetAllocationStatusType form base class.
 *
 * @method agPetAllocationStatusType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPetAllocationStatusTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                              => new sfWidgetFormInputHidden(),
      'pet_allocation_status_type'      => new sfWidgetFormInputText(),
      'pet_allocation_status_type_desc' => new sfWidgetFormInputText(),
      'being_serviced'                  => new sfWidgetFormInputCheckbox(),
      'created_at'                      => new sfWidgetFormDateTime(),
      'updated_at'                      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'pet_allocation_status_type'      => new sfValidatorString(array('max_length' => 32)),
      'pet_allocation_status_type_desc' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'being_serviced'                  => new sfValidatorBoolean(),
      'created_at'                      => new sfValidatorDateTime(),
      'updated_at'                      => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agPetAllocationStatusType', 'column' => array('pet_allocation_status_type')))
    );

    $this->widgetSchema->setNameFormat('ag_pet_allocation_status_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPetAllocationStatusType';
  }

}