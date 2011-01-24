<?php

/**
 * agStaffPoolForm
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agStaffPoolForm extends agLuceneSearchForm
{

  public $lucene_search_id;
  public function embedAgSearchForms()
  {
    //$staffContainerForm = $this;
    //$this->embedLuceneForm($this);
    $this->embedStaffGeneratorForm();
  }

  public function embedStaffGeneratorForm()
  {
    if (!$this->isNew()) {
      $staffGenObject = Doctrine_Query::create()
              ->from('agScenarioStaffGenerator a')
              ->where('a.lucene_search_id =?', $this->getObject()->id)
              ->execute()->getFirst();
    }
    $staffGenForm = new agScenarioStaffGeneratorForm(isset($staffGenObject) ? $staffGenObject : null);


    $staffGenForm->setWidget('lucene_search_id', new sfWidgetFormInputHidden());
    $staffGenForm->setWidget('scenario_id', new sfWidgetFormInputHidden());
    $staffGenForm->setValidator('lucene_search_id', new sfValidatorPass());
    $staffGenForm->setValidator('scenario_id', new sfValidatorPass());

    unset($staffGenForm['created_at'], $staffGenForm['updated_at']);


    $this->embedForm('staff_generator', $staffGenForm);
  }

//  public function embedLuceneForm($containerForm)
//  {
//    if (!$this->valid()) {
//      $luceneObject = Doctrine_Query::create()
//              ->from('agLuceneSearch a')
//              //->where('a.p =?', $id)
//              ->execute()->getFirst();
//    }
//    $luceneForm = new agLuceneSearchForm(isset($luceneObject) ? $luceneObject : null);
//    unset($luceneForm['created_at'], $luceneForm['updated_at']);
//    unset($luceneForm['ag_report_list']);
//
//    $containerForm->embedForm('lucene_search', $luceneForm);
//  }

  public function configure()
  {
    parent::configure();
    unset($this['created_at'], $this['updated_at']);
    unset($this['ag_report_list']);
    $this->embedAgSearchForms();

  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (isset($this->embeddedForms['staff_generator'])) {
      $form = $this->embeddedForms['staff_generator'];
      $values = $this->values['staff_generator'];
      $this->saveStaffGenForm($form, $values);
      unset($this->embeddedForms['staff_generator']);
    }
    return parent::saveEmbeddedForms($con, $forms);
  }

  public function saveLuceneForm($form, $values)
  {
    $form->updateObject($values);
//    if ($form->getObject()->person_id == null) {
//      $form->getObject()->person_id = $this->getObject()->id;
//    }
    $form->getObject()->save();
    $this->lucene_search_id = $form->getObject()->id;
  }

  public function saveStaffGenForm($form, $values)
  {
    $form->updateObject($values);
    if ($form->getObject()->lucene_search_id == null) {
      $form->getObject()->lucene_search_id = $this->getObject()->id;
      $form->getObject()->scenario_id = $this->getObject()->id;
    }
    $form->getObject()->save();
    //$this->staff_resource_id = $form->getObject()->id;
  }

}

