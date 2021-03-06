<?php

/**
 * agScenarioFacilityDistribution filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagScenarioFacilityDistributionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'scenario_service_area_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioServiceArea'), 'add_empty' => true)),
      'scenario_facility_group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityGroup'), 'add_empty' => true)),
      'created_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'scenario_service_area_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agScenarioServiceArea'), 'column' => 'id')),
      'scenario_facility_group_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agScenarioFacilityGroup'), 'column' => 'id')),
      'created_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_scenario_facility_distribution_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agScenarioFacilityDistribution';
  }

  public function getFields()
  {
    return array(
      'id'                         => 'Number',
      'scenario_service_area_id'   => 'ForeignKey',
      'scenario_facility_group_id' => 'ForeignKey',
      'created_at'                 => 'Date',
      'updated_at'                 => 'Date',
    );
  }
}
