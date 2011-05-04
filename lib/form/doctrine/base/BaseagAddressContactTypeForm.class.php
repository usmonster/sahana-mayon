<?php

/**
 * agAddressContactType form base class.
 *
 * @method agAddressContactType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAddressContactTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'address_contact_type' => new sfWidgetFormInputText(),
      'app_display'          => new sfWidgetFormInputCheckbox(),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_at'           => new sfWidgetFormDateTime(),
      'ag_site_address_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAddress')),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'address_contact_type' => new sfValidatorString(array('max_length' => 32)),
      'app_display'          => new sfValidatorBoolean(array('required' => false)),
      'created_at'           => new sfValidatorDateTime(),
      'updated_at'           => new sfValidatorDateTime(),
      'ag_site_address_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAddress', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agAddressContactType', 'column' => array('address_contact_type')))
    );

    $this->widgetSchema->setNameFormat('ag_address_contact_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAddressContactType';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_site_address_list']))
    {
      $this->setDefault('ag_site_address_list', $this->object->agSiteAddress->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagSiteAddressList($con);

    parent::doSave($con);
  }

  public function saveagSiteAddressList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_site_address_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agSiteAddress->getPrimaryKeys();
    $values = $this->getValue('ag_site_address_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agSiteAddress', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agSiteAddress', array_values($link));
    }
  }

}
