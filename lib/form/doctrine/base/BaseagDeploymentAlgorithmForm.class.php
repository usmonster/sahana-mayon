<?php

/**
 * agDeploymentAlgorithm form base class.
 *
 * @method agDeploymentAlgorithm getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagDeploymentAlgorithmForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'deployment_algorithm' => new sfWidgetFormInputText(),
      'description'          => new sfWidgetFormInputText(),
      'app_display'          => new sfWidgetFormInputCheckbox(),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_at'           => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'deployment_algorithm' => new sfValidatorString(array('max_length' => 30)),
      'description'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_display'          => new sfValidatorBoolean(array('required' => false)),
      'created_at'           => new sfValidatorDateTime(),
      'updated_at'           => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agDeploymentAlgorithm', 'column' => array('deployment_algorithm')))
    );

    $this->widgetSchema->setNameFormat('ag_deployment_algorithm[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agDeploymentAlgorithm';
  }

}