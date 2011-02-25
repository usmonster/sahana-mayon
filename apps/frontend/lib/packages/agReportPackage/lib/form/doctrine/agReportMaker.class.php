<?php

/**
 * Agasti ag Report Maker Form Class - A class to generate either a new staff pool form or an
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
class agReportMakerForm extends sfForm
{

  public $lucen_search_id;
  public $scenario_id;
  public $staff_gen_id;
  public $available_tables; //this should be a two dimensional array, with the keys as the available table names


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
    $this->embedReportGeneratorForm();
    $this->embedLuceneForm();
    $this->embedReportForm();
    $this->embedQuerySelectFieldForm();
    //going to have to dynamically embed query select forms, one per table/column
  }

  /**
   * Embeds the Report Generator Form
   * */
  public function embedReportGeneratorForm()
  {
    if (isset($this->report_gen_id)) {
      $staffGenObject = agDoctrineQuery::create()
              ->from('agReportGenerator a')
              ->where('a.id =?', $this->report_gen_id)
              ->execute()->getFirst();
      $this->lucene_search_id = $staffGenObject->lucene_search_id;
    }
    $reportGenForm = new agReportGeneratorForm(isset($reportGenObject) ? $reportGenObject : null);

    $reportGenForm->setWidget('lucene_search_id', new sfWidgetFormInputHidden());
    $reportGenForm->setWidget('report_id', new sfWidgetFormInputHidden());
    $reportGenForm->setValidator('lucene_search_id', new sfValidatorPass(array('required' => false)));
//    $reportGenForm->setValidator('search_weight', new sfValidatorPass(array('required' => false)));
    $reportGenForm->setValidator('report_id', new sfValidatorPass());

    unset($reportGenForm['created_at'], $reportGenForm['updated_at']);

    $this->embedForm('report_generator', $reportGenForm);
  }

  /**
   * Embeds the Lucene Form
   * */
  public function embedLuceneForm()
  {
    if (isset($this->lucene_search_id)) {
      $luceneObject = agDoctrineQuery::create()
              ->from('agLuceneSearch a')
              ->where('a.id =?', $this->lucene_search_id)
              ->execute()->getFirst();
    }
    $luceneForm = new agLuceneSearchForm(isset($luceneObject) ? $luceneObject : null);

    unset($luceneForm['created_at'], $luceneForm['updated_at']);
    unset($luceneForm['ag_report_list']);

    $this->embedForm('lucene_search', $luceneForm);
  }
  public function embedReportForm()
  {
    if (isset($this->report_id)) {
      $reportObject = agDoctrineQuery::create()
              ->from('agReport a')
              ->where('a.id =?', $this->report_id)
              ->execute()->getFirst();
    }
    $reportForm = new agReportForm(isset($reportObject) ? $luceneObject : null);

    unset($reportForm['created_at'], $reportForm['updated_at']);
    unset($reportForm['ag_lucene_search_list']);

    $this->embedForm('report', $reportForm);
  }
  public function embedQuerySelectFieldForm()
  {
    $this->available_tables = Doctrine::getLoadedModels();

    if (isset($this->lucene_search_id)) {
      $luceneObject = agDoctrineQuery::create()
              ->from('agLuceneSearch a')
              ->where('a.id =?', $this->lucene_search_id)
              ->execute()->getFirst();
    }
    $queryselectForm = new agQuerySelectFieldForm(isset($luceneObject) ? $luceneObject : null);

    unset($queryselectForm['created_at'], $queryselectForm['updated_at']);
    $queryselectForm->setWidget('report_id', new sfWidgetFormInputHidden());

    $this->embedForm('query_select', $queryselectForm);
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

  public function saveReportGenForm($form, $values)
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

