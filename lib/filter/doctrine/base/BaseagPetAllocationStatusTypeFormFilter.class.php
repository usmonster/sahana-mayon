<?php

/**
 * agPetAllocationStatusType filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagPetAllocationStatusTypeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'pet_allocation_status_type'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'pet_allocation_status_type_desc' => new sfWidgetFormFilterInput(),
      'being_serviced'                  => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'                      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'pet_allocation_status_type'      => new sfValidatorPass(array('required' => false)),
      'pet_allocation_status_type_desc' => new sfValidatorPass(array('required' => false)),
      'being_serviced'                  => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'                      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_pet_allocation_status_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPetAllocationStatusType';
  }

  public function getFields()
  {
    return array(
      'id'                              => 'Number',
      'pet_allocation_status_type'      => 'Text',
      'pet_allocation_status_type_desc' => 'Text',
      'being_serviced'                  => 'Boolean',
      'created_at'                      => 'Date',
      'updated_at'                      => 'Date',
    );
  }
}
