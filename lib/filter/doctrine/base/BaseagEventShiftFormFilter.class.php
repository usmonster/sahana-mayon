<?php

/**
 * agEventShift filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagEventShiftFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'event_facility_resource_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityResource'), 'add_empty' => true)),
      'staff_resource_type_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => true)),
      'minimum_staff'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'maximum_staff'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'start_time'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'end_time'                   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'break_start'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'break_end'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'task_id'                    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftTask'), 'add_empty' => true)),
      'shift_status_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftStatus'), 'add_empty' => true)),
      'staff_wave'                 => new sfWidgetFormFilterInput(),
      'deployment_algorithm_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agDeploymentAlgorithm'), 'add_empty' => true)),
      'created_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_staff_event_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEventStaff')),
    ));

    $this->setValidators(array(
      'event_facility_resource_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEventFacilityResource'), 'column' => 'id')),
      'staff_resource_type_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agStaffResourceType'), 'column' => 'id')),
      'minimum_staff'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'maximum_staff'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'start_time'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'end_time'                   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'break_start'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'break_end'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'task_id'                    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agShiftTask'), 'column' => 'id')),
      'shift_status_id'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agShiftStatus'), 'column' => 'id')),
      'staff_wave'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'deployment_algorithm_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agDeploymentAlgorithm'), 'column' => 'id')),
      'created_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_staff_event_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEventStaff', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_event_shift_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgStaffEventListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agEventStaffShift agEventStaffShift')
      ->andWhereIn('agEventStaffShift.event_staff_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agEventShift';
  }

  public function getFields()
  {
    return array(
      'id'                         => 'Number',
      'event_facility_resource_id' => 'ForeignKey',
      'staff_resource_type_id'     => 'ForeignKey',
      'minimum_staff'              => 'Number',
      'maximum_staff'              => 'Number',
      'start_time'                 => 'Date',
      'end_time'                   => 'Date',
      'break_start'                => 'Date',
      'break_end'                  => 'Date',
      'task_id'                    => 'ForeignKey',
      'shift_status_id'            => 'ForeignKey',
      'staff_wave'                 => 'Number',
      'deployment_algorithm_id'    => 'ForeignKey',
      'created_at'                 => 'Date',
      'updated_at'                 => 'Date',
      'ag_staff_event_list'        => 'ManyKey',
    );
  }
}
