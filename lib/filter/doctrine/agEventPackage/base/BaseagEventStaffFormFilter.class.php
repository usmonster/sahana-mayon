<?php

/**
 * agEventStaff filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagEventStaffFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'event_id'                     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => true)),
      'staff_resource_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResource'), 'add_empty' => true)),
      'deployment_weight'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_event_facility_shift_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEventShift')),
    ));

    $this->setValidators(array(
      'event_id'                     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEvent'), 'column' => 'id')),
      'staff_resource_id'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agStaffResource'), 'column' => 'id')),
      'deployment_weight'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'                   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_event_facility_shift_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEventShift', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_event_staff_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgEventFacilityShiftListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agEventStaffShift.event_shift_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agEventStaff';
  }

  public function getFields()
  {
    return array(
      'id'                           => 'Number',
      'event_id'                     => 'ForeignKey',
      'staff_resource_id'            => 'ForeignKey',
      'deployment_weight'            => 'Number',
      'created_at'                   => 'Date',
      'updated_at'                   => 'Date',
      'ag_event_facility_shift_list' => 'ManyKey',
    );
  }
}
