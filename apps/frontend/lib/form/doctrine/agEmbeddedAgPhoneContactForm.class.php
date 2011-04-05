<?php

/**
 * agEntityPhoneContact form.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Nils Stolpe, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 * */
class agEmbeddedAgPhoneContactForm extends agPhoneContactForm
{

  public function setup()
  {
    $this->setWidgets(
        array(
          'id' => new sfWidgetFormInputHidden(),
          'phone_contact' => new agWidgetFormInputPhoneText(
              array(
                'match_pattern' =>
                $this->getObject()->getAgPhoneFormat()->getAgPhoneFormatType()->match_pattern,
                'replacement_pattern' =>
                $this->getObject()->getAgPhoneFormat()->getAgPhoneFormatType()->replacement_pattern),
              array('class' => 'inputGray')
          ),
        )
    );

    $this->setValidators(
        array(
          'id' => new sfValidatorChoice(
              array(
                'choices' => array(
                  $this->getObject()->get('id')),
                'empty_value' => $this->getObject()->get('id'),
                'required' => false)
          ),
          'phone_contact' => new sfValidatorRegex(
              array(
                'required' => false,
                'empty_value' => null,
                'pattern' => '/^((\([\d]{3}\) *[\d]{3} *-?[\d]{4})|(([\d]{3}(.|-)? *){2}[\d]{4}))( *x\d+)?$/')
          ),
        )
    );
  }

}