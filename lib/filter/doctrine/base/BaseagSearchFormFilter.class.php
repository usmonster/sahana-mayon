<?php

/**
 * agSearch filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagSearchFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'search_type_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSearchType'), 'add_empty' => true)),
      'search_hash'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'search_name'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'search_condition' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_report_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agReport')),
    ));

    $this->setValidators(array(
      'search_type_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agSearchType'), 'column' => 'id')),
      'search_hash'      => new sfValidatorPass(array('required' => false)),
      'search_name'      => new sfValidatorPass(array('required' => false)),
      'search_condition' => new sfValidatorPass(array('required' => false)),
      'created_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_report_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agReport', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_search_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgReportListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agReportGenerator agReportGenerator')
      ->andWhereIn('agReportGenerator.report_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agSearch';
  }

  public function getFields()
  {
    return array(
      'id'               => 'Number',
      'search_type_id'   => 'ForeignKey',
      'search_hash'      => 'Text',
      'search_name'      => 'Text',
      'search_condition' => 'Text',
      'created_at'       => 'Date',
      'updated_at'       => 'Date',
      'ag_report_list'   => 'ManyKey',
    );
  }
}
