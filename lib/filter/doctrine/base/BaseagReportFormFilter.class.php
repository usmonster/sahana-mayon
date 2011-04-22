<?php

/**
 * agReport filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagReportFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'report_name'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'report_description'    => new sfWidgetFormFilterInput(),
      'created_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_lucene_search_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agSearch')),
    ));

    $this->setValidators(array(
      'report_name'           => new sfValidatorPass(array('required' => false)),
      'report_description'    => new sfValidatorPass(array('required' => false)),
      'created_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_lucene_search_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agSearch', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_report_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgLuceneSearchListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agReportGenerator.search_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agReport';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'report_name'           => 'Text',
      'report_description'    => 'Text',
      'created_at'            => 'Date',
      'updated_at'            => 'Date',
      'ag_lucene_search_list' => 'ManyKey',
    );
  }
}
