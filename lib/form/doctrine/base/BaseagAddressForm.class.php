<?php

/**
 * agAddress form base class.
 *
 * @method agAddress getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAddressForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                           => new sfWidgetFormInputHidden(),
      'address_standard_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressStandard'), 'add_empty' => false)),
      'address_hash'                 => new sfWidgetFormInputText(),
      'created_at'                   => new sfWidgetFormDateTime(),
      'updated_at'                   => new sfWidgetFormDateTime(),
      'ag_address_value_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAddressValue')),
      'ag_address_contact_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAddressContactType')),
    ));

    $this->setValidators(array(
      'id'                           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'address_standard_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressStandard'))),
      'address_hash'                 => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'created_at'                   => new sfValidatorDateTime(),
      'updated_at'                   => new sfValidatorDateTime(),
      'ag_address_value_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAddressValue', 'required' => false)),
      'ag_address_contact_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAddressContactType', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_address[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAddress';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_address_value_list']))
    {
      $this->setDefault('ag_address_value_list', $this->object->agAddressValue->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_address_contact_type_list']))
    {
      $this->setDefault('ag_address_contact_type_list', $this->object->agAddressContactType->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagAddressValueList($con);
    $this->saveagAddressContactTypeList($con);

    parent::doSave($con);
  }

  public function saveagAddressValueList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_address_value_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agAddressValue->getPrimaryKeys();
    $values = $this->getValue('ag_address_value_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agAddressValue', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agAddressValue', array_values($link));
    }
  }

  public function saveagAddressContactTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_address_contact_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agAddressContactType->getPrimaryKeys();
    $values = $this->getValue('ag_address_contact_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agAddressContactType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agAddressContactType', array_values($link));
    }
  }

}