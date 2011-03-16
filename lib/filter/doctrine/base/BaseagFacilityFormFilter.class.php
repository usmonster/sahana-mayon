<?php

/**
 * agFacility filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagFacilityFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'site_id'                        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSite'), 'add_empty' => true)),
      'facility_name'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_facility_resource_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResourceType')),
    ));

    $this->setValidators(array(
      'site_id'                        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agSite'), 'column' => 'id')),
      'facility_name'                  => new sfValidatorPass(array('required' => false)),
      'created_at'                     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_facility_resource_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResourceType', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_facility_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgFacilityResourceTypeListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agFacilityResource.facility_resource_type_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agFacility';
  }

  public function getFields()
  {
    return array(
      'id'                             => 'Number',
      'site_id'                        => 'ForeignKey',
      'facility_name'                  => 'Text',
      'created_at'                     => 'Date',
      'updated_at'                     => 'Date',
      'ag_facility_resource_type_list' => 'ManyKey',
    );
  }
}
