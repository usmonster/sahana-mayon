<?php

/**
 * agBatchTemplate form base class.
 *
 * @method agBatchTemplate getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagBatchTemplateForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                            => new sfWidgetFormInputHidden(),
      'batch_template'                => new sfWidgetFormInputText(),
      'description'                   => new sfWidgetFormInputText(),
      'app_display'                   => new sfWidgetFormInputCheckbox(),
      'reply_expected'                => new sfWidgetFormInputCheckbox(),
      'created_at'                    => new sfWidgetFormDateTime(),
      'updated_at'                    => new sfWidgetFormDateTime(),
      'ag_message_template_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageTemplate')),
      'ag_mesage_reply_argument_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageReplyArgument')),
    ));

    $this->setValidators(array(
      'id'                            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'batch_template'                => new sfValidatorString(array('max_length' => 30)),
      'description'                   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_display'                   => new sfValidatorBoolean(array('required' => false)),
      'reply_expected'                => new sfValidatorBoolean(),
      'created_at'                    => new sfValidatorDateTime(),
      'updated_at'                    => new sfValidatorDateTime(),
      'ag_message_template_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageTemplate', 'required' => false)),
      'ag_mesage_reply_argument_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageReplyArgument', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agBatchTemplate', 'column' => array('batch_template')))
    );

    $this->widgetSchema->setNameFormat('ag_batch_template[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agBatchTemplate';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_message_template_list']))
    {
      $this->setDefault('ag_message_template_list', $this->object->agMessageTemplate->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_mesage_reply_argument_list']))
    {
      $this->setDefault('ag_mesage_reply_argument_list', $this->object->agMesageReplyArgument->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagMessageTemplateList($con);
    $this->saveagMesageReplyArgumentList($con);

    parent::doSave($con);
  }

  public function saveagMessageTemplateList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_message_template_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agMessageTemplate->getPrimaryKeys();
    $values = $this->getValue('ag_message_template_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agMessageTemplate', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agMessageTemplate', array_values($link));
    }
  }

  public function saveagMesageReplyArgumentList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_mesage_reply_argument_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agMesageReplyArgument->getPrimaryKeys();
    $values = $this->getValue('ag_mesage_reply_argument_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agMesageReplyArgument', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agMesageReplyArgument', array_values($link));
    }
  }

}