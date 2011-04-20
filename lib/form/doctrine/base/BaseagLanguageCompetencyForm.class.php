<?php

/**
 * agLanguageCompetency form base class.
 *
 * @method agLanguageCompetency getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagLanguageCompetencyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                            => new sfWidgetFormInputHidden(),
      'language_competency'           => new sfWidgetFormInputText(),
      'app_display'                   => new sfWidgetFormInputCheckbox(),
      'created_at'                    => new sfWidgetFormDateTime(),
      'updated_at'                    => new sfWidgetFormDateTime(),
      'ag_person_mj_ag_language_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPersonMjAgLanguage')),
      'ag_language_format_list'       => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageFormat')),
    ));

    $this->setValidators(array(
      'id'                            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'language_competency'           => new sfValidatorString(array('max_length' => 64)),
      'app_display'                   => new sfValidatorBoolean(array('required' => false)),
      'created_at'                    => new sfValidatorDateTime(),
      'updated_at'                    => new sfValidatorDateTime(),
      'ag_person_mj_ag_language_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPersonMjAgLanguage', 'required' => false)),
      'ag_language_format_list'       => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageFormat', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agLanguageCompetency', 'column' => array('language_competency')))
    );

    $this->widgetSchema->setNameFormat('ag_language_competency[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agLanguageCompetency';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_person_mj_ag_language_list']))
    {
      $this->setDefault('ag_person_mj_ag_language_list', $this->object->agPersonMjAgLanguage->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_language_format_list']))
    {
      $this->setDefault('ag_language_format_list', $this->object->agLanguageFormat->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagPersonMjAgLanguageList($con);
    $this->saveagLanguageFormatList($con);

    parent::doSave($con);
  }

  public function saveagPersonMjAgLanguageList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_person_mj_ag_language_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agPersonMjAgLanguage->getPrimaryKeys();
    $values = $this->getValue('ag_person_mj_ag_language_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agPersonMjAgLanguage', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agPersonMjAgLanguage', array_values($link));
    }
  }

  public function saveagLanguageFormatList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_language_format_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agLanguageFormat->getPrimaryKeys();
    $values = $this->getValue('ag_language_format_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agLanguageFormat', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agLanguageFormat', array_values($link));
    }
  }

}
