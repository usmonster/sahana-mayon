<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class agSingleEventFacilityResourceActivationTimeForm extends BaseagEventFacilityResourceActivationTimeForm
{
  public function configure()
  {
    unset($this['created_at'], $this['updated_at']);
    $this->setWidget('activation_time', new sfWidgetFormDateTime());
    $this->setWidget('event_facility_resource_id', new sfWidgetFormInputHidden());
  }
}

?>
