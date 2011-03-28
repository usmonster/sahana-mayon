<?php

/**
 * agGeoFeatureForm extended class for embedding into agAddressGeoForm(mainly)
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Nils Stolpe, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 * */

class agEmbeddedGeoFeatureForm extends agGeoFeatureForm
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
      'geo_coordinate_order' => new sfValidatorInteger(array('required' => false)),
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