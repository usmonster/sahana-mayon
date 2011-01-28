<?php

/**
 * agGeoRelationship filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagGeoRelationshipFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'geo_id1'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('geo1'), 'add_empty' => true)),
      'geo_id2'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('geo2'), 'add_empty' => true)),
      'geo_relationship_type_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoRelationshipType'), 'add_empty' => true)),
      'geo_relationship_km_value' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'geo_id1'                   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('geo1'), 'column' => 'id')),
      'geo_id2'                   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('geo2'), 'column' => 'id')),
      'geo_relationship_type_id'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agGeoRelationshipType'), 'column' => 'id')),
      'geo_relationship_km_value' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'created_at'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_geo_relationship_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agGeoRelationship';
  }

  public function getFields()
  {
    return array(
      'id'                        => 'Number',
      'geo_id1'                   => 'ForeignKey',
      'geo_id2'                   => 'ForeignKey',
      'geo_relationship_type_id'  => 'ForeignKey',
      'geo_relationship_km_value' => 'Number',
      'created_at'                => 'Date',
      'updated_at'                => 'Date',
    );
  }
}
