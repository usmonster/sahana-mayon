<?php

/**
 * agStaffResourceTypeProvision form base class.
 *
 * @method agStaffResourceTypeProvision getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagStaffResourceTypeProvisionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'staff_resource_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => false)),
      'skill_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSkill'), 'add_empty' => false)),
      'skills_provision_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSkillsProvision'), 'add_empty' => false)),
      'created_at'             => new sfWidgetFormDateTime(),
      'updated_at'             => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'staff_resource_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'))),
      'skill_id'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agSkill'))),
      'skills_provision_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agSkillsProvision'))),
      'created_at'             => new sfValidatorDateTime(),
      'updated_at'             => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agStaffResourceTypeProvision', 'column' => array('staff_resource_type_id', 'skill_id')))
    );

    $this->widgetSchema->setNameFormat('ag_staff_resource_type_provision[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agStaffResourceTypeProvision';
  }

}
