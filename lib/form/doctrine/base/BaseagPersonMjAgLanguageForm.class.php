<?php

/**
 * agPersonMjAgLanguage form base class.
 *
 * @method agPersonMjAgLanguage getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPersonMjAgLanguageForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                          => new sfWidgetFormInputHidden(),
      'person_id'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => false)),
      'language_id'                 => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agLanguage'), 'add_empty' => false)),
      'priority'                    => new sfWidgetFormInputText(),
      'created_at'                  => new sfWidgetFormDateTime(),
      'updated_at'                  => new sfWidgetFormDateTime(),
      'ag_language_format_list'     => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageFormat')),
      'ag_language_competency_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageCompetency')),
    ));

    $this->setValidators(array(
      'id'                          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_id'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'))),
      'language_id'                 => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agLanguage'))),
      'priority'                    => new sfValidatorInteger(),
      'created_at'                  => new sfValidatorDateTime(),
      'updated_at'                  => new sfValidatorDateTime(),
      'ag_language_format_list'     => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageFormat', 'required' => false)),
      'ag_language_competency_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageCompetency', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agPersonMjAgLanguage', 'column' => array('person_id', 'language_id'))),
        new sfValidatorDoctrineUnique(array('model' => 'agPersonMjAgLanguage', 'column' => array('person_id', 'priority'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_person_mj_ag_language[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPersonMjAgLanguage';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_language_format_list']))
    {
      $this->setDefault('ag_language_format_list', $this->object->agLanguageFormat->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_language_competency_list']))
    {
      $this->setDefault('ag_language_competency_list', $this->object->agLanguageCompetency->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagLanguageFormatList($con);
    $this->saveagLanguageCompetencyList($con);

    parent::doSave($con);
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

  public function saveagLanguageCompetencyList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_language_competency_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agLanguageCompetency->getPrimaryKeys();
    $values = $this->getValue('ag_language_competency_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agLanguageCompetency', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agLanguageCompetency', array_values($link));
    }
  }

}