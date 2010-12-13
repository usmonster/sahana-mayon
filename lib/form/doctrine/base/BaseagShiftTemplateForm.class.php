<?php

/**
 * agShiftTemplate form base class.
 *
 * @method agShiftTemplate getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagShiftTemplateForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                   => new sfWidgetFormInputHidden(),
      'shift_template'                       => new sfWidgetFormInputText(),
      'description'                          => new sfWidgetFormInputText(),
      'scenario_id'                          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenario'), 'add_empty' => false)),
      'facility_resource_type_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceType'), 'add_empty' => false)),
      'staff_resource_type_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => false)),
      'task_id'                              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agTask'), 'add_empty' => false)),
      'task_length_minutes'                  => new sfWidgetFormInputText(),
      'break_length_minutes'                 => new sfWidgetFormInputText(),
      'minutes_start_to_facility_activation' => new sfWidgetFormInputText(),
      'shift_repeats'                        => new sfWidgetFormInputText(),
      'max_staff_repeat_shifts'              => new sfWidgetFormInputText(),
      'shift_status_id'                      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftStatus'), 'add_empty' => false)),
      'deployment_algorithm_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agDeploymentAlgorithm'), 'add_empty' => false)),
      'created_at'                           => new sfWidgetFormDateTime(),
      'updated_at'                           => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'shift_template'                       => new sfValidatorString(array('max_length' => 64)),
      'description'                          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'scenario_id'                          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agScenario'))),
      'facility_resource_type_id'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceType'))),
      'staff_resource_type_id'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'))),
      'task_id'                              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agTask'))),
      'task_length_minutes'                  => new sfValidatorInteger(),
      'break_length_minutes'                 => new sfValidatorInteger(),
      'minutes_start_to_facility_activation' => new sfValidatorInteger(),
      'shift_repeats'                        => new sfValidatorInteger(),
      'max_staff_repeat_shifts'              => new sfValidatorInteger(),
      'shift_status_id'                      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftStatus'))),
      'deployment_algorithm_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agDeploymentAlgorithm'))),
      'created_at'                           => new sfValidatorDateTime(),
      'updated_at'                           => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('ag_shift_template[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agShiftTemplate';
  }

}