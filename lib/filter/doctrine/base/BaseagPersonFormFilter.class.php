<?php

/**
 * agPerson filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagPersonFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'entity_id'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'), 'add_empty' => true)),
      'created_at'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
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
      'ag_staff_status_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agStaffStatus')),
      'ag_import_type_list'         => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agImportType')),
    ));

    $this->setValidators(array(
      'entity_id'                   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEntity'), 'column' => 'id')),
      'created_at'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
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
      'ag_staff_status_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaffStatus', 'required' => false)),
      'ag_import_type_list'         => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agImportType', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_person_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgNationalityListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonMjAgNationality agPersonMjAgNationality')
      ->andWhereIn('agPersonMjAgNationality.nationality_id', $values)
    ;
  }

  public function addAgReligionListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonMjAgReligion agPersonMjAgReligion')
      ->andWhereIn('agPersonMjAgReligion.religion_id', $values)
    ;
  }

  public function addAgProfessionListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonMjAgProfession agPersonMjAgProfession')
      ->andWhereIn('agPersonMjAgProfession.profession_id', $values)
    ;
  }

  public function addAgLanguageListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonMjAgLanguage agPersonMjAgLanguage')
      ->andWhereIn('agPersonMjAgLanguage.language_id', $values)
    ;
  }

  public function addAgCountryListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonResidentialStatus agPersonResidentialStatus')
      ->andWhereIn('agPersonResidentialStatus.country_id', $values)
    ;
  }

  public function addAgEthnicityListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonEthnicity agPersonEthnicity')
      ->andWhereIn('agPersonEthnicity.ethnicity_id', $values)
    ;
  }

  public function addAgSexListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonSex agPersonSex')
      ->andWhereIn('agPersonSex.sex_id', $values)
    ;
  }

  public function addAgMaritalStatusListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonMaritalStatus agPersonMaritalStatus')
      ->andWhereIn('agPersonMaritalStatus.marital_status_id', $values)
    ;
  }

  public function addAgImportListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonLastImport agPersonLastImport')
      ->andWhereIn('agPersonLastImport.last_import_id', $values)
    ;
  }

  public function addAgResidentialStatusListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonResidentialStatus agPersonResidentialStatus')
      ->andWhereIn('agPersonResidentialStatus.residential_status_id', $values)
    ;
  }

  public function addAgPersonNameListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonMjAgPersonName agPersonMjAgPersonName')
      ->andWhereIn('agPersonMjAgPersonName.person_name_id', $values)
    ;
  }

  public function addAgPersonNameTypeListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonMjAgPersonName agPersonMjAgPersonName')
      ->andWhereIn('agPersonMjAgPersonName.person_name_type_id', $values)
    ;
  }

  public function addAgPersonCustomFieldListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agPersonCustomFieldValue agPersonCustomFieldValue')
      ->andWhereIn('agPersonCustomFieldValue.person_custom_field_id', $values)
    ;
  }

  public function addAgStaffStatusListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agStaff agStaff')
      ->andWhereIn('agStaff.staff_status_id', $values)
    ;
  }

  public function addAgImportTypeListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agImport agImport')
      ->andWhereIn('agImport.import_type_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agPerson';
  }

  public function getFields()
  {
    return array(
      'id'                          => 'Number',
      'entity_id'                   => 'ForeignKey',
      'created_at'                  => 'Date',
      'updated_at'                  => 'Date',
      'ag_nationality_list'         => 'ManyKey',
      'ag_religion_list'            => 'ManyKey',
      'ag_profession_list'          => 'ManyKey',
      'ag_language_list'            => 'ManyKey',
      'ag_country_list'             => 'ManyKey',
      'ag_ethnicity_list'           => 'ManyKey',
      'ag_sex_list'                 => 'ManyKey',
      'ag_marital_status_list'      => 'ManyKey',
      'ag_import_list'              => 'ManyKey',
      'ag_residential_status_list'  => 'ManyKey',
      'ag_person_name_list'         => 'ManyKey',
      'ag_person_name_type_list'    => 'ManyKey',
      'ag_person_custom_field_list' => 'ManyKey',
      'ag_staff_status_list'        => 'ManyKey',
      'ag_import_type_list'         => 'ManyKey',
    );
  }
}
