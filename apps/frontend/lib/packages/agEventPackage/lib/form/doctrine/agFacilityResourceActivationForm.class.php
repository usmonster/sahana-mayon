<?php

/**
 * agFacilityResourceActivationForm class takes in an array of forms with which to construct
 * a set of embedded forms
 *
 * Provides embedded subform for editing facility resources
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agFacilityResourceAcvitationForm extends sfForm
{

  public $facility_resources;

  /**
   *
   * @param $facility_resources a set of facility resources to iterate through and construct the form
   */
  public function __construct($facility_resources = null) //we should disallow null from coming in as we NEED an array of forms
  {

    $this->facility_resources = $facility_resources;
    parent::__construct(array(), array(), array());
  }

  public function configure()
  {
    $this->widgetSchema->setNameFormat('facility_resource_activation[%s]');
    $this->embedEventFacilityResourceActivationTimeForms();
    $this->setWidget('activation_time', new sfWidgetFormDateTime());
  }

  public function embedEventFacilityResourceActivationTimeForms()
  {
    $facilityStaffResourceConDeco = new agWidgetFormSchemaFormatterInline($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('facilityStaffResourceConDeco', $facilityStaffResourceConDeco);
    $this->getWidgetSchema()->setFormFormatterName('facilityStaffResourceConDeco');

    if (isset($this->facility_resources)) {
      foreach ($this->facility_resources as $facility_resource) {

        $fgroupForm = new agEventFacilityResourceActivationTimeForm();
        $fgroupForm->getWidget('operate_on')->setAttribute('class', 'checkToggle');
        $facility_type = $facility_resource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type;

        $facility_name = $facility_resource->getAgFacilityResource()->getAgFacility()->facility_name;

        $facility_label = $facility_name . ' : ' . $facility_type;
        //$fgroupForm->setDefault('facility_resource_id', $foo->facility_resource_id);
        //$fgroupForm->setDefault('event_facility_group_id', $foo->event_facility_group_id);
        //$fgroupForm->setDefault('event_facility_group_id', $facility_resource->event_facility_group_id);
        $fgroupForm->setDefault('event_facility_resource_id', $facility_resource->id);
        $this->embedForm($facility_label, $fgroupForm);
        $c = $fgroupForm->getWidget('operate_on');
      }
    } else {

      //this portion should be removed entirely
      $fgroupForm = new agEventFacilityResourceActivationTimeForm();
      $fgroupForm->getWidget('operate_on')->setAttribute('class', 'checkToggle');
      //$fgroupForm = new PluginagEventFacilityResourceActivationTimeForm();
      $fgroupDec = new agWidgetFormSchemaFormatterRow($fgroupForm->getWidgetSchema());
      $fgroupForm->getWidgetSchema()->addFormFormatter('row', $fgroupDec);
      $fgroupForm->getWidgetSchema()->setFormFormatterName('row');


      $this->embedForm('new', $fgroupForm);
    }
  }
}

