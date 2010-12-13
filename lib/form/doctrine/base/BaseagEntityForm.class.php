<?php

/**
 * agEntity form base class.
 *
 * @method agEntity getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEntityForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
      'ag_phone_contact_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPhoneContact')),
      'ag_email_contact_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEmailContact')),
      'ag_message_batch_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageBatch')),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'created_at'            => new sfValidatorDateTime(),
      'updated_at'            => new sfValidatorDateTime(),
      'ag_phone_contact_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPhoneContact', 'required' => false)),
      'ag_email_contact_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEmailContact', 'required' => false)),
      'ag_message_batch_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageBatch', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_entity[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEntity';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_phone_contact_list']))
    {
      $this->setDefault('ag_phone_contact_list', $this->object->agPhoneContact->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_email_contact_list']))
    {
      $this->setDefault('ag_email_contact_list', $this->object->agEmailContact->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_message_batch_list']))
    {
      $this->setDefault('ag_message_batch_list', $this->object->agMessageBatch->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagPhoneContactList($con);
    $this->saveagEmailContactList($con);
    $this->saveagMessageBatchList($con);

    parent::doSave($con);
  }

  public function saveagPhoneContactList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_phone_contact_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agPhoneContact->getPrimaryKeys();
    $values = $this->getValue('ag_phone_contact_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agPhoneContact', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agPhoneContact', array_values($link));
    }
  }

  public function saveagEmailContactList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_email_contact_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agEmailContact->getPrimaryKeys();
    $values = $this->getValue('ag_email_contact_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agEmailContact', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agEmailContact', array_values($link));
    }
  }

  public function saveagMessageBatchList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_message_batch_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agMessageBatch->getPrimaryKeys();
    $values = $this->getValue('ag_message_batch_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agMessageBatch', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agMessageBatch', array_values($link));
    }
  }

}