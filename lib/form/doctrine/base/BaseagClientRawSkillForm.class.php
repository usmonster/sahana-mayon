<?php

/**
 * agClientRawSkill form base class.
 *
 * @method agClientRawSkill getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagClientRawSkillForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'raw_skill_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agRawSkill'), 'add_empty' => false)),
      'client_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClient'), 'add_empty' => false)),
      'created_at'   => new sfWidgetFormDateTime(),
      'updated_at'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'raw_skill_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agRawSkill'))),
      'client_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agClient'))),
      'created_at'   => new sfValidatorDateTime(),
      'updated_at'   => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agClientRawSkill', 'column' => array('raw_skill_id', 'client_id')))
    );

    $this->widgetSchema->setNameFormat('ag_client_raw_skill[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agClientRawSkill';
  }

}