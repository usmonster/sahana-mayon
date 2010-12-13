<?php

/**
 * agAddressAlias form base class.
 *
 * @method agAddressAlias getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAddressAliasForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'address_value_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressValue'), 'add_empty' => false)),
      'alias'            => new sfWidgetFormInputText(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'address_value_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressValue'))),
      'alias'            => new sfValidatorString(array('max_length' => 64)),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agAddressAlias', 'column' => array('address_value_id', 'alias')))
    );

    $this->widgetSchema->setNameFormat('ag_address_alias[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAddressAlias';
  }

}