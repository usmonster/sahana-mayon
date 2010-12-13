<?php

/**
 * agPersonMjAgProfession form base class.
 *
 * @method agPersonMjAgProfession getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPersonMjAgProfessionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'person_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => false)),
      'profession_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agProfession'), 'add_empty' => false)),
      'title'         => new sfWidgetFormInputText(),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'))),
      'profession_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agProfession'))),
      'title'         => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agPersonMjAgProfession', 'column' => array('person_id', 'profession_id')))
    );

    $this->widgetSchema->setNameFormat('ag_person_mj_ag_profession[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPersonMjAgProfession';
  }

}