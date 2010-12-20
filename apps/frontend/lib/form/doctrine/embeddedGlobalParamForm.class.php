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
class embeddedGlobalParamForm extends BaseagGlobalParamForm
{
  public function setup()
  {
    parent::setup();
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'host_id'    => new sfWidgetFormInputHidden(),//sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agHost'), 'add_empty' => false)),
      'datapoint'  => new sfWidgetFormInputText(),
      'value'      => new sfWidgetFormInputText(),
      //'created_at' => new sfWidgetFormDateTime(),
      //'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'host_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agHost'))),
      'datapoint'  => new sfValidatorString(array('max_length' => 128)),
      'value'      => new sfValidatorString(array('max_length' => 128)),
      //'created_at' => new sfValidatorDateTime(),
      //'updated_at' => new sfValidatorDateTime(),
    ));

  }
  public function configure(){
    parent::configure();
        unset($this['created_at'],
          $this['updated_at']
          );
  }
  public function getModelName()
  {
    return 'agGlobalParam';
  }

}