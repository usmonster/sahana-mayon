<?php

/**
 * tempGeoRelationship form base class.
 *
 * @method tempGeoRelationship getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasetempGeoRelationshipForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'geo_id1' => new sfWidgetFormInputHidden(),
      'geo_id2' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'geo_id1' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('geo_id1')), 'empty_value' => $this->getObject()->get('geo_id1'), 'required' => false)),
      'geo_id2' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('geo_id2')), 'empty_value' => $this->getObject()->get('geo_id2'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('temp_geo_relationship[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'tempGeoRelationship';
  }

}