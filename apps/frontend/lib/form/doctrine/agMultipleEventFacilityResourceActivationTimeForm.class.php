<?php
class agMultipleEventFacilityResourceActivationTimeForm extends BaseagEventFacilityResourceActivationTimeForm
{
  public function configure()
  {
    unset($this['created_at'], $this['updated_at']);
    $this->setWidget('event_facility_resource_id', new sfWidgetFormInputHidden());
    $this->setWidget('activation_time', new sfWidgetFormInputHidden());
    $this->setWidget('operate_on', new agWidgetFormSelectCheckbox(array('choices' => array(null)), array()));
    $this->getWidgetSchema()->setLabel('operate_on', false);


    $fgroupDec = new agWidgetFormSchemaFormatterNoList($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('row', $fgroupDec);
    $this->getWidgetSchema()->setFormFormatterName('row');
  }
}
?>
