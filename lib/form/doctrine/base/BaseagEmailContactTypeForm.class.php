<?php

/**
 * agEmailContactType form base class.
 *
 * @method agEmailContactType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEmailContactTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'email_contact_type'    => new sfWidgetFormInputText(),
      'app_display'           => new sfWidgetFormInputCheckbox(),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
      'ag_email_contact_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEmailContact')),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'email_contact_type'    => new sfValidatorString(array('max_length' => 32)),
      'app_display'           => new sfValidatorBoolean(array('required' => false)),
      'created_at'            => new sfValidatorDateTime(),
      'updated_at'            => new sfValidatorDateTime(),
      'ag_email_contact_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEmailContact', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEmailContactType', 'column' => array('email_contact_type')))
    );

    $this->widgetSchema->setNameFormat('ag_email_contact_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEmailContactType';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_email_contact_list']))
    {
      $this->setDefault('ag_email_contact_list', $this->object->agEmailContact->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagEmailContactList($con);

    parent::doSave($con);
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

}