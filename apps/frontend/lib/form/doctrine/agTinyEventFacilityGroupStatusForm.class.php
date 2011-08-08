<?php

/**
 * agTinyEventFacilityGroupStatusForm
 *
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     Nils Stolpe
 */
class agTinyEventFacilityGroupStatusForm extends PluginagEventFacilityGroupStatusForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'id'                                  => new sfWidgetFormInputHidden(),
      'event_facility_group_id'             => new sfWidgetFormInputHidden(),
      'time_stamp'                          => new sfWidgetFormInputHidden(),
      'facility_group_allocation_status_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityGroupAllocationStatus'), 'add_empty' => false, 'method' => 'getFacilityGroupAllocationStatus')),
    ));

    $this->setValidators(array(
      'id'                                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_facility_group_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityGroup'))),
      'time_stamp'                          => new sfValidatorDateTime(),
      'facility_group_allocation_status_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityGroupAllocationStatus'))),
    ));

    $this->widgetSchema->setNameFormat('ag_event_facility_group_status[%s]');

    $this->getWidgetSchema()->setLabel('facility_group_allocation_status_id', false);

    $custDeco = new agWidgetFormSchemaFormatterTiny($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');
  }

}