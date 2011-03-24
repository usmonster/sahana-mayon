<?php

/**
 * Returns the current form's model object
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEmbeddedAgPersonLanguageForm extends agPersonMjAgLanguageForm
{

  /**
   * @todo comment this function
   */
  public function configure()
  {
    parent::configure();

    unset($this['person_id'], $this['person_name_id']);
  }

  /**
   * @todo comment this function
   */
  public function setup()
  {
    $this->setWidgets(array(
      'id' => new sfWidgetFormInputHidden(),
      #'person_id'                   => new sfWidgetFormInputHidden(),
      'language_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agLanguage'), 'add_empty' => true), array('class' => 'width200')),
      'priority' => new sfWidgetFormInputHidden(),
        #'created_at'                  => new sfWidgetFormDateTime(),
        #'updated_at'                  => new sfWidgetFormDateTime(),
        #'ag_language_format_list'     => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageFormat')),
        #'ag_language_competency_list' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      #'person_id'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'))),
      'language_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agLanguage'), 'required' => false)),
      'priority' => new sfValidatorInteger(array('required' => false)),
        #'created_at'                  => new sfValidatorDateTime(),
        #'updated_at'                  => new sfValidatorDateTime(),
        #'ag_language_format_list'     => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageFormat', 'required' => false)),
        #'ag_language_competency_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageCompetency', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
        new sfValidatorAnd(array(
          new sfValidatorDoctrineUnique(array('model' => 'agPersonMjAgLanguage', 'column' => array('person_id', 'language_id'))),
          new sfValidatorDoctrineUnique(array('model' => 'agPersonMjAgLanguage', 'column' => array('person_id', 'priority'))),
        ))
    );

    $this->widgetSchema->setNameFormat('ag_person_mj_ag_language[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    //parent::setup();
  }

}
