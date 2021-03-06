<?php

/**
 * agEntityAddressContact filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagEntityAddressContactFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'entity_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'), 'add_empty' => true)),
      'address_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddress'), 'add_empty' => true)),
      'address_contact_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressContactType'), 'add_empty' => true)),
      'priority'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'entity_id'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEntity'), 'column' => 'id')),
      'address_id'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agAddress'), 'column' => 'id')),
      'address_contact_type_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agAddressContactType'), 'column' => 'id')),
      'priority'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_entity_address_contact_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEntityAddressContact';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'entity_id'               => 'ForeignKey',
      'address_id'              => 'ForeignKey',
      'address_contact_type_id' => 'ForeignKey',
      'priority'                => 'Number',
      'created_at'              => 'Date',
      'updated_at'              => 'Date',
    );
  }
}
