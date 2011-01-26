<?php

/**
 * Agasti ag Staff Pool Form Class - A class to generate either a new staff pool form or an
 * edit staff pool form
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * @todo add delete handling
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 * */
class agStaffPoolForm extends sfForm
{

  public $lucene_search_id;
  public $scenario_id;
  public $staff_gen_id;

  /**
   *
   * @param integer $staff_gen_id an incoming staff generator id to construct the form
   */
  public function __construct($staff_gen_id = null)
  {
    $this->staff_gen_id = $staff_gen_id;
    parent::__construct(array(), array(), array());
  }

  /**
   * Configures the form and starts off the embedding chain.
   * */
  public function embedAgSearchForms()
  {
    $this->embedStaffGeneratorForm();
    $this->embedLuceneForm();
  }

  /**
   * Embeds the Staff Generator Form
   * */
  public function embedStaffGeneratorForm()
  {
    if (isset($this->staff_gen_id)) {
      $staffGenObject = Doctrine_Query::create()
              ->from('agScenarioStaffGenerator a')
              ->where('a.id =?', $this->staff_gen_id)
              ->execute()->getFirst();
      $this->lucene_search_id = $staffGenObject->lucene_search_id;
    }
    $staffGenForm = new agScenarioStaffGeneratorForm(isset($staffGenObject) ? $staffGenObject : null);

    $staffGenForm->setWidget('lucene_search_id', new sfWidgetFormInputHidden());
    $staffGenForm->setWidget('scenario_id', new sfWidgetFormInputHidden());
    $staffGenForm->setValidator('lucene_search_id', new sfValidatorPass(array('required' => false)));
//    $staffGenForm->setValidator('search_weight', new sfValidatorPass(array('required' => false)));
    $staffGenForm->setValidator('scenario_id', new sfValidatorPass());

    unset($staffGenForm['created_at'], $staffGenForm['updated_at']);

    $this->embedForm('staff_generator', $staffGenForm);
  }

  /**
   * Embeds the Lucene Form
   * */
  public function embedLuceneForm()
  {
    if (isset($this->lucene_search_id)) {
      $luceneObject = Doctrine_Query::create()
              ->from('agLuceneSearch a')
              ->where('a.id =?', $this->lucene_search_id)
              ->execute()->getFirst();
    }
    $luceneForm = new agLuceneSearchForm(isset($luceneObject) ? $luceneObject : null);
    
    unset($luceneForm['created_at'], $luceneForm['updated_at']);
    unset($luceneForm['ag_report_list']);

    $this->embedForm('lucene_search', $luceneForm);
  }

  /**
   * sets the form up, gives it a name and embeds the forms
   * */
  public function configure()
  {
    $this->widgetSchema->setNameFormat('staff_pool[%s]');
    $this->embedAgSearchForms();
  }

  /**
   * this is the only saving that takes place since the sfForm has nothing but embedded forms
   * @param $con to maintain amorphism this interface is copied
   * @param $forms to maintain amorphism this interface is copied
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (isset($this->embeddedForms['lucene_search'])) {
      $form = $this->embeddedForms['lucene_search'];
      $values = $this->values['lucene_search'];
      $this->saveLuceneForm($form, $values);
      unset($this->embeddedForms['lucene_search']);
    }

    if (isset($this->embeddedForms['staff_generator'])) {
      $form = $this->embeddedForms['staff_generator'];
      $values = $this->values['staff_generator'];
      $this->saveStaffGenForm($form, $values);
      unset($this->embeddedForms['staff_generator']);
    }
  }

  /**
   * save the embedded lucene form
   * @param sfForm $form a form to process
   * @param mixed $values a set of values coming from a post
   */
  public function saveLuceneForm($form, $values)
  {
    $form->updateObject($values);

    $form->getObject()->save();
    $this->lucene_search_id = $form->getObject()->getId();
  }
  
  /**
   * save the embedded scenario staff generator form
   * @param sfForm $form a form to process
   * @param mixed $values a set of values coming from a post
   */

  public function saveStaffGenForm($form, $values)
  {
    $form->updateObject($values);
    if ($form->getObject()->lucene_search_id == null) {
      $form->getObject()->lucene_search_id = $this->lucene_search_id;
      $form->getObject()->scenario_id = $this->scenario_id;
    }
    $form->getObject()->save();
    //$this->staff_resource_id = $form->getObject()->id;
  }

}

