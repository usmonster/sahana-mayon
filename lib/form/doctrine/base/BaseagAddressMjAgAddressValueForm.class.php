<?php

/**
 * agAddressMjAgAddressValue form base class.
 *
 * @method agAddressMjAgAddressValue getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAddressMjAgAddressValueForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'address_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddress'), 'add_empty' => false)),
      'address_value_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressValue'), 'add_empty' => false)),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'address_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddress'))),
      'address_value_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressValue'))),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agAddressMjAgAddressValue', 'column' => array('address_id', 'address_value_id')))
    );

    $this->widgetSchema->setNameFormat('ag_address_mj_ag_address_value[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAddressMjAgAddressValue';
  }

}
