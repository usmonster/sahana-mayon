<?php

/**
 * agIso31661Alpha2 form base class.
 *
 * @method agIso31661Alpha2 getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagIso31661Alpha2Form extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'value'           => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'ag_country_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agCountry')),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'value'           => new sfValidatorString(array('max_length' => 2)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
      'ag_country_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agCountry', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agIso31661Alpha2', 'column' => array('value')))
    );

    $this->widgetSchema->setNameFormat('ag_iso31661_alpha2[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agIso31661Alpha2';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_country_list']))
    {
      $this->setDefault('ag_country_list', $this->object->agCountry->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagCountryList($con);

    parent::doSave($con);
  }

  public function saveagCountryList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_country_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agCountry->getPrimaryKeys();
    $values = $this->getValue('ag_country_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agCountry', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agCountry', array_values($link));
    }
  }

}