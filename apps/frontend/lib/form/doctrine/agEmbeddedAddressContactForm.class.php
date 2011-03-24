<?php

/** 
* Entitiy Address Contact
*
* PHP Version 5.3
*
* LICENSE: This source file is subject to LGPLv2.1 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Nils Stolpe, CUNY SPS
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/



class agEmbeddedAddressContactForm extends agEntityAddressContactForm
{
/**
* @todo - comment this function
*/

  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'entity_id'               => new sfWidgetFormInputHidden(),
      'address_id'              => new sfWidgetFormInputHidden(),
      'address_contact_type_id' => new sfWidgetFormInputHidden(),
      'priority'                => new sfWidgetFormInputHidden(),
      'created_at'              => new sfWidgetFormDateTime(),
      'updated_at'              => new sfWidgetFormDateTime(),
      'address_to_type'         => new sfWidgetFormInputText(array(), array('class' => 'inputGray')),
    ));
    $this->widgetSchema->setLabel('address_to_type',false);

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'entity_id'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'))),
      'address_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddress'))),
      'address_contact_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressContactType'))),
      'priority'                => new sfValidatorInteger(),
      'created_at'              => new sfValidatorDateTime(),
      'updated_at'              => new sfValidatorDateTime(),
      'address_to_type'         => new sfValidatorString(array('max_length' => 255)),
    ));
  }
/**
* @todo - comment this function
*/

  public function configure()
  {
    unset($this['entity_id'], $this['created_at'], $this['updated_at']);
  }
}
