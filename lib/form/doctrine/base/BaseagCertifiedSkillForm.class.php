<?php

/**
 * agCertifiedSkill form base class.
 *
 * @method agCertifiedSkill getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagCertifiedSkillForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'certification_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agCertification'), 'add_empty' => false)),
      'skill_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSkill'), 'add_empty' => false)),
      'competency_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agCompetency'), 'add_empty' => false)),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'certification_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agCertification'))),
      'skill_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agSkill'))),
      'competency_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agCompetency'))),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agCertifiedSkill', 'column' => array('certification_id', 'skill_id', 'competency_id')))
    );

    $this->widgetSchema->setNameFormat('ag_certified_skill[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agCertifiedSkill';
  }

}
