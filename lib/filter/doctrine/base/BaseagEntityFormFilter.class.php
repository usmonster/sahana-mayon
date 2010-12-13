<?php

/**
 * agEntity filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagEntityFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'created_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_phone_contact_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agPhoneContact')),
      'ag_email_contact_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEmailContact')),
      'ag_message_batch_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agMessageBatch')),
    ));

    $this->setValidators(array(
      'created_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_phone_contact_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agPhoneContact', 'required' => false)),
      'ag_email_contact_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEmailContact', 'required' => false)),
      'ag_message_batch_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agMessageBatch', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_entity_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgPhoneContactListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agEntityPhoneContact.phone_contact_id', $values)
    ;
  }

  public function addAgEmailContactListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agEntityEmailContact.email_contact_id', $values)
    ;
  }

  public function addAgMessageBatchListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agMessage agMessage')
      ->andWhereIn('agMessage.message_batch_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agEntity';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'created_at'            => 'Date',
      'updated_at'            => 'Date',
      'ag_phone_contact_list' => 'ManyKey',
      'ag_email_contact_list' => 'ManyKey',
      'ag_message_batch_list' => 'ManyKey',
    );
  }
}
