<?php

/**
 * agPetCaretaker form base class.
 *
 * @method agPetCaretaker getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPetCaretakerForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'pet_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPet'), 'add_empty' => false)),
      'entity_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'), 'add_empty' => false)),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'pet_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPet'))),
      'entity_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'))),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agPetCaretaker', 'column' => array('pet_id')))
    );

    $this->widgetSchema->setNameFormat('ag_pet_caretaker[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPetCaretaker';
  }

}