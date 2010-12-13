<?php

/**
 * agDefaultScenarioFacilityResourceType form base class.
 *
 * @method agDefaultScenarioFacilityResourceType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagDefaultScenarioFacilityResourceTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                        => new sfWidgetFormInputHidden(),
      'scenario_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenario'), 'add_empty' => false)),
      'facility_resource_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceType'), 'add_empty' => false)),
      'created_at'                => new sfWidgetFormDateTime(),
      'updated_at'                => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'scenario_id'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agScenario'))),
      'facility_resource_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceType'))),
      'created_at'                => new sfValidatorDateTime(),
      'updated_at'                => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agDefaultScenarioFacilityResourceType', 'column' => array('scenario_id', 'facility_resource_type_id')))
    );

    $this->widgetSchema->setNameFormat('ag_default_scenario_facility_resource_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agDefaultScenarioFacilityResourceType';
  }

}