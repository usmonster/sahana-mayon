<?php

/**
 * agScenarioFacilityResource filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagScenarioFacilityResourceFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'facility_resource_id'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResource'), 'add_empty' => true)),
      'scenario_facility_group_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityGroup'), 'add_empty' => true)),
      'facility_resource_allocation_status_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceAllocationStatus'), 'add_empty' => true)),
      'activation_sequence'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_staff_resource_type_list'            => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResourceType')),
    ));

    $this->setValidators(array(
      'facility_resource_id'                   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFacilityResource'), 'column' => 'id')),
      'scenario_facility_group_id'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agScenarioFacilityGroup'), 'column' => 'id')),
      'facility_resource_allocation_status_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFacilityResourceAllocationStatus'), 'column' => 'id')),
      'activation_sequence'                    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'                             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_staff_resource_type_list'            => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResourceType', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_scenario_facility_resource_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgStaffResourceTypeListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agFacilityStaffResource agFacilityStaffResource')
      ->andWhereIn('agFacilityStaffResource.staff_resource_type_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agScenarioFacilityResource';
  }

  public function getFields()
  {
    return array(
      'id'                                     => 'Number',
      'facility_resource_id'                   => 'ForeignKey',
      'scenario_facility_group_id'             => 'ForeignKey',
      'facility_resource_allocation_status_id' => 'ForeignKey',
      'activation_sequence'                    => 'Number',
      'created_at'                             => 'Date',
      'updated_at'                             => 'Date',
      'ag_staff_resource_type_list'            => 'ManyKey',
    );
  }
}
