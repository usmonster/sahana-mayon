<?php

/**
 * agScenarioShift form.
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agScenarioShiftForm extends BaseagScenarioShiftForm
{
  public function configure()
  {
    unset($this['created_at'],
          $this['updated_at']
         );
  }

  public function setup()
  {

    $this->setWidgets(array(
      'id'                                  => new sfWidgetFormInputHidden(),
      'scenario_facility_resource_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityResource'), 'add_empty' => false)),
      'staff_resource_type_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => false)),
      'task_id'                             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agTask'), 'add_empty' => false)),
      'task_length_minutes'                 => new sfWidgetFormInputText(),
      'break_length_minutes'                => new sfWidgetFormInputText(),
      'minutes_start_to_facility_activation'=> new sfWidgetFormInputText(),
      'minimum_staff'                       => new sfWidgetFormInputText(),
      'maximum_staff'                       => new sfWidgetFormInputText(),
      'staff_wave'                          => new sfWidgetFormInputText(),
      'shift_status'                        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftStatus'), 'add_empty' => false)),
      'deployment_algorithm'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agDeploymentAlgorithm'), 'add_empty' => false)),
      ));


    $this->setValidators(array(
      'id'                                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'scenario_facility_resource_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityResource'))),
      'staff_resource_type_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'))),
      'task_id'                             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agTask'))),
      'task_length_minutes'                 => new sfValidatorInteger(array('max' => 10)),
      'break_length_minutes'                => new sfValidatorInteger(array('max' => 10)),
      'minutes_start_to_facility_activation'=> new sfValidatorInteger(array('max' => 20)),
      'minimum_staff'                       => new sfValidatorInteger(array('max' => 5)),
      'maximum_staff'                       => new sfValidatorInteger(array('max' => 5)),
      'staff_wave'                          => new sfValidatorInteger(array('max' => 5)),
      'shift_status'                        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftStatus'))),
      'deployment_algorithm'                => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agDeploymentAlgorithm'))),
    ));

    $this->validatorSchema->setOption('allow_extra_fields', true);

    $this->widgetSchema->setNameFormat('ag_scenario_shift[%s]');

    $custDeco = new agWidgetFormSchemaFormatterShift($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    //$this->setupInheritance();
  }

}
