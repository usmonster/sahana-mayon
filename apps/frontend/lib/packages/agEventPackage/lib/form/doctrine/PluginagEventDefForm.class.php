<?php

/**
 * agEvent form base class.
 *
 * @method agEvent getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
class PluginagEventDefForm extends PluginagEventForm
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'event_name'            => new sfWidgetFormInputText(array(), array('class' => 'inputGray')),
      'zero_hour'             => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_name'            => new sfValidatorString(array('max_length' => 64)),
      'zero_hour'             => new sfValidatorDateTime(),
    ));
  }
}