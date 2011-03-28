<?php

/**
 * Form stub for multiple event functionatlity and time
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */

class agMultipleEventFacilityResourceActivationTimeForm extends BaseagEventFacilityResourceActivationTimeForm
{
  /*
   * 
   */
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
