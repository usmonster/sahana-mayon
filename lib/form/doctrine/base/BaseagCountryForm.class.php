<?php

/**
 * agCountry form base class.
 *
 * @method agCountry getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagCountryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                         => new sfWidgetFormInputHidden(),
      'country'                    => new sfWidgetFormInputText(),
      'app_display'                => new sfWidgetFormInputCheckbox(),
      'created_at'                 => new sfWidgetFormDateTime(),
      'updated_at'                 => new sfWidgetFormDateTime(),
      'ag_person_list'             => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPerson')),
      'ag_residential_status_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agResidentialStatus')),
      'ag_iso31662_list'           => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agIso31662')),
      'ag_iso31661_alpha2_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Alpha2')),
      'ag_iso31661_alpha3_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Alpha3')),
      'ag_iso31661_numeric_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Numeric')),
    ));

    $this->setValidators(array(
      'id'                         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'country'                    => new sfValidatorString(array('max_length' => 128)),
      'app_display'                => new sfValidatorBoolean(array('required' => false)),
      'created_at'                 => new sfValidatorDateTime(),
      'updated_at'                 => new sfValidatorDateTime(),
      'ag_person_list'             => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPerson', 'required' => false)),
      'ag_residential_status_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agResidentialStatus', 'required' => false)),
      'ag_iso31662_list'           => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agIso31662', 'required' => false)),
      'ag_iso31661_alpha2_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Alpha2', 'required' => false)),
      'ag_iso31661_alpha3_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Alpha3', 'required' => false)),
      'ag_iso31661_numeric_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Numeric', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agCountry', 'column' => array('country')))
    );

    $this->widgetSchema->setNameFormat('ag_country[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agCountry';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_person_list']))
    {
      $this->setDefault('ag_person_list', $this->object->agPerson->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_residential_status_list']))
    {
      $this->setDefault('ag_residential_status_list', $this->object->agResidentialStatus->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_iso31662_list']))
    {
      $this->setDefault('ag_iso31662_list', $this->object->agIso31662->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_iso31661_alpha2_list']))
    {
      $this->setDefault('ag_iso31661_alpha2_list', $this->object->agIso31661Alpha2->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_iso31661_alpha3_list']))
    {
      $this->setDefault('ag_iso31661_alpha3_list', $this->object->agIso31661Alpha3->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_iso31661_numeric_list']))
    {
      $this->setDefault('ag_iso31661_numeric_list', $this->object->agIso31661Numeric->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagPersonList($con);
    $this->saveagResidentialStatusList($con);
    $this->saveagIso31662List($con);
    $this->saveagIso31661Alpha2List($con);
    $this->saveagIso31661Alpha3List($con);
    $this->saveagIso31661NumericList($con);

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

  public function saveagResidentialStatusList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_residential_status_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agResidentialStatus->getPrimaryKeys();
    $values = $this->getValue('ag_residential_status_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agResidentialStatus', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agResidentialStatus', array_values($link));
    }
  }

  public function saveagIso31662List($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_iso31662_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agIso31662->getPrimaryKeys();
    $values = $this->getValue('ag_iso31662_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agIso31662', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agIso31662', array_values($link));
    }
  }

  public function saveagIso31661Alpha2List($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_iso31661_alpha2_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agIso31661Alpha2->getPrimaryKeys();
    $values = $this->getValue('ag_iso31661_alpha2_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agIso31661Alpha2', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agIso31661Alpha2', array_values($link));
    }
  }

  public function saveagIso31661Alpha3List($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_iso31661_alpha3_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agIso31661Alpha3->getPrimaryKeys();
    $values = $this->getValue('ag_iso31661_alpha3_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agIso31661Alpha3', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agIso31661Alpha3', array_values($link));
    }
  }

  public function saveagIso31661NumericList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_iso31661_numeric_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agIso31661Numeric->getPrimaryKeys();
    $values = $this->getValue('ag_iso31661_numeric_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agIso31661Numeric', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agIso31661Numeric', array_values($link));
    }
  }

}
