<?php

/**
 * agEvent form base class.
 *
 * @method agEvent getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'event_name'            => new sfWidgetFormInputText(),
      'zero_hour'             => new sfWidgetFormDateTime(),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
      'ag_affected_area_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAffectedArea')),
      'ag_scenario_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agScenario')),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_name'            => new sfValidatorString(array('max_length' => 64)),
      'zero_hour'             => new sfValidatorDateTime(array('required' => false)),
      'created_at'            => new sfValidatorDateTime(),
      'updated_at'            => new sfValidatorDateTime(),
      'ag_affected_area_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAffectedArea', 'required' => false)),
      'ag_scenario_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agScenario', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEvent', 'column' => array('event_name')))
    );

    $this->widgetSchema->setNameFormat('ag_event[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEvent';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_affected_area_list']))
    {
      $this->setDefault('ag_affected_area_list', $this->object->agAffectedArea->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_scenario_list']))
    {
      $this->setDefault('ag_scenario_list', $this->object->agScenario->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagAffectedAreaList($con);
    $this->saveagScenarioList($con);

    parent::doSave($con);
  }

  public function saveagAffectedAreaList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_affected_area_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agAffectedArea->getPrimaryKeys();
    $values = $this->getValue('ag_affected_area_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agAffectedArea', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agAffectedArea', array_values($link));
    }
  }

  public function saveagScenarioList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_scenario_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agScenario->getPrimaryKeys();
    $values = $this->getValue('ag_scenario_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agScenario', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agScenario', array_values($link));
    }
  }

}