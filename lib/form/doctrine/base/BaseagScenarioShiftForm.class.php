<?php

/**
 * agScenarioShift form base class.
 *
 * @method agScenarioShift getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagScenarioShiftForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                   => new sfWidgetFormInputHidden(),
      'scenario_facility_resource_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityResource'), 'add_empty' => false)),
      'staff_resource_type_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => false)),
      'task_id'                              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agTask'), 'add_empty' => false)),
      'task_length_minutes'                  => new sfWidgetFormInputText(),
      'break_length_minutes'                 => new sfWidgetFormInputText(),
      'minutes_start_to_facility_activation' => new sfWidgetFormInputText(),
      'minimum_staff'                        => new sfWidgetFormInputText(),
      'maximum_staff'                        => new sfWidgetFormInputText(),
      'staff_wave'                           => new sfWidgetFormInputText(),
      'shift_status_id'                      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftStatus'), 'add_empty' => false)),
      'deployment_algorithm_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agDeploymentAlgorithm'), 'add_empty' => false)),
      'originator_id'                        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftTemplate'), 'add_empty' => true)),
      'created_at'                           => new sfWidgetFormDateTime(),
      'updated_at'                           => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'scenario_facility_resource_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityResource'))),
      'staff_resource_type_id'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'))),
      'task_id'                              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agTask'))),
      'task_length_minutes'                  => new sfValidatorInteger(),
      'break_length_minutes'                 => new sfValidatorInteger(),
      'minutes_start_to_facility_activation' => new sfValidatorInteger(),
      'minimum_staff'                        => new sfValidatorInteger(),
      'maximum_staff'                        => new sfValidatorInteger(),
      'staff_wave'                           => new sfValidatorInteger(),
      'shift_status_id'                      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftStatus'))),
      'deployment_algorithm_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agDeploymentAlgorithm'))),
      'originator_id'                        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftTemplate'), 'required' => false)),
      'created_at'                           => new sfValidatorDateTime(),
      'updated_at'                           => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('ag_scenario_shift[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agScenarioShift';
  }

}