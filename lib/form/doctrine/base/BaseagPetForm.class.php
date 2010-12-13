<?php

/**
 * agPet form base class.
 *
 * @method agPet getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPetForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'pet_name'             => new sfWidgetFormInputText(),
      'event_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => false)),
      'sex_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSex'), 'add_empty' => false)),
      'species_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSpecies'), 'add_empty' => false)),
      'age'                  => new sfWidgetFormInputText(),
      'age_date_recorded'    => new sfWidgetFormDate(),
      'physical_description' => new sfWidgetFormInputText(),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_at'           => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'pet_name'             => new sfValidatorString(array('max_length' => 128)),
      'event_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'))),
      'sex_id'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agSex'))),
      'species_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agSpecies'))),
      'age'                  => new sfValidatorInteger(array('required' => false)),
      'age_date_recorded'    => new sfValidatorDate(array('required' => false)),
      'physical_description' => new sfValidatorPass(array('required' => false)),
      'created_at'           => new sfValidatorDateTime(),
      'updated_at'           => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('ag_pet[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPet';
  }

}