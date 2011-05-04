<?php

/**
 * agAddressValue form base class.
 *
 * @method agAddressValue getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAddressValueForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'address_element_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressElement'), 'add_empty' => false)),
      'value'              => new sfWidgetFormInputText(),
      'created_at'         => new sfWidgetFormDateTime(),
      'updated_at'         => new sfWidgetFormDateTime(),
      'ag_address_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAddress')),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'address_element_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressElement'))),
      'value'              => new sfValidatorString(array('max_length' => 255)),
      'created_at'         => new sfValidatorDateTime(),
      'updated_at'         => new sfValidatorDateTime(),
      'ag_address_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAddress', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agAddressValue', 'column' => array('value', 'address_element_id')))
    );

    $this->widgetSchema->setNameFormat('ag_address_value[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAddressValue';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_address_list']))
    {
      $this->setDefault('ag_address_list', $this->object->agAddress->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagAddressList($con);

    parent::doSave($con);
  }

  public function saveagAddressList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_address_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agAddress->getPrimaryKeys();
    $values = $this->getValue('ag_address_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agAddress', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agAddress', array_values($link));
    }
  }

}
