<?php

/**
 * A form the extends agPersonName form for name entry when creating an agPerson.
 * 
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 * */
class agEmbeddedAgPersonNameForm extends agPersonNameForm
{

  /**
   * Sets up the widgets for an agEmbeddedAgPersonNameForm.
   * The validator for the person_name field is set to "'required' => false" so that an error
   * is not returned on form submission.
   * */
  public function setup()
  {
    parent::setup();

    $this->setWidgets(array(
      'id' => new sfWidgetFormInputHidden(),
      'person_name' => new sfWidgetFormInputText(array(), array('class' => 'inputGray')),
    ));

    $this->setValidators(array(
      'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_name' => new sfValidatorString(array('max_length' => 64, 'required' => false, 'empty_value' => null)),
    ));
  }

  public function configure()
  {
    $custDeco = new agWidgetFormSchemaFormatterInline($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');
  }

}
