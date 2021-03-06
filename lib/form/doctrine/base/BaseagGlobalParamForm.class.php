<?php

/**
 * agGlobalParam form base class.
 *
 * @method agGlobalParam getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagGlobalParamForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'host_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agHost'), 'add_empty' => false)),
      'datapoint'   => new sfWidgetFormInputText(),
      'value'       => new sfWidgetFormInputText(),
      'description' => new sfWidgetFormInputText(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'host_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agHost'))),
      'datapoint'   => new sfValidatorString(array('max_length' => 128)),
      'value'       => new sfValidatorString(array('max_length' => 128)),
      'description' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agGlobalParam', 'column' => array('host_id', 'datapoint')))
    );

    $this->widgetSchema->setNameFormat('ag_global_param[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agGlobalParam';
  }

}
