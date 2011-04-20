<?php

/**
 * agScenarioStaffResource form base class.
 *
 * @method agScenarioStaffResource getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagScenarioStaffResourceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'scenario_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenario'), 'add_empty' => false)),
      'staff_resource_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResource'), 'add_empty' => false)),
      'deployment_weight' => new sfWidgetFormInputText(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'scenario_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agScenario'))),
      'staff_resource_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResource'))),
      'deployment_weight' => new sfValidatorInteger(),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agScenarioStaffResource', 'column' => array('scenario_id', 'staff_resource_id')))
    );

    $this->widgetSchema->setNameFormat('ag_scenario_staff_resource[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agScenarioStaffResource';
  }

}
