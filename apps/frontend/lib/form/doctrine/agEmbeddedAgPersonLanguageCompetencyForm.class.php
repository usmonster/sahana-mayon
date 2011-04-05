<?php

/**
 * agEmbeddedAgPersonLanguageCompetencyForm
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Nils Stolpe, CUNY SPS
 * @author Ilya Gulko, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEmbeddedAgPersonLanguageCompetencyForm extends agPersonLanguageCompetencyForm
{

  private static $staticLists;

  /**
   * setup()
   *
   * Sets the widgets for this form.
   * */
  public function setup()
  {
    /**
     * Pre-fetch the resultset that will be used to populate any
     * dropdowns that use database data for choices and store it in
     * a private static variable called $staticLists in case this
     * form is embedded multiple times
     * */
    if (!isset($this::$staticLists)) {
      $lists = array('agLanguageCompetency');

      foreach ($lists as $list) {
        $this::$staticLists[$list] = Doctrine::getTable($list)->createQuery($list)->execute();
      }
    }

    $this->setWidgets(
        array(
          'person_language_id' => new sfWidgetFormInputHidden(),
          'language_format_id' => new sfWidgetFormInputHidden(),
          'language_competency_id' => new sfWidgetFormDoctrineChoice(
              array('query' => $this::$staticLists['agLanguageCompetency'],
                'model' => $this->getRelatedModelName('agLanguageCompetency'),
                'add_empty' => true),
              array('class' => 'inputGray')
          ),
        )
    );

    $this->setValidators(
        array(
          'person_language_id' => new sfValidatorDoctrineChoice(
              array('model' => $this->getRelatedModelName('agPersonMjAgLanguage'),
                'required' => false)
          ),
          'language_format_id' => new sfValidatorDoctrineChoice(
              array('model' => $this->getRelatedModelName('agLanguageFormat'),
                'required' => false)
          ),
          'language_competency_id' => new sfValidatorDoctrineChoice(
              array('model' => $this->getRelatedModelName('agLanguageCompetency'),
                'required' => false)
          ),
        )
    );
  }

  /**
   * configure()
   * Used to set the custom formatter for this widget.
   * */
  public function configure()
  {
    /**
     * Set a custom formatter for this form
     * */
    $custDeco = new agWidgetFormSchemaFormatterInline($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');
  }

}
