<?php

/**
 * Extends agPersonDateOfBirthForm and return date of birth information
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
 */
class agEmbeddedPersonDateOfBirthForm extends agPersonDateOfBirthForm
{

  /**
   * @return a setup form with all associated/needed data for date of birth
   */
  public function setup()
  {
    parent::setup();

    $this->setWidgets(array(
      #'id'            => new sfWidgetFormInputHidden(),
      #'person_id'     => new sfWidgetFormInputHidden(),//sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => false)),
      #'date_of_birth' => new sfWidgetFormDate(),
      'date_of_birth' => new sfWidgetFormInputText(array(), array('id' => 'dob', 'class' => 'inputGray')),
    ));

    $this->setValidators(array(
      //'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      //'person_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'))),
      'date_of_birth' => new sfValidatorDate(array('required' => false, 'empty_value' => '0000-00-00')),
        /**
         * @todo: 'empty_value' => '0000-00-00' is not optimal, but is currently preventing a DB error on submit.
         */
    ));
  }

  public function configure()
  {
    parent::configure();
    $custDeco = new agWidgetFormSchemaFormatterInline($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');
  }

}
