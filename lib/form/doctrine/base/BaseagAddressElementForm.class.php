<?php

/**
 * agAddressElement form base class.
 *
 * @method agAddressElement getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAddressElementForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'address_element'      => new sfWidgetFormInputText(),
      'description'          => new sfWidgetFormInputText(),
      'app_display'          => new sfWidgetFormInputCheckbox(),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_at'           => new sfWidgetFormDateTime(),
      'ag_address_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAddressStandard')),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'address_element'      => new sfValidatorString(array('max_length' => 128)),
      'description'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_display'          => new sfValidatorBoolean(array('required' => false)),
      'created_at'           => new sfValidatorDateTime(),
      'updated_at'           => new sfValidatorDateTime(),
      'ag_address_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAddressStandard', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agAddressElement', 'column' => array('address_element')))
    );

    $this->widgetSchema->setNameFormat('ag_address_element[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAddressElement';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_address_type_list']))
    {
      $this->setDefault('ag_address_type_list', $this->object->agAddressType->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagAddressTypeList($con);

    parent::doSave($con);
  }

  public function saveagAddressTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_address_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agAddressType->getPrimaryKeys();
    $values = $this->getValue('ag_address_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agAddressType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agAddressType', array_values($link));
    }
  }

}