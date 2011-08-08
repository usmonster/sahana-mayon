<?php

/**
 * agEntityAddressContact form base class.
 *
 * @method agEntityAddressContact getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEntityAddressContactForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'entity_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'), 'add_empty' => false)),
      'address_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddress'), 'add_empty' => false)),
      'address_contact_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressContactType'), 'add_empty' => false)),
      'priority'                => new sfWidgetFormInputText(),
      'created_at'              => new sfWidgetFormDateTime(),
      'updated_at'              => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'entity_id'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'))),
      'address_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddress'))),
      'address_contact_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressContactType'))),
      'priority'                => new sfValidatorInteger(),
      'created_at'              => new sfValidatorDateTime(),
      'updated_at'              => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agEntityAddressContact', 'column' => array('entity_id', 'address_contact_type_id', 'address_id'))),
        new sfValidatorDoctrineUnique(array('model' => 'agEntityAddressContact', 'column' => array('entity_id', 'priority'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_entity_address_contact[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEntityAddressContact';
  }

}
