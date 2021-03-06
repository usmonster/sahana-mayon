<?php

/**
 * agPersonCustomFieldValue form base class.
 *
 * @method agPersonCustomFieldValue getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPersonCustomFieldValueForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'person_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => false)),
      'person_custom_field_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPersonCustomField'), 'add_empty' => false)),
      'value'                  => new sfWidgetFormInputText(),
      'created_at'             => new sfWidgetFormDateTime(),
      'updated_at'             => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'))),
      'person_custom_field_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPersonCustomField'))),
      'value'                  => new sfValidatorString(array('max_length' => 255)),
      'created_at'             => new sfValidatorDateTime(),
      'updated_at'             => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agPersonCustomFieldValue', 'column' => array('person_id', 'person_custom_field_id')))
    );

    $this->widgetSchema->setNameFormat('ag_person_custom_field_value[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPersonCustomFieldValue';
  }

}
