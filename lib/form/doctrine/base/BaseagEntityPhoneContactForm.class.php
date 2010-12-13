<?php

/**
 * agEntityPhoneContact form base class.
 *
 * @method agEntityPhoneContact getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEntityPhoneContactForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'entity_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'), 'add_empty' => false)),
      'phone_contact_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPhoneContact'), 'add_empty' => false)),
      'phone_contact_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPhoneContactType'), 'add_empty' => false)),
      'priority'              => new sfWidgetFormInputText(),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'entity_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'))),
      'phone_contact_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPhoneContact'))),
      'phone_contact_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPhoneContactType'))),
      'priority'              => new sfValidatorInteger(),
      'created_at'            => new sfValidatorDateTime(),
      'updated_at'            => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agEntityPhoneContact', 'column' => array('entity_id', 'phone_contact_id', 'phone_contact_type_id'))),
        new sfValidatorDoctrineUnique(array('model' => 'agEntityPhoneContact', 'column' => array('entity_id', 'priority'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_entity_phone_contact[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEntityPhoneContact';
  }

}