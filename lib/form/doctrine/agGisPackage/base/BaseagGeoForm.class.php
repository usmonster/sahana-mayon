<?php

/**
 * agGeo form base class.
 *
 * @method agGeo getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagGeoForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'geo_type_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoType'), 'add_empty' => false)),
      'geo_source_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoSource'), 'add_empty' => false)),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'geo_type_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoType'))),
      'geo_source_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoSource'))),
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('ag_geo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agGeo';
  }

}