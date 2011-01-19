<?php
/**
* Embedded staff resource organization form for the agStaff package.
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
class PluginagEmbeddedAgStaffResourceOrganizationForm extends agStaffResourceOrganizationForm
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
//      'staff_resource_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResource'), 'add_empty' => false, 'method' => 'getAgStaffResourceType')),
      'organization_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agOrganization'), 'method' => 'getOrganization', 'add_empty' => false)),
//      'created_at'        => new sfWidgetFormDateTime(),
//      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
//      'staff_resource_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResource'))),
      'organization_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agOrganization'))),
//      'created_at'        => new sfValidatorDateTime(),
//      'updated_at'        => new sfValidatorDateTime(),
    ));
  }
}
