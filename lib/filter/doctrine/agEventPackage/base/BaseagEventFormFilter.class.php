<?php

/**
 * agEvent filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagEventFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'event_name'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'zero_hour'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'created_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_affected_area_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAffectedArea')),
      'ag_scenario_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agScenario')),
    ));

    $this->setValidators(array(
      'event_name'            => new sfValidatorPass(array('required' => false)),
      'zero_hour'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'created_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_affected_area_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAffectedArea', 'required' => false)),
      'ag_scenario_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agScenario', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_event_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgAffectedAreaListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agEventAffectedArea agEventAffectedArea')
      ->andWhereIn('agEventAffectedArea.affected_area_id', $values)
    ;
  }

  public function addAgScenarioListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agEventScenario agEventScenario')
      ->andWhereIn('agEventScenario.scenario_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agEvent';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'event_name'            => 'Text',
      'zero_hour'             => 'Date',
      'created_at'            => 'Date',
      'updated_at'            => 'Date',
      'ag_affected_area_list' => 'ManyKey',
      'ag_scenario_list'      => 'ManyKey',
    );
  }
}
