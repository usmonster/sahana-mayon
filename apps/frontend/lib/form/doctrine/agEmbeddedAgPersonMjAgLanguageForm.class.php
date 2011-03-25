<?php

/**
 * agEmbeddedAgPersonMjAgLanguageForm.
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
**/
class agEmbeddedAgPersonMjAgLanguageForm extends agPersonMjAgLanguageForm
{
  private static $agLanguageList;

  public function setup()
  {
    if (!isset($this::$agLanguageList)) {
      $this::$agLanguageList = Doctrine::getTable('agLanguage')->createQuery('a')->execute();
    }

    $this->setWidgets(array(
      'id'                          => new sfWidgetFormInputHidden(),
      //'person_id'                   => new sfWidgetFormInputHidden(),
      'language_id'                 => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agLanguage'), 'query' => $this::$agLanguageList, 'add_empty' => true), array('class' => 'inputGray')),
      'priority'                    => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'id'                          => new sfValidatorInteger(array('required' => false)),//new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      //'person_id'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'))),
      'language_id'                 => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agLanguage'), 'required' => false)),
      'priority'                    => new sfValidatorInteger(),
    ));
  }

  public function configure()
  {
    $custDeco = new agWidgetFormSchemaFormatterInline($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');
  }
}