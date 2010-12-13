<?php

/**
 * agResidentialStatus filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagResidentialStatusFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'residential_status' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'        => new sfWidgetFormFilterInput(),
      'app_display'        => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_person_list'     => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPerson')),
      'ag_country_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agCountry')),
    ));

    $this->setValidators(array(
      'residential_status' => new sfValidatorPass(array('required' => false)),
      'description'        => new sfValidatorPass(array('required' => false)),
      'app_display'        => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_person_list'     => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPerson', 'required' => false)),
      'ag_country_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agCountry', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_residential_status_filters[%s]');

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

  public function getModelName()
  {
    return 'agResidentialStatus';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Number',
      'residential_status' => 'Text',
      'description'        => 'Text',
      'app_display'        => 'Boolean',
      'created_at'         => 'Date',
      'updated_at'         => 'Date',
      'ag_person_list'     => 'ManyKey',
      'ag_country_list'    => 'ManyKey',
    );
  }
}
