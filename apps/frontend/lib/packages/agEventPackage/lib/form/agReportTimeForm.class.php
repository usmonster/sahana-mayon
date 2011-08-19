<?php
class agReportTimeForm extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'report_time'    => new sfWidgetFormInput(),
    ));

    $this->widgetSchema->setNameFormat('reportTime[%s]');

    $this->setValidators(array(
      'report_time'    => new sfValidatorDateTime(array('required' => true)),
    ));
  }

}