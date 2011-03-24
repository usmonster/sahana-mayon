<?php

/**
 * extended agGeoCoordinate form base class.
 * only to be used for embedding
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEmbeddedGeoCoordinateForm extends agGeoCoordinateForm
{

  /**
   * setup the form with the needed elements, omit ones we don't want
   * used for embedding into a general agAddressGeo form
   * this form is mainly for testing purposes, the end user should not
   * interact with this form directly.
   */
  public function setup()
  {
    $this->setWidgets(array(
      'id' => new sfWidgetFormInputHidden(),
      'longitude' => new sfWidgetFormInputText(),
      'latitude' => new sfWidgetFormInputText(),
        //'created_at' => new sfWidgetFormDateTime(),
        //'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'longitude' => new sfValidatorNumber(),
      'latitude' => new sfValidatorNumber(),
        //'created_at' => new sfValidatorDateTime(),
        //'updated_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('ag_geo_coordinate[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();
  }

  /**
   *
   * @return string representing the model name of this class
   */
  public function getModelName()
  {
    return 'agGeoCoordinate';
  }

}