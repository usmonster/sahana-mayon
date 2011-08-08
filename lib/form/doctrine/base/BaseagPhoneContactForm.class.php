<?php

/**
 * agPhoneContact form base class.
 *
 * @method agPhoneContact getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPhoneContactForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                         => new sfWidgetFormInputHidden(),
      'phone_contact'              => new sfWidgetFormInputText(),
      'phone_format_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPhoneFormat'), 'add_empty' => false)),
      'created_at'                 => new sfWidgetFormDateTime(),
      'updated_at'                 => new sfWidgetFormDateTime(),
      'ag_phone_contact_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPhoneContactType')),
      'ag_entity_list'             => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEntity')),
    ));

    $this->setValidators(array(
      'id'                         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'phone_contact'              => new sfValidatorString(array('max_length' => 16)),
      'phone_format_id'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPhoneFormat'))),
      'created_at'                 => new sfValidatorDateTime(),
      'updated_at'                 => new sfValidatorDateTime(),
      'ag_phone_contact_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPhoneContactType', 'required' => false)),
      'ag_entity_list'             => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEntity', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agPhoneContact', 'column' => array('phone_contact')))
    );

    $this->widgetSchema->setNameFormat('ag_phone_contact[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPhoneContact';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_phone_contact_type_list']))
    {
      $this->setDefault('ag_phone_contact_type_list', $this->object->agPhoneContactType->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_entity_list']))
    {
      $this->setDefault('ag_entity_list', $this->object->agEntity->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagPhoneContactTypeList($con);
    $this->saveagEntityList($con);

    parent::doSave($con);
  }

  public function saveagPhoneContactTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_phone_contact_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agPhoneContactType->getPrimaryKeys();
    $values = $this->getValue('ag_phone_contact_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agPhoneContactType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agPhoneContactType', array_values($link));
    }
  }

  public function saveagEntityList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_entity_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agEntity->getPrimaryKeys();
    $values = $this->getValue('ag_entity_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agEntity', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agEntity', array_values($link));
    }
  }

}
