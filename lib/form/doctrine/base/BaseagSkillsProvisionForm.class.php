<?php

/**
 * agSkillsProvision form base class.
 *
 * @method agSkillsProvision getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagSkillsProvisionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'skill_provision' => new sfWidgetFormInputText(),
      'description'     => new sfWidgetFormInputText(),
      'app_display'     => new sfWidgetFormInputCheckbox(),
      'priority'        => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'skill_provision' => new sfValidatorString(array('max_length' => 32)),
      'description'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_display'     => new sfValidatorBoolean(array('required' => false)),
      'priority'        => new sfValidatorInteger(),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agSkillsProvision', 'column' => array('skill_provision')))
    );

    $this->widgetSchema->setNameFormat('ag_skills_provision[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agSkillsProvision';
  }

}
