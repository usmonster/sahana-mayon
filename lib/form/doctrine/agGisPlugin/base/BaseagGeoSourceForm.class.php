<?php

/**
 * agGeoSource form base class.
 *
 * @method agGeoSource getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagGeoSourceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'geo_source'          => new sfWidgetFormInputText(),
      'data_compiled'       => new sfWidgetFormDateTime(),
      'geo_source_score_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoSourceScore'), 'add_empty' => false)),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'geo_source'          => new sfValidatorString(array('max_length' => 64)),
      'data_compiled'       => new sfValidatorDateTime(),
      'geo_source_score_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoSourceScore'))),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agGeoSource', 'column' => array('geo_source', 'data_compiled')))
    );

    $this->widgetSchema->setNameFormat('ag_geo_source[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agGeoSource';
  }

}