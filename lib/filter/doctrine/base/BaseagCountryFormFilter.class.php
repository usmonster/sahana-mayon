<?php

/**
 * agCountry filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagCountryFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'country'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'app_display'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_person_list'             => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPerson')),
      'ag_residential_status_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agResidentialStatus')),
      'ag_iso31662_list'           => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agIso31662')),
      'ag_iso31661_alpha2_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Alpha2')),
      'ag_iso31661_alpha3_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Alpha3')),
      'ag_iso31661_numeric_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Numeric')),
    ));

    $this->setValidators(array(
      'country'                    => new sfValidatorPass(array('required' => false)),
      'app_display'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_person_list'             => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPerson', 'required' => false)),
      'ag_residential_status_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agResidentialStatus', 'required' => false)),
      'ag_iso31662_list'           => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agIso31662', 'required' => false)),
      'ag_iso31661_alpha2_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Alpha2', 'required' => false)),
      'ag_iso31661_alpha3_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Alpha3', 'required' => false)),
      'ag_iso31661_numeric_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agIso31661Numeric', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_country_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgPersonListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agPersonResidentialStatus.person_id', $values)
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

  public function addAgIso31662ListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agCountryMjAgIso31662 agCountryMjAgIso31662')
      ->andWhereIn('agCountryMjAgIso31662.iso_31662_id', $values)
    ;
  }

  public function addAgIso31661Alpha2ListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agCountryMjAgIso31661Alpha2 agCountryMjAgIso31661Alpha2')
      ->andWhereIn('agCountryMjAgIso31661Alpha2.iso_31661_alpha2_id', $values)
    ;
  }

  public function addAgIso31661Alpha3ListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agCountryMjAgIso31661Alpha3 agCountryMjAgIso31661Alpha3')
      ->andWhereIn('agCountryMjAgIso31661Alpha3.iso_31661_alpha3_id', $values)
    ;
  }

  public function addAgIso31661NumericListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agCountryMjAgIso31661Numeric agCountryMjAgIso31661Numeric')
      ->andWhereIn('agCountryMjAgIso31661Numeric.iso_31661_numeric_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agCountry';
  }

  public function getFields()
  {
    return array(
      'id'                         => 'Number',
      'country'                    => 'Text',
      'app_display'                => 'Boolean',
      'created_at'                 => 'Date',
      'updated_at'                 => 'Date',
      'ag_person_list'             => 'ManyKey',
      'ag_residential_status_list' => 'ManyKey',
      'ag_iso31662_list'           => 'ManyKey',
      'ag_iso31661_alpha2_list'    => 'ManyKey',
      'ag_iso31661_alpha3_list'    => 'ManyKey',
      'ag_iso31661_numeric_list'   => 'ManyKey',
    );
  }
}
