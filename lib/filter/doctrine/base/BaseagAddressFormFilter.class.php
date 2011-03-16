<?php

/**
 * agAddress filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagAddressFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'address_standard_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressStandard'), 'add_empty' => true)),
      'address_hash'                 => new sfWidgetFormFilterInput(),
      'created_at'                   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_address_value_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAddressValue')),
      'ag_address_contact_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAddressContactType')),
    ));

    $this->setValidators(array(
      'address_standard_id'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agAddressStandard'), 'column' => 'id')),
      'address_hash'                 => new sfValidatorPass(array('required' => false)),
      'created_at'                   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_address_value_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAddressValue', 'required' => false)),
      'ag_address_contact_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAddressContactType', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_address_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgAddressValueListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agAddressMjAgAddressValue agAddressMjAgAddressValue')
      ->andWhereIn('agAddressMjAgAddressValue.address_value_id', $values)
    ;
  }

  public function addAgAddressContactTypeListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agEntityAddressContact agEntityAddressContact')
      ->andWhereIn('agEntityAddressContact.address_contact_type_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agAddress';
  }

  public function getFields()
  {
    return array(
      'id'                           => 'Number',
      'address_standard_id'          => 'ForeignKey',
      'address_hash'                 => 'Text',
      'created_at'                   => 'Date',
      'updated_at'                   => 'Date',
      'ag_address_value_list'        => 'ManyKey',
      'ag_address_contact_type_list' => 'ManyKey',
    );
  }
}
