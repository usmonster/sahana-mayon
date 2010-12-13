<?php
/**
* agEntityPhoneContact form.
*
* PHP Version 5
*
* LICENSE: This source file is subject to LGPLv3.0 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Nils Stolpe, CUNY SPS
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
**/
class agEmbeddedAgPhoneContactForm extends agPhoneContactForm
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                         => new sfWidgetFormInputHidden(),
      'phone_contact'              => new sfWidgetFormInputText(array(), array('class' => 'inputGray')),
    ));

    $this->setValidators(array(
      'id'                         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'phone_contact'              => new sfValidatorRegex(array('required' => false, 'pattern' => '/^((\([\d]{3}\) *[\d]{3} *-?[\d]{4})|(([\d]{3}(.|-)? *){2}[\d]{4}))( *x\d+)?$/')),//sfValidatorString(array('max_length' => 16)),
      #'phone_format_id'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPhoneFormat'))),
    ));
  }
}