<?php

/**
 * agEntityPhoneContact filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagEntityPhoneContactFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'entity_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'), 'add_empty' => true)),
      'phone_contact_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPhoneContact'), 'add_empty' => true)),
      'phone_contact_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPhoneContactType'), 'add_empty' => true)),
      'priority'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'entity_id'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEntity'), 'column' => 'id')),
      'phone_contact_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agPhoneContact'), 'column' => 'id')),
      'phone_contact_type_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agPhoneContactType'), 'column' => 'id')),
      'priority'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_entity_phone_contact_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEntityPhoneContact';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'entity_id'             => 'ForeignKey',
      'phone_contact_id'      => 'ForeignKey',
      'phone_contact_type_id' => 'ForeignKey',
      'priority'              => 'Number',
      'created_at'            => 'Date',
      'updated_at'            => 'Date',
    );
  }
}
