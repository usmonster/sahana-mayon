<?php

/**
 * agStaffResource form.
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrinePluginFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PluginagEmbeddedAgStaffResourceForm extends PluginagStaffResourceForm
{

  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
//      'staff_id'                 => new sfWidgetFormInputHidden(),//sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaff'), 'add_empty' => false)),
      'staff_resource_type_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => false, 'method' => 'getStaffResourceType')),
      'staff_resource_status_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceStatus'), 'add_empty' => false, 'method' => 'getStaffResourceStatus')),
      'organization_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agOrganization'), 'add_empty' => false, 'method' => 'getOrganization')),
      //'created_at'               => new sfWidgetFormDateTime(),
      //'updated_at'               => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
  //    'staff_id'                 => new sfValidatorInteger(), //new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaff'))),
      'staff_resource_type_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'))),
      'staff_resource_status_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceStatus'))),
      'organization_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agOrganization'))),
      //'created_at'               => new sfValidatorDateTime(),
      //'updated_at'               => new sfValidatorDateTime(),
    ));
  }

}
