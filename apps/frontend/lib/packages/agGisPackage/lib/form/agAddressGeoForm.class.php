<?php
/**
* extended agAddressGeo form base class.
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

class agAddressGeoForm extends BaseagAddressGeoForm
{
/**
 * setup the form with the needed elements, omit ones we don't want
 * also embed a geo_coordinate form for entry of longitude/latitude.
 * this form is mainly for testing purposes, the end user should not
 * interact with this form directly.
 */
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'address_id'         => new sfWidgetFormInputText(),
      'geo_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'), 'add_empty' => false)),
      'geo_match_score_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoMatchScore'), 'add_empty' => false)),
      //'created_at'         => new sfWidgetFormDateTime(),
      //'updated_at'         => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'address_id'         => new sfValidatorInteger(),
      'geo_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'))),
      'geo_match_score_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoMatchScore'))),
      //'created_at'         => new sfValidatorDateTime(),
      //'updated_at'         => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agAddressGeo', 'column' => array('address_id')))
    );

    $this->widgetSchema->setNameFormat('ag_address_geo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();


    $agGeoCoordForm = new agEmbeddedGeoCoordinateForm();
    $this->embedForm('coordinates', $agGeoCoordForm);
  }

  /**
   *
   * @return string representing the model name of this class
   */
  public function getModelName()
  {
    return 'agAddressGeo';
  }

}