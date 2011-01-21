<?php

/**
 * agAffectedArea filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagAffectedAreaFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'affected_area'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'geo_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'), 'add_empty' => true)),
      'required_evacuation' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_event_list'       => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEvent')),
    ));

    $this->setValidators(array(
      'affected_area'       => new sfValidatorPass(array('required' => false)),
      'geo_id'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agGeo'), 'column' => 'id')),
      'required_evacuation' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_event_list'       => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEvent', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_affected_area_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgEventListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agEventAffectedArea.event_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agAffectedArea';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'affected_area'       => 'Text',
      'geo_id'              => 'ForeignKey',
      'required_evacuation' => 'Boolean',
      'created_at'          => 'Date',
      'updated_at'          => 'Date',
      'ag_event_list'       => 'ManyKey',
    );
  }
}
