<?php

/**
 * agRawSkillMap form base class.
 *
 * @method agRawSkillMap getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagRawSkillMapForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'raw_skill_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agRawSkill'), 'add_empty' => false)),
      'skill_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSkill'), 'add_empty' => false)),
      'created_at'   => new sfWidgetFormDateTime(),
      'updated_at'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'raw_skill_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agRawSkill'))),
      'skill_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agSkill'))),
      'created_at'   => new sfValidatorDateTime(),
      'updated_at'   => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agRawSkillMap', 'column' => array('raw_skill_id', 'skill_id')))
    );

    $this->widgetSchema->setNameFormat('ag_raw_skill_map[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agRawSkillMap';
  }

}