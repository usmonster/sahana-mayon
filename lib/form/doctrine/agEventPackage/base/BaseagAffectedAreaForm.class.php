<?php

/**
 * agAffectedArea form base class.
 *
 * @method agAffectedArea getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAffectedAreaForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'affected_area'       => new sfWidgetFormInputText(),
      'geo_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'), 'add_empty' => false)),
      'required_evacuation' => new sfWidgetFormInputCheckbox(),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
      'ag_event_list'       => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEvent')),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'affected_area'       => new sfValidatorString(array('max_length' => 64)),
      'geo_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'))),
      'required_evacuation' => new sfValidatorBoolean(),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
      'ag_event_list'       => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEvent', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agAffectedArea', 'column' => array('affected_area')))
    );

    $this->widgetSchema->setNameFormat('ag_affected_area[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAffectedArea';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_event_list']))
    {
      $this->setDefault('ag_event_list', $this->object->agEvent->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagEventList($con);

    parent::doSave($con);
  }

  public function saveagEventList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_event_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agEvent->getPrimaryKeys();
    $values = $this->getValue('ag_event_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agEvent', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agEvent', array_values($link));
    }
  }

}
