<?php

/**
 * agClientAllocationStatus filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagClientAllocationStatusFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'client_id'                        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClient'), 'add_empty' => true)),
      'time_stamp'                       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'event_facility_resource_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityResource'), 'add_empty' => true)),
      'client_allocation_status_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClientAllocationStatusType'), 'add_empty' => true)),
      'created_at'                       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'client_id'                        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agClient'), 'column' => 'id')),
      'time_stamp'                       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'event_facility_resource_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEventFacilityResource'), 'column' => 'id')),
      'client_allocation_status_type_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agClientAllocationStatusType'), 'column' => 'id')),
      'created_at'                       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_client_allocation_status_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agClientAllocationStatus';
  }

  public function getFields()
  {
    return array(
      'id'                               => 'Number',
      'client_id'                        => 'ForeignKey',
      'time_stamp'                       => 'Date',
      'event_facility_resource_id'       => 'ForeignKey',
      'client_allocation_status_type_id' => 'ForeignKey',
      'created_at'                       => 'Date',
      'updated_at'                       => 'Date',
    );
  }
}
