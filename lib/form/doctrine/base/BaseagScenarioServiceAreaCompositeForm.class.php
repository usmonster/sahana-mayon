<?php

/**
 * agScenarioServiceAreaComposite form base class.
 *
 * @method agScenarioServiceAreaComposite getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagScenarioServiceAreaCompositeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'scenario_service_area_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioServiceArea'), 'add_empty' => false)),
      'geo_id'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'), 'add_empty' => false)),
      'created_at'               => new sfWidgetFormDateTime(),
      'updated_at'               => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'scenario_service_area_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioServiceArea'))),
      'geo_id'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'))),
      'created_at'               => new sfValidatorDateTime(),
      'updated_at'               => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agScenarioServiceAreaComposite', 'column' => array('scenario_service_area_id')))
    );

    $this->widgetSchema->setNameFormat('ag_scenario_service_area_composite[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agScenarioServiceAreaComposite';
  }

}
