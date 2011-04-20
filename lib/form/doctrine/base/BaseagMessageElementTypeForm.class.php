<?php

/**
 * agMessageElementType form base class.
 *
 * @method agMessageElementType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagMessageElementTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'message_element_type'     => new sfWidgetFormInputText(),
      'description'              => new sfWidgetFormInputText(),
      'app_display'              => new sfWidgetFormInputCheckbox(),
      'created_at'               => new sfWidgetFormDateTime(),
      'updated_at'               => new sfWidgetFormDateTime(),
      'ag_message_type_list'     => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageType')),
      'ag_message_template_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageTemplate')),
      'ag_message_list'          => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessage')),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'message_element_type'     => new sfValidatorString(array('max_length' => 30)),
      'description'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_display'              => new sfValidatorBoolean(array('required' => false)),
      'created_at'               => new sfValidatorDateTime(),
      'updated_at'               => new sfValidatorDateTime(),
      'ag_message_type_list'     => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageType', 'required' => false)),
      'ag_message_template_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageTemplate', 'required' => false)),
      'ag_message_list'          => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessage', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agMessageElementType', 'column' => array('message_element_type')))
    );

    $this->widgetSchema->setNameFormat('ag_message_element_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agMessageElementType';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_message_type_list']))
    {
      $this->setDefault('ag_message_type_list', $this->object->agMessageType->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_message_template_list']))
    {
      $this->setDefault('ag_message_template_list', $this->object->agMessageTemplate->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_message_list']))
    {
      $this->setDefault('ag_message_list', $this->object->agMessage->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagMessageTypeList($con);
    $this->saveagMessageTemplateList($con);
    $this->saveagMessageList($con);

    parent::doSave($con);
  }

  public function saveagMessageTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_message_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agMessageType->getPrimaryKeys();
    $values = $this->getValue('ag_message_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agMessageType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agMessageType', array_values($link));
    }
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
