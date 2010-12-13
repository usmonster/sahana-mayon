<?php

/**
 * agPhoneContact filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagPhoneContactFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'phone_contact'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'phone_format_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPhoneFormat'), 'add_empty' => true)),
      'created_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_phone_contact_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPhoneContactType')),
      'ag_entity_list'             => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEntity')),
    ));

    $this->setValidators(array(
      'phone_contact'              => new sfValidatorPass(array('required' => false)),
      'phone_format_id'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agPhoneFormat'), 'column' => 'id')),
      'created_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_phone_contact_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPhoneContactType', 'required' => false)),
      'ag_entity_list'             => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEntity', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_phone_contact_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgPhoneContactTypeListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agEntityPhoneContact agEntityPhoneContact')
      ->andWhereIn('agEntityPhoneContact.phone_contact_type_id', $values)
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
      ->leftJoin($query->getRootAlias().'.agEntityPhoneContact agEntityPhoneContact')
      ->andWhereIn('agEntityPhoneContact.entity_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agPhoneContact';
  }

  public function getFields()
  {
    return array(
      'id'                         => 'Number',
      'phone_contact'              => 'Text',
      'phone_format_id'            => 'ForeignKey',
      'created_at'                 => 'Date',
      'updated_at'                 => 'Date',
      'ag_phone_contact_type_list' => 'ManyKey',
      'ag_entity_list'             => 'ManyKey',
    );
  }
}
