<?php

/**
 * agEventFacilityGroup filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagEventFacilityGroupFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'event_id'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => true)),
      'event_facility_group'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'facility_group_type_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityGroupType'), 'add_empty' => true)),
      'activation_sequence'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_facility_resource_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResource')),
    ));

    $this->setValidators(array(
      'event_id'                  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEvent'), 'column' => 'id')),
      'event_facility_group'      => new sfValidatorPass(array('required' => false)),
      'facility_group_type_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFacilityGroupType'), 'column' => 'id')),
      'activation_sequence'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_facility_resource_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResource', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_event_facility_group_filters[%s]');

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
      ->leftJoin($query->getRootAlias().'.agEventFacilityResource agEventFacilityResource')
      ->andWhereIn('agEventFacilityResource.facility_resource_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agEventFacilityGroup';
  }

  public function getFields()
  {
    return array(
      'id'                        => 'Number',
      'event_id'                  => 'ForeignKey',
      'event_facility_group'      => 'Text',
      'facility_group_type_id'    => 'ForeignKey',
      'activation_sequence'       => 'Number',
      'created_at'                => 'Date',
      'updated_at'                => 'Date',
      'ag_facility_resource_list' => 'ManyKey',
    );
  }
}
