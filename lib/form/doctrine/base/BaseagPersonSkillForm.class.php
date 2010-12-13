<?php

/**
 * agPersonSkill form base class.
 *
 * @method agPersonSkill getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPersonSkillForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'person_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => false)),
      'skill_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSkill'), 'add_empty' => false)),
      'competency_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agCompetency'), 'add_empty' => false)),
      'date_expires'  => new sfWidgetFormDate(),
      'confirmed'     => new sfWidgetFormInputCheckbox(),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'))),
      'skill_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agSkill'))),
      'competency_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agCompetency'))),
      'date_expires'  => new sfValidatorDate(array('required' => false)),
      'confirmed'     => new sfValidatorBoolean(array('required' => false)),
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agPersonSkill', 'column' => array('person_id', 'skill_id')))
    );

    $this->widgetSchema->setNameFormat('ag_person_skill[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPersonSkill';
  }

}