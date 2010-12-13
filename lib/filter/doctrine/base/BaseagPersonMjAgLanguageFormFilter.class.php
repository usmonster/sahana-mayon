<?php

/**
 * agPersonMjAgLanguage filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagPersonMjAgLanguageFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'person_id'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => true)),
      'language_id'                 => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agLanguage'), 'add_empty' => true)),
      'priority'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_language_format_list'     => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageFormat')),
      'ag_language_competency_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageCompetency')),
    ));

    $this->setValidators(array(
      'person_id'                   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agPerson'), 'column' => 'id')),
      'language_id'                 => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agLanguage'), 'column' => 'id')),
      'priority'                    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_language_format_list'     => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageFormat', 'required' => false)),
      'ag_language_competency_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agLanguageCompetency', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_person_mj_ag_language_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgLanguageFormatListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agPersonLanguageCompetency.language_format_id', $values)
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
    return 'agPersonMjAgLanguage';
  }

  public function getFields()
  {
    return array(
      'id'                          => 'Number',
      'person_id'                   => 'ForeignKey',
      'language_id'                 => 'ForeignKey',
      'priority'                    => 'Number',
      'created_at'                  => 'Date',
      'updated_at'                  => 'Date',
      'ag_language_format_list'     => 'ManyKey',
      'ag_language_competency_list' => 'ManyKey',
    );
  }
}
