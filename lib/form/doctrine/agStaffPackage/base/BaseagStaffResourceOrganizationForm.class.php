<?php

/**
 * agStaffResourceOrganization form base class.
 *
 * @method agStaffResourceOrganization getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagStaffResourceOrganizationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'staff_resource_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResource'), 'add_empty' => false)),
      'organization_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agOrganization'), 'add_empty' => false)),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'staff_resource_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResource'))),
      'organization_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agOrganization'))),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agStaffResourceOrganization', 'column' => array('staff_resource_id')))
    );

    $this->widgetSchema->setNameFormat('ag_staff_resource_organization[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agStaffResourceOrganization';
  }

}