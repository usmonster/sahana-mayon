<?php

/**
 * agAddressContactType filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagAddressContactTypeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'address_contact_type' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'app_display'          => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'ag_site_address_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agAddress')),
    ));

    $this->setValidators(array(
      'address_contact_type' => new sfValidatorPass(array('required' => false)),
      'app_display'          => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'ag_site_address_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agAddress', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_address_contact_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgSiteAddressListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('agEntityAddressContact.address_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agAddressContactType';
  }

  public function getFields()
  {
    return array(
      'id'                   => 'Number',
      'address_contact_type' => 'Text',
      'app_display'          => 'Boolean',
      'created_at'           => 'Date',
      'updated_at'           => 'Date',
      'ag_site_address_list' => 'ManyKey',
    );
  }
}
