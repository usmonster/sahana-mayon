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
      'id' => new sfWidgetFormInputHidden(),
      'staff_id' => new sfWidgetFormInputHidden(),//sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaff'), 'add_empty' => false)),
      'staff_resource_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => false)),
      //'created_at' => new sfWidgetFormDateTime(),
      //'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'staff_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaff'), 'required' => false)),
      'staff_resource_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'))),
      //'created_at' => new sfValidatorDateTime(),
      //'updated_at' => new sfValidatorDateTime(),
    ));
  }

}
