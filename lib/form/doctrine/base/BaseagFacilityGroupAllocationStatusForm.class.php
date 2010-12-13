<?php

/**
 * agFacilityGroupAllocationStatus form base class.
 *
 * @method agFacilityGroupAllocationStatus getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagFacilityGroupAllocationStatusForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                               => new sfWidgetFormInputHidden(),
      'facility_group_allocation_status' => new sfWidgetFormInputText(),
      'description'                      => new sfWidgetFormInputText(),
      'allocatable'                      => new sfWidgetFormInputCheckbox(),
      'standby'                          => new sfWidgetFormInputCheckbox(),
      'active'                           => new sfWidgetFormInputCheckbox(),
      'app_display'                      => new sfWidgetFormInputCheckbox(),
      'created_at'                       => new sfWidgetFormDateTime(),
      'updated_at'                       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'facility_group_allocation_status' => new sfValidatorString(array('max_length' => 30)),
      'description'                      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'allocatable'                      => new sfValidatorBoolean(array('required' => false)),
      'standby'                          => new sfValidatorBoolean(array('required' => false)),
      'active'                           => new sfValidatorBoolean(array('required' => false)),
      'app_display'                      => new sfValidatorBoolean(array('required' => false)),
      'created_at'                       => new sfValidatorDateTime(),
      'updated_at'                       => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agFacilityGroupAllocationStatus', 'column' => array('facility_group_allocation_status')))
    );

    $this->widgetSchema->setNameFormat('ag_facility_group_allocation_status[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agFacilityGroupAllocationStatus';
  }

}