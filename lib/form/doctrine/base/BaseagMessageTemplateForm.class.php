<?php

/**
 * agMessageTemplate form base class.
 *
 * @method agMessageTemplate getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagMessageTemplateForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                           => new sfWidgetFormInputHidden(),
      'message_template'             => new sfWidgetFormInputText(),
      'message_type_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessage'), 'add_empty' => false)),
      'created_at'                   => new sfWidgetFormDateTime(),
      'updated_at'                   => new sfWidgetFormDateTime(),
      'ag_message_element_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageElementType')),
      'ag_batch_template_list'       => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agBatchTemplate')),
    ));

    $this->setValidators(array(
      'id'                           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'message_template'             => new sfValidatorString(array('max_length' => 64)),
      'message_type_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessage'))),
      'created_at'                   => new sfValidatorDateTime(),
      'updated_at'                   => new sfValidatorDateTime(),
      'ag_message_element_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageElementType', 'required' => false)),
      'ag_batch_template_list'       => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agBatchTemplate', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agMessageTemplate', 'column' => array('message_template')))
    );

    $this->widgetSchema->setNameFormat('ag_message_template[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agMessageTemplate';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_message_element_type_list']))
    {
      $this->setDefault('ag_message_element_type_list', $this->object->agMessageElementType->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_batch_template_list']))
    {
      $this->setDefault('ag_batch_template_list', $this->object->agBatchTemplate->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagMessageElementTypeList($con);
    $this->saveagBatchTemplateList($con);

    parent::doSave($con);
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

}