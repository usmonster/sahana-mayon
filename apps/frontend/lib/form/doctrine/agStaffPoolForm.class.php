<?php

/**
 * agStaffPoolForm
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agStaffPoolForm extends sfForm
{
  public function configure()
  {
    parent::configure();
    $staffGenForm = new agScenarioStaffGeneratorForm();
    //$reportGenForm = new agReportGeneratorForm();
    //$reportForm = new agReportForm();
    $luceneForm = new agLuceneSearchForm();


    unset($staffGenForm['created_at'], $staffGenForm['updated_at']);
    //unset($reportGenForm['created_at'], $reportGenForm['updated_at']);
    //unset($reportForm['created_at'], $reportForm['updated_at']);
    unset($luceneForm['created_at'], $luceneForm['updated_at']);
    unset($luceneForm['ag_report_list']);
    //we probably only really need lucene search and staff generator forms
    
    $this->embedForm('staff_generator', $staffGenForm);
    //$this->embedForm('report', $reportForm);
    //$this->embedForm('report_generator', $reportGenForm);
    $this->embedForm('lucene_search', $luceneForm);
  }
}
