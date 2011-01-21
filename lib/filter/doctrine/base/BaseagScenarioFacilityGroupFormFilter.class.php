<?php

/**
 * agScenarioFacilityGroup filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagScenarioFacilityGroupFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'scenario_id'                         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenario'), 'add_empty' => true)),
      'scenario_facility_group'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'facility_group_type_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityGroupType'), 'add_empty' => true)),
      'facility_group_allocation_status_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityGroupAllocationStatus'), 'add_empty' => true)),
      'activation_sequence'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'ag_facility_resource_list'           => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResource')),
    ));

    $this->setValidators(array(
      'scenario_id'                         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agScenario'), 'column' => 'id')),
      'scenario_facility_group'             => new sfValidatorPass(array('required' => false)),
      'facility_group_type_id'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFacilityGroupType'), 'column' => 'id')),
      'facility_group_allocation_status_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFacilityGroupAllocationStatus'), 'column' => 'id')),
      'activation_sequence'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ag_facility_resource_list'           => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResource', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_scenario_facility_group_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgFacilityResourceListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agScenarioFacilityResource.facility_resource_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agScenarioFacilityGroup';
  }

  public function getFields()
  {
    return array(
      'id'                                  => 'Number',
      'scenario_id'                         => 'ForeignKey',
      'scenario_facility_group'             => 'Text',
      'facility_group_type_id'              => 'ForeignKey',
      'facility_group_allocation_status_id' => 'ForeignKey',
      'activation_sequence'                 => 'Number',
      'ag_facility_resource_list'           => 'ManyKey',
    );
  }
}
