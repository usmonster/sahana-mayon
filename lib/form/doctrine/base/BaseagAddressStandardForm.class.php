<?php

/**
 * agAddressStandard form base class.
 *
 * @method agAddressStandard getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAddressStandardForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'address_standard'        => new sfWidgetFormInputText(),
      'country_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agCountry'), 'add_empty' => false)),
      'description'             => new sfWidgetFormInputText(),
      'app_display'             => new sfWidgetFormInputCheckbox(),
      'created_at'              => new sfWidgetFormDateTime(),
      'updated_at'              => new sfWidgetFormDateTime(),
      'ag_address_element_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAddressElement')),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'address_standard'        => new sfValidatorString(array('max_length' => 128)),
      'country_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agCountry'))),
      'description'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_display'             => new sfValidatorBoolean(array('required' => false)),
      'created_at'              => new sfValidatorDateTime(),
      'updated_at'              => new sfValidatorDateTime(),
      'ag_address_element_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAddressElement', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agAddressStandard', 'column' => array('address_standard')))
    );

    $this->widgetSchema->setNameFormat('ag_address_standard[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAddressStandard';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_address_element_list']))
    {
      $this->setDefault('ag_address_element_list', $this->object->agAddressElement->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagAddressElementList($con);

    parent::doSave($con);
  }

  public function saveagAddressElementList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_address_element_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agAddressElement->getPrimaryKeys();
    $values = $this->getValue('ag_address_element_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agAddressElement', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agAddressElement', array_values($link));
    }
  }

}
