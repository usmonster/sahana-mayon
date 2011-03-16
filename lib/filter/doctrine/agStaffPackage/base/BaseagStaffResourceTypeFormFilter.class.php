<?php

/**
 * agStaffResourceType filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagStaffResourceTypeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'staff_resource_type'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'staff_resource_type_abbr'           => new sfWidgetFormFilterInput(),
      'description'                        => new sfWidgetFormFilterInput(),
      'app_display'                        => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'                         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_staff_list'                      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agStaff')),
      'ag_skill_list'                      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agSkill')),
      'ag_scenario_facility_resource_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agScenarioFacilityResource')),
    ));

    $this->setValidators(array(
      'staff_resource_type'                => new sfValidatorPass(array('required' => false)),
      'staff_resource_type_abbr'           => new sfValidatorPass(array('required' => false)),
      'description'                        => new sfValidatorPass(array('required' => false)),
      'app_display'                        => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'                         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_staff_list'                      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaff', 'required' => false)),
      'ag_skill_list'                      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agSkill', 'required' => false)),
      'ag_scenario_facility_resource_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agScenarioFacilityResource', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_staff_resource_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgStaffListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agStaffResource agStaffResource')
      ->andWhereIn('agStaffResource.staff_id', $values)
    ;
  }

  public function addAgSkillListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agStaffResourceTypeProvision agStaffResourceTypeProvision')
      ->andWhereIn('agStaffResourceTypeProvision.skill_id', $values)
    ;
  }

  public function addAgScenarioFacilityResourceListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agFacilityStaffResource.scenario_facility_resource_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agStaffResourceType';
  }

  public function getFields()
  {
    return array(
      'id'                                 => 'Number',
      'staff_resource_type'                => 'Text',
      'staff_resource_type_abbr'           => 'Text',
      'description'                        => 'Text',
      'app_display'                        => 'Boolean',
      'created_at'                         => 'Date',
      'updated_at'                         => 'Date',
      'ag_staff_list'                      => 'ManyKey',
      'ag_skill_list'                      => 'ManyKey',
      'ag_scenario_facility_resource_list' => 'ManyKey',
    );
  }
}
