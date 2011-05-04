<?php

/**
 * agGeoRelationship form base class.
 *
 * @method agGeoRelationship getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagGeoRelationshipForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                        => new sfWidgetFormInputHidden(),
      'geo_id1'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('geo1'), 'add_empty' => false)),
      'geo_id2'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('geo2'), 'add_empty' => false)),
      'geo_relationship_type_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoRelationshipType'), 'add_empty' => false)),
      'geo_relationship_km_value' => new sfWidgetFormInputText(),
      'created_at'                => new sfWidgetFormDateTime(),
      'updated_at'                => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'geo_id1'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('geo1'))),
      'geo_id2'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('geo2'))),
      'geo_relationship_type_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoRelationshipType'))),
      'geo_relationship_km_value' => new sfValidatorNumber(),
      'created_at'                => new sfValidatorDateTime(),
      'updated_at'                => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agGeoRelationship', 'column' => array('geo_id1', 'geo_id2')))
    );

    $this->widgetSchema->setNameFormat('ag_geo_relationship[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agGeoRelationship';
  }

}
