<?php

/**
 * agAddressFormat form base class.
 *
 * @method agAddressFormat getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAddressFormatForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'address_standard_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressStandard'), 'add_empty' => false)),
      'address_element_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressElement'), 'add_empty' => false)),
      'line_sequence'       => new sfWidgetFormInputText(),
      'inline_sequence'     => new sfWidgetFormInputText(),
      'pre_delimiter'       => new sfWidgetFormInputText(),
      'post_delimiter'      => new sfWidgetFormInputText(),
      'field_type_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFieldType'), 'add_empty' => false)),
      'is_required'         => new sfWidgetFormInputCheckbox(),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'address_standard_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressStandard'))),
      'address_element_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressElement'))),
      'line_sequence'       => new sfValidatorInteger(),
      'inline_sequence'     => new sfValidatorInteger(),
      'pre_delimiter'       => new sfValidatorString(array('max_length' => 32, 'required' => false)),
      'post_delimiter'      => new sfValidatorString(array('max_length' => 32, 'required' => false)),
      'field_type_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFieldType'))),
      'is_required'         => new sfValidatorBoolean(),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('ag_address_format[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAddressFormat';
  }

}