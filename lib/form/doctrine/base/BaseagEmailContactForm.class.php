<?php

/**
 * agEmailContact form base class.
 *
 * @method agEmailContact getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEmailContactForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                         => new sfWidgetFormInputHidden(),
      'email_contact'              => new sfWidgetFormInputText(),
      'created_at'                 => new sfWidgetFormDateTime(),
      'updated_at'                 => new sfWidgetFormDateTime(),
      'ag_email_contact_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEmailContactType')),
      'ag_entity_list'             => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEntity')),
    ));

    $this->setValidators(array(
      'id'                         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'email_contact'              => new sfValidatorString(array('max_length' => 255)),
      'created_at'                 => new sfValidatorDateTime(),
      'updated_at'                 => new sfValidatorDateTime(),
      'ag_email_contact_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEmailContactType', 'required' => false)),
      'ag_entity_list'             => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEntity', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEmailContact', 'column' => array('email_contact')))
    );

    $this->widgetSchema->setNameFormat('ag_email_contact[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEmailContact';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_email_contact_type_list']))
    {
      $this->setDefault('ag_email_contact_type_list', $this->object->agEmailContactType->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_entity_list']))
    {
      $this->setDefault('ag_entity_list', $this->object->agEntity->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagEmailContactTypeList($con);
    $this->saveagEntityList($con);

    parent::doSave($con);
  }

  public function saveagEmailContactTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_email_contact_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agEmailContactType->getPrimaryKeys();
    $values = $this->getValue('ag_email_contact_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agEmailContactType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agEmailContactType', array_values($link));
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