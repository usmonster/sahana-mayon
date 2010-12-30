<?php

/**
 * agEmbeddedAgFacilityStaffResourceForm extends agFacilityStaffResourceForm.
 *
 * @method agFacilityStaffResource getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
class agEmbeddedAgFacilityStaffResourceForm extends agFacilityStaffResourceForm
{
  public function setup()
  {
    $this->setWidgets(array(
      //'id'                            => new sfWidgetFormInputHidden(),
      'scenario_facility_resource_id' => new sfWidgetFormInputHidden(),
      'staff_resource_type_id'        => new sfWidgetFormInputHidden(),
      'minimum_staff'                 => new sfWidgetFormInputText(array('label' => 'Min'), array('class' => 'inputGraySmall')),
      'maximum_staff'                 => new sfWidgetFormInputText(array('label' => 'Max'), array('class' => 'inputGraySmall')),
      //'created_at'                    => new sfWidgetFormDateTime(),
      //'updated_at'                    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      //'id'                            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'scenario_facility_resource_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityResource'))),
      'staff_resource_type_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'))),
      'minimum_staff'                 => new sfValidatorInteger(),
      'maximum_staff'                 => new sfValidatorInteger(),
      //'created_at'                    => new sfValidatorDateTime(),
      //'updated_at'                    => new sfValidatorDateTime(),
    ));
  }
}