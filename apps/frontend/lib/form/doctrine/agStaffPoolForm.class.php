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
    $reportForm = new agReportGeneratorForm();

    unset($staffGenForm['created_at'], $staffGenForm['updated_at']);
    unset($reportForm['created_at'], $reportForm['updated_at']);

    $this->embedForm('staff_generator', $staffGenForm);
    $this->embedForm('report_generator', $reportForm);

  }
}
