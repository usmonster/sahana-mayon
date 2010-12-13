<?php

/**
 * agPerson form base class.
 *
 * @method agPerson getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPersonForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                          => new sfWidgetFormInputHidden(),
      'entity_id'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'), 'add_empty' => false)),
      'created_at'                  => new sfWidgetFormDateTime(),
      'updated_at'                  => new sfWidgetFormDateTime(),
      'ag_nationality_list'         => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agNationality')),
      'ag_religion_list'            => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agReligion')),
      'ag_profession_list'          => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agProfession')),
      'ag_language_list'            => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agLanguage')),
      'ag_country_list'             => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agCountry')),
      'ag_ethnicity_list'           => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEthnicity')),
      'ag_sex_list'                 => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agSex')),
      'ag_marital_status_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMaritalStatus')),
      'ag_import_list'              => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agImport')),
      'ag_residential_status_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agResidentialStatus')),
      'ag_person_name_list'         => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPersonName')),
      'ag_person_name_type_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPersonNameType')),
      'ag_person_custom_field_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPersonCustomField')),
      'ag_import_type_list'         => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agImportType')),
      'ag_staff_status_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agStaffStatus')),
    ));

    $this->setValidators(array(
      'id'                          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'entity_id'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'))),
      'created_at'                  => new sfValidatorDateTime(),
      'updated_at'                  => new sfValidatorDateTime(),
      'ag_nationality_list'         => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agNationality', 'required' => false)),
      'ag_religion_list'            => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agReligion', 'required' => false)),
      'ag_profession_list'          => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agProfession', 'required' => false)),
      'ag_language_list'            => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agLanguage', 'required' => false)),
      'ag_country_list'             => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agCountry', 'required' => false)),
      'ag_ethnicity_list'           => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEthnicity', 'required' => false)),
      'ag_sex_list'                 => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agSex', 'required' => false)),
      'ag_marital_status_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMaritalStatus', 'required' => false)),
      'ag_import_list'              => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agImport', 'required' => false)),
      'ag_residential_status_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agResidentialStatus', 'required' => false)),
      'ag_person_name_list'         => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPersonName', 'required' => false)),
      'ag_person_name_type_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPersonNameType', 'required' => false)),
      'ag_person_custom_field_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPersonCustomField', 'required' => false)),
      'ag_import_type_list'         => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agImportType', 'required' => false)),
      'ag_staff_status_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaffStatus', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agPerson', 'column' => array('entity_id')))
    );

    $this->widgetSchema->setNameFormat('ag_person[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPerson';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_nationality_list']))
    {
      $this->setDefault('ag_nationality_list', $this->object->agNationality->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_religion_list']))
    {
      $this->setDefault('ag_religion_list', $this->object->agReligion->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_profession_list']))
    {
      $this->setDefault('ag_profession_list', $this->object->agProfession->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_language_list']))
    {
      $this->setDefault('ag_language_list', $this->object->agLanguage->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_country_list']))
    {
      $this->setDefault('ag_country_list', $this->object->agCountry->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_ethnicity_list']))
    {
      $this->setDefault('ag_ethnicity_list', $this->object->agEthnicity->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_sex_list']))
    {
      $this->setDefault('ag_sex_list', $this->object->agSex->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_marital_status_list']))
    {
      $this->setDefault('ag_marital_status_list', $this->object->agMaritalStatus->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_import_list']))
    {
      $this->setDefault('ag_import_list', $this->object->agImport->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_residential_status_list']))
    {
      $this->setDefault('ag_residential_status_list', $this->object->agResidentialStatus->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_person_name_list']))
    {
      $this->setDefault('ag_person_name_list', $this->object->agPersonName->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_person_name_type_list']))
    {
      $this->setDefault('ag_person_name_type_list', $this->object->agPersonNameType->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_person_custom_field_list']))
    {
      $this->setDefault('ag_person_custom_field_list', $this->object->agPersonCustomField->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_import_type_list']))
    {
      $this->setDefault('ag_import_type_list', $this->object->agImportType->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_staff_status_list']))
    {
      $this->setDefault('ag_staff_status_list', $this->object->agStaffStatus->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagNationalityList($con);
    $this->saveagReligionList($con);
    $this->saveagProfessionList($con);
    $this->saveagLanguageList($con);
    $this->saveagCountryList($con);
    $this->saveagEthnicityList($con);
    $this->saveagSexList($con);
    $this->saveagMaritalStatusList($con);
    $this->saveagImportList($con);
    $this->saveagResidentialStatusList($con);
    $this->saveagPersonNameList($con);
    $this->saveagPersonNameTypeList($con);
    $this->saveagPersonCustomFieldList($con);
    $this->saveagImportTypeList($con);
    $this->saveagStaffStatusList($con);

    parent::doSave($con);
  }

  public function saveagNationalityList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_nationality_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agNationality->getPrimaryKeys();
    $values = $this->getValue('ag_nationality_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agNationality', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agNationality', array_values($link));
    }
  }

  public function saveagReligionList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_religion_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agReligion->getPrimaryKeys();
    $values = $this->getValue('ag_religion_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agReligion', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agReligion', array_values($link));
    }
  }

  public function saveagProfessionList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_profession_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agProfession->getPrimaryKeys();
    $values = $this->getValue('ag_profession_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agProfession', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agProfession', array_values($link));
    }
  }

  public function saveagLanguageList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_language_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agLanguage->getPrimaryKeys();
    $values = $this->getValue('ag_language_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agLanguage', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agLanguage', array_values($link));
    }
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

  public function saveagEthnicityList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_ethnicity_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agEthnicity->getPrimaryKeys();
    $values = $this->getValue('ag_ethnicity_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agEthnicity', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agEthnicity', array_values($link));
    }
  }

  public function saveagSexList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_sex_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agSex->getPrimaryKeys();
    $values = $this->getValue('ag_sex_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agSex', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agSex', array_values($link));
    }
  }

  public function saveagMaritalStatusList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_marital_status_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agMaritalStatus->getPrimaryKeys();
    $values = $this->getValue('ag_marital_status_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agMaritalStatus', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agMaritalStatus', array_values($link));
    }
  }

  public function saveagImportList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_import_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agImport->getPrimaryKeys();
    $values = $this->getValue('ag_import_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agImport', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agImport', array_values($link));
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

  public function saveagPersonNameList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_person_name_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agPersonName->getPrimaryKeys();
    $values = $this->getValue('ag_person_name_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agPersonName', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agPersonName', array_values($link));
    }
  }

  public function saveagPersonNameTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_person_name_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agPersonNameType->getPrimaryKeys();
    $values = $this->getValue('ag_person_name_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agPersonNameType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agPersonNameType', array_values($link));
    }
  }

  public function saveagPersonCustomFieldList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_person_custom_field_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agPersonCustomField->getPrimaryKeys();
    $values = $this->getValue('ag_person_custom_field_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agPersonCustomField', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agPersonCustomField', array_values($link));
    }
  }

  public function saveagImportTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_import_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agImportType->getPrimaryKeys();
    $values = $this->getValue('ag_import_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agImportType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agImportType', array_values($link));
    }
  }

  public function saveagStaffStatusList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_staff_status_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agStaffStatus->getPrimaryKeys();
    $values = $this->getValue('ag_staff_status_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agStaffStatus', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agStaffStatus', array_values($link));
    }
  }

}