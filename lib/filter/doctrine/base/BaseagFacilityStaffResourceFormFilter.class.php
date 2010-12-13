<?php

/**
 * agFacilityStaffResource filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagFacilityStaffResourceFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'scenario_facility_resource_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityResource'), 'add_empty' => true)),
      'staff_resource_type_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => true)),
      'minimum_staff'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'maximum_staff'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'scenario_facility_resource_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agScenarioFacilityResource'), 'column' => 'id')),
      'staff_resource_type_id'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agStaffResourceType'), 'column' => 'id')),
      'minimum_staff'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'maximum_staff'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'                    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_facility_staff_resource_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agFacilityStaffResource';
  }

  public function getFields()
  {
    return array(
      'id'                            => 'Number',
      'scenario_facility_resource_id' => 'ForeignKey',
      'staff_resource_type_id'        => 'ForeignKey',
      'minimum_staff'                 => 'Number',
      'maximum_staff'                 => 'Number',
      'created_at'                    => 'Date',
      'updated_at'                    => 'Date',
    );
  }
}
