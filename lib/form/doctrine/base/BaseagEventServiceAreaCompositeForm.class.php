<?php

/**
 * agEventServiceAreaComposite form base class.
 *
 * @method agEventServiceAreaComposite getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventServiceAreaCompositeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'event_service_area_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventServiceArea'), 'add_empty' => false)),
      'geo_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'), 'add_empty' => false)),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_service_area_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventServiceArea'))),
      'geo_id'                => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'))),
      'created_at'            => new sfValidatorDateTime(),
      'updated_at'            => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventServiceAreaComposite', 'column' => array('event_service_area_id', 'geo_id')))
    );

    $this->widgetSchema->setNameFormat('ag_event_service_area_composite[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventServiceAreaComposite';
  }

}