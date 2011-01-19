<?php
/**
* Embedded staff form for the agStaff package.
*
* PHP Version 5
*
* LICENSE: This source file is subject to LGPLv3.0 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Nils Stolpe, CUNY SPS
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
**/

class PluginagEmbeddedAgStaffForm extends PluginagStaffForm
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                          => new sfWidgetFormInputHidden(),
//      'person_id'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => false)),
      'staff_status_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffStatus'), 'add_empty' => false, 'method' => 'getStaffStatus')),
//      'created_at'                  => new sfWidgetFormDateTime(),
//      'updated_at'                  => new sfWidgetFormDateTime(),
//      'ag_staff_resource_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResourceType')),
    ));

    $this->setValidators(array(
      'id'                          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
//      'person_id'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'))),
      'staff_status_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffStatus'))),
//      'created_at'                  => new sfValidatorDateTime(),
//      'updated_at'                  => new sfValidatorDateTime(),
//      'ag_staff_resource_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResourceType', 'required' => false)),
    ));
  }
}