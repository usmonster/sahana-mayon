<?php

/**
 * agEventFacilityResourceActivationTime form base class.
 *
 * @method agEventFacilityResourceActivationTime getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
class agTinyEventFacilityResourceActivationTimeForm extends PluginagEventFacilityResourceActivationTimeForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'id'                         => new sfWidgetFormInputHidden(),
      'event_facility_resource_id' => new sfWidgetFormInputHidden(),
      'activation_time'            => /*new sfWidgetFormDateTime(),*/new sfWidgetFormInputText(array(), array('class' => 'inputGray submitTextToForm')),
    ));

    $this->setValidators(array(
      'id'                         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_facility_resource_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityResource'))),
      'activation_time'            => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('ag_event_facility_resource_activation_time[%s]');
    
    $this->getWidgetSchema()->setLabel('activation_time', false);

    $custDeco = new agWidgetFormSchemaFormatterTiny($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');

  }

}