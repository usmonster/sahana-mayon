<?php

/**
 * agAddressValue filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagAddressValueFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'address_element_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressElement'), 'add_empty' => true)),
      'value'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_address_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAddress')),
    ));

    $this->setValidators(array(
      'address_element_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agAddressElement'), 'column' => 'id')),
      'value'              => new sfValidatorPass(array('required' => false)),
      'created_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_address_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAddress', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_address_value_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgAddressListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agAddressMjAgAddressValue.address_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agAddressValue';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Number',
      'address_element_id' => 'ForeignKey',
      'value'              => 'Text',
      'created_at'         => 'Date',
      'updated_at'         => 'Date',
      'ag_address_list'    => 'ManyKey',
    );
  }
}
