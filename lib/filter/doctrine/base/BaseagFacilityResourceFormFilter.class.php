<?php

/**
 * agFacilityResource filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagFacilityResourceFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'facility_resource_code'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'facility_id'                     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacility'), 'add_empty' => true)),
      'facility_resource_type_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceType'), 'add_empty' => true)),
      'facility_resource_status_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceStatus'), 'add_empty' => true)),
      'capacity'                        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_event_facility_group_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEventFacilityGroup')),
      'ag_scenario_facility_group_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agScenarioFacilityGroup')),
    ));

    $this->setValidators(array(
      'facility_resource_code'          => new sfValidatorPass(array('required' => false)),
      'facility_id'                     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFacility'), 'column' => 'id')),
      'facility_resource_type_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFacilityResourceType'), 'column' => 'id')),
      'facility_resource_status_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFacilityResourceStatus'), 'column' => 'id')),
      'capacity'                        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'                      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_event_facility_group_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEventFacilityGroup', 'required' => false)),
      'ag_scenario_facility_group_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agScenarioFacilityGroup', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_facility_resource_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgEventFacilityGroupListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agEventFacilityResource agEventFacilityResource')
      ->andWhereIn('agEventFacilityResource.event_facility_group_id', $values)
    ;
  }

  public function addAgScenarioFacilityGroupListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agScenarioFacilityResource agScenarioFacilityResource')
      ->andWhereIn('agScenarioFacilityResource.scenario_facility_group_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agFacilityResource';
  }

  public function getFields()
  {
    return array(
      'id'                              => 'Number',
      'facility_resource_code'          => 'Text',
      'facility_id'                     => 'ForeignKey',
      'facility_resource_type_id'       => 'ForeignKey',
      'facility_resource_status_id'     => 'ForeignKey',
      'capacity'                        => 'Number',
      'created_at'                      => 'Date',
      'updated_at'                      => 'Date',
      'ag_event_facility_group_list'    => 'ManyKey',
      'ag_scenario_facility_group_list' => 'ManyKey',
    );
  }
}
