<?php

/**
 * agIso31661Alpha2 filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagIso31661Alpha2FormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'value'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_country_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agCountry')),
    ));

    $this->setValidators(array(
      'value'           => new sfValidatorPass(array('required' => false)),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_country_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agCountry', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_iso31661_alpha2_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
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
      ->leftJoin($query->getRootAlias().'.agCountryMjAgIso31661Alpha2 agCountryMjAgIso31661Alpha2')
      ->andWhereIn('agCountryMjAgIso31661Alpha2.country_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agIso31661Alpha2';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'value'           => 'Text',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
      'ag_country_list' => 'ManyKey',
    );
  }
}
