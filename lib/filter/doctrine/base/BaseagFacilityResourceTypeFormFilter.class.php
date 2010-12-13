<?php

/**
 * agFacilityResourceType filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagFacilityResourceTypeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'facility_resource_type'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'facility_resource_type_abbr' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'                 => new sfWidgetFormFilterInput(),
      'app_display'                 => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_facility_list'            => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agFacility')),
    ));

    $this->setValidators(array(
      'facility_resource_type'      => new sfValidatorPass(array('required' => false)),
      'facility_resource_type_abbr' => new sfValidatorPass(array('required' => false)),
      'description'                 => new sfValidatorPass(array('required' => false)),
      'app_display'                 => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_facility_list'            => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agFacility', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_facility_resource_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgFacilityListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agFacilityResource agFacilityResource')
      ->andWhereIn('agFacilityResource.facility_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agFacilityResourceType';
  }

  public function getFields()
  {
    return array(
      'id'                          => 'Number',
      'facility_resource_type'      => 'Text',
      'facility_resource_type_abbr' => 'Text',
      'description'                 => 'Text',
      'app_display'                 => 'Boolean',
      'created_at'                  => 'Date',
      'updated_at'                  => 'Date',
      'ag_facility_list'            => 'ManyKey',
    );
  }
}
