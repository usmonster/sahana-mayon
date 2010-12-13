<?php

/**
 * agEmailContact filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagEmailContactFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'email_contact'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_email_contact_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEmailContactType')),
      'ag_entity_list'             => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEntity')),
    ));

    $this->setValidators(array(
      'email_contact'              => new sfValidatorPass(array('required' => false)),
      'created_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_email_contact_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEmailContactType', 'required' => false)),
      'ag_entity_list'             => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEntity', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_email_contact_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgEmailContactTypeListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agEntityEmailContact agEntityEmailContact')
      ->andWhereIn('agEntityEmailContact.email_contact_type_id', $values)
    ;
  }

  public function addAgEntityListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agEntityEmailContact agEntityEmailContact')
      ->andWhereIn('agEntityEmailContact.entity_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agEmailContact';
  }

  public function getFields()
  {
    return array(
      'id'                         => 'Number',
      'email_contact'              => 'Text',
      'created_at'                 => 'Date',
      'updated_at'                 => 'Date',
      'ag_email_contact_type_list' => 'ManyKey',
      'ag_entity_list'             => 'ManyKey',
    );
  }
}
