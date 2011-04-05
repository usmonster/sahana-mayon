<?php

/**
 * A form the extends agEmailContactForm form for name entry when creating an agPerson
 * or othe agEntity.
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
class agEmbeddedAgEmailContactForm extends agEmailContactForm
{

  /**
   * Sets up the widgets for an agEmbeddedEmailContactForm.
   * The validator for the email_contact field is set to "'required' => false" so that an error
   * is not returned on form submission
   * */
  public function setup()
  {
    parent::setup();

    $this->setWidgets(
        array(
          'id' => new sfWidgetFormInputHidden(),
          'email_contact' => new sfWidgetFormInputText(
              array(), array('class' => 'inputGray')
          ),
        )
    );

    $this->setValidators(
        array(
          'id' => new sfValidatorChoice(
              array('choices' =>
                array($this->getObject()->get('id')), 'empty_value' =>
                $this->getObject()->get('id'), 'required' => false)
          ),
          'email_contact' => new sfValidatorEmail(
              array('required' => false, 'empty_value' => null)
          ),
        )
    );
  }

}