<?php

/**
 * agGeoFeature form base class.
 *
 * @method agGeoFeature getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagGeoFeatureForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'geo_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'), 'add_empty' => false)),
      'geo_coordinate_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoCoordinate'), 'add_empty' => false)),
      'geo_coordinate_order' => new sfWidgetFormInputText(),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_at'           => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'geo_id'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'))),
      'geo_coordinate_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoCoordinate'))),
      'geo_coordinate_order' => new sfValidatorInteger(),
      'created_at'           => new sfValidatorDateTime(),
      'updated_at'           => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agGeoFeature', 'column' => array('geo_id', 'geo_coordinate_id'))),
        new sfValidatorDoctrineUnique(array('model' => 'agGeoFeature', 'column' => array('geo_id', 'geo_coordinate_order'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_geo_feature[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agGeoFeature';
  }

}
