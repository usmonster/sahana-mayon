<?php

/**
 * agShiftTemplate filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagShiftTemplateFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'scenario_id'                          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenario'), 'add_empty' => true)),
      'facility_resource_type_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceType'), 'add_empty' => true)),
      'staff_resource_type_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => true)),
      'task_id'                              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agTask'), 'add_empty' => true)),
      'task_length_minutes'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'break_length_minutes'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'minutes_start_to_facility_activation' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'shift_repeats'                        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'max_staff_repeat_shifts'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'shift_status_id'                      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftStatus'), 'add_empty' => true)),
      'deployment_algorithm_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agDeploymentAlgorithm'), 'add_empty' => true)),
      'created_at'                           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'scenario_id'                          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agScenario'), 'column' => 'id')),
      'facility_resource_type_id'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFacilityResourceType'), 'column' => 'id')),
      'staff_resource_type_id'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agStaffResourceType'), 'column' => 'id')),
      'task_id'                              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agTask'), 'column' => 'id')),
      'task_length_minutes'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'break_length_minutes'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'minutes_start_to_facility_activation' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shift_repeats'                        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'max_staff_repeat_shifts'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shift_status_id'                      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agShiftStatus'), 'column' => 'id')),
      'deployment_algorithm_id'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agDeploymentAlgorithm'), 'column' => 'id')),
      'created_at'                           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_shift_template_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agShiftTemplate';
  }

  public function getFields()
  {
    return array(
      'id'                                   => 'Number',
      'scenario_id'                          => 'ForeignKey',
      'facility_resource_type_id'            => 'ForeignKey',
      'staff_resource_type_id'               => 'ForeignKey',
      'task_id'                              => 'ForeignKey',
      'task_length_minutes'                  => 'Number',
      'break_length_minutes'                 => 'Number',
      'minutes_start_to_facility_activation' => 'Number',
      'shift_repeats'                        => 'Number',
      'max_staff_repeat_shifts'              => 'Number',
      'shift_status_id'                      => 'ForeignKey',
      'deployment_algorithm_id'              => 'ForeignKey',
      'created_at'                           => 'Date',
      'updated_at'                           => 'Date',
    );
  }
}
