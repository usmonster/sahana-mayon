<?php

/**
 * agMessageType form base class.
 *
 * @method agMessageType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagMessageTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                           => new sfWidgetFormInputHidden(),
      'message_type'                 => new sfWidgetFormInputText(),
      'description'                  => new sfWidgetFormInputText(),
      'app_display'                  => new sfWidgetFormInputCheckbox(),
      'created_at'                   => new sfWidgetFormDateTime(),
      'updated_at'                   => new sfWidgetFormDateTime(),
      'ag_message_element_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageElementType')),
    ));

    $this->setValidators(array(
      'id'                           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'message_type'                 => new sfValidatorString(array('max_length' => 30)),
      'description'                  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_display'                  => new sfValidatorBoolean(array('required' => false)),
      'created_at'                   => new sfValidatorDateTime(),
      'updated_at'                   => new sfValidatorDateTime(),
      'ag_message_element_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageElementType', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agMessageType', 'column' => array('message_type')))
    );

    $this->widgetSchema->setNameFormat('ag_message_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agMessageType';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_message_element_type_list']))
    {
      $this->setDefault('ag_message_element_type_list', $this->object->agMessageElementType->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagMessageElementTypeList($con);

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

}
