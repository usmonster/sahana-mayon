<?php

/**
 * agAddressStandard filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagAddressStandardFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'address_standard'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'country_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agCountry'), 'add_empty' => true)),
      'description'             => new sfWidgetFormFilterInput(),
      'app_display'             => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_address_element_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAddressElement')),
    ));

    $this->setValidators(array(
      'address_standard'        => new sfValidatorPass(array('required' => false)),
      'country_id'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agCountry'), 'column' => 'id')),
      'description'             => new sfValidatorPass(array('required' => false)),
      'app_display'             => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_address_element_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAddressElement', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_address_standard_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgAddressElementListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.agAddressFormat agAddressFormat')
      ->andWhereIn('agAddressFormat.address_element_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agAddressStandard';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'address_standard'        => 'Text',
      'country_id'              => 'ForeignKey',
      'description'             => 'Text',
      'app_display'             => 'Boolean',
      'created_at'              => 'Date',
      'updated_at'              => 'Date',
      'ag_address_element_list' => 'ManyKey',
    );
  }
}
