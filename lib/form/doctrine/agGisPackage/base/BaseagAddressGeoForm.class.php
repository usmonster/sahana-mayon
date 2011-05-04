<?php

/**
 * agAddressGeo form base class.
 *
 * @method agAddressGeo getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAddressGeoForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'address_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddress'), 'add_empty' => false)),
      'geo_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'), 'add_empty' => false)),
      'geo_match_score_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoMatchScore'), 'add_empty' => false)),
      'created_at'         => new sfWidgetFormDateTime(),
      'updated_at'         => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'address_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddress'))),
      'geo_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'))),
      'geo_match_score_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoMatchScore'))),
      'created_at'         => new sfValidatorDateTime(),
      'updated_at'         => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agAddressGeo', 'column' => array('address_id'))),
        new sfValidatorDoctrineUnique(array('model' => 'agAddressGeo', 'column' => array('address_id', 'geo_id'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_address_geo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAddressGeo';
  }

}
