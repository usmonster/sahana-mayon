<?php

/**
 * agMessage form base class.
 *
 * @method agMessage getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagMessageForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                              => new sfWidgetFormInputHidden(),
      'message_batch_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageBatch'), 'add_empty' => false)),
      'recipient_id'                    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'), 'add_empty' => false)),
      'created_at'                      => new sfWidgetFormDateTime(),
      'updated_at'                      => new sfWidgetFormDateTime(),
      'ag_messsage_reply_argument_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageReplyArgument')),
      'ag_message_element_type_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageElementType')),
    ));

    $this->setValidators(array(
      'id'                              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'message_batch_id'                => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageBatch'))),
      'recipient_id'                    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'))),
      'created_at'                      => new sfValidatorDateTime(),
      'updated_at'                      => new sfValidatorDateTime(),
      'ag_messsage_reply_argument_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageReplyArgument', 'required' => false)),
      'ag_message_element_type_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageElementType', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agMessage', 'column' => array('message_batch_id', 'recipient_id')))
    );

    $this->widgetSchema->setNameFormat('ag_message[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agMessage';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_messsage_reply_argument_list']))
    {
      $this->setDefault('ag_messsage_reply_argument_list', $this->object->agMesssageReplyArgument->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_message_element_type_list']))
    {
      $this->setDefault('ag_message_element_type_list', $this->object->agMessageElementType->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagMesssageReplyArgumentList($con);
    $this->saveagMessageElementTypeList($con);

    parent::doSave($con);
  }

  public function saveagMesssageReplyArgumentList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_messsage_reply_argument_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agMesssageReplyArgument->getPrimaryKeys();
    $values = $this->getValue('ag_messsage_reply_argument_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agMesssageReplyArgument', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agMesssageReplyArgument', array_values($link));
    }
  }

  public function saveagMessageElementTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_message_element_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agMessageElementType->getPrimaryKeys();
    $values = $this->getValue('ag_message_element_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agMessageElementType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agMessageElementType', array_values($link));
    }
  }

}