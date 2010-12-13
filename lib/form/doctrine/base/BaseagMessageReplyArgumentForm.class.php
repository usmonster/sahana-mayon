<?php

/**
 * agMessageReplyArgument form base class.
 *
 * @method agMessageReplyArgument getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagMessageReplyArgumentForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'reply_argument'         => new sfWidgetFormInputText(),
      'description'            => new sfWidgetFormInputText(),
      'created_at'             => new sfWidgetFormDateTime(),
      'updated_at'             => new sfWidgetFormDateTime(),
      'ag_batch_template_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agBatchTemplate')),
      'ag_message_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessage')),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'reply_argument'         => new sfValidatorString(array('max_length' => 64)),
      'description'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'             => new sfValidatorDateTime(),
      'updated_at'             => new sfValidatorDateTime(),
      'ag_batch_template_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agBatchTemplate', 'required' => false)),
      'ag_message_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessage', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agMessageReplyArgument', 'column' => array('reply_argument')))
    );

    $this->widgetSchema->setNameFormat('ag_message_reply_argument[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agMessageReplyArgument';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_batch_template_list']))
    {
      $this->setDefault('ag_batch_template_list', $this->object->agBatchTemplate->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_message_list']))
    {
      $this->setDefault('ag_message_list', $this->object->agMessage->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagBatchTemplateList($con);
    $this->saveagMessageList($con);

    parent::doSave($con);
  }

  public function saveagBatchTemplateList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_batch_template_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agBatchTemplate->getPrimaryKeys();
    $values = $this->getValue('ag_batch_template_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agBatchTemplate', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agBatchTemplate', array_values($link));
    }
  }

  public function saveagMessageList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_message_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agMessage->getPrimaryKeys();
    $values = $this->getValue('ag_message_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agMessage', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agMessage', array_values($link));
    }
  }

}