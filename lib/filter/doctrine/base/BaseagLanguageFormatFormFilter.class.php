<?php

/**
 * agLanguageFormat filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagLanguageFormatFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'language_format'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'app_display'                   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'                    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_person_mj_ag_language_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPersonMjAgLanguage')),
      'ag_language_competency_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageCompetency')),
    ));

    $this->setValidators(array(
      'language_format'               => new sfValidatorPass(array('required' => false)),
      'app_display'                   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'                    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_person_mj_ag_language_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPersonMjAgLanguage', 'required' => false)),
      'ag_language_competency_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageCompetency', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_language_format_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgPersonMjAgLanguageListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agPersonLanguageCompetency agPersonLanguageCompetency')
      ->andWhereIn('agPersonLanguageCompetency.person_language_id', $values)
    ;
  }

  public function addAgLanguageCompetencyListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agPersonLanguageCompetency agPersonLanguageCompetency')
      ->andWhereIn('agPersonLanguageCompetency.language_competency_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agLanguageFormat';
  }

  public function getFields()
  {
    return array(
      'id'                            => 'Number',
      'language_format'               => 'Text',
      'app_display'                   => 'Boolean',
      'created_at'                    => 'Date',
      'updated_at'                    => 'Date',
      'ag_person_mj_ag_language_list' => 'ManyKey',
      'ag_language_competency_list'   => 'ManyKey',
    );
  }
}
