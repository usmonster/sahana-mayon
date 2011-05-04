<?php

/**
 * agNationality form base class.
 *
 * @method agNationality getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagNationalityForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'nationality'    => new sfWidgetFormInputText(),
      'description'    => new sfWidgetFormInputText(),
      'app_display'    => new sfWidgetFormInputCheckbox(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
      'ag_person_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPerson')),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'nationality'    => new sfValidatorString(array('max_length' => 128)),
      'description'    => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'app_display'    => new sfValidatorBoolean(array('required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
      'ag_person_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPerson', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agNationality', 'column' => array('nationality')))
    );

    $this->widgetSchema->setNameFormat('ag_nationality[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agNationality';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_person_list']))
    {
      $this->setDefault('ag_person_list', $this->object->agPerson->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagPersonList($con);

    parent::doSave($con);
  }

  public function saveagPersonList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_person_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agPerson->getPrimaryKeys();
    $values = $this->getValue('ag_person_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agPerson', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agPerson', array_values($link));
    }
  }

}
