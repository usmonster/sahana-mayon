<?php

/**
 * agFacilityResourceStatus form base class.
 *
 * @method agFacilityResourceStatus getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagFacilityResourceStatusForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'facility_resource_status' => new sfWidgetFormInputText(),
      'is_available'             => new sfWidgetFormInputCheckbox(),
      'created_at'               => new sfWidgetFormDateTime(),
      'updated_at'               => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'facility_resource_status' => new sfValidatorString(array('max_length' => 40)),
      'is_available'             => new sfValidatorBoolean(array('required' => false)),
      'created_at'               => new sfValidatorDateTime(),
      'updated_at'               => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agFacilityResourceStatus', 'column' => array('facility_resource_status')))
    );

    $this->widgetSchema->setNameFormat('ag_facility_resource_status[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agFacilityResourceStatus';
  }

}
