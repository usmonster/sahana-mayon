<?php

/**
 * agFacilityResourceAllocationStatus filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagFacilityResourceAllocationStatusFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'facility_resource_allocation_status' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'                         => new sfWidgetFormFilterInput(),
      'committed'                           => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'staffed'                             => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'servicing_clients'                   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'accepting_clients'                   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'allocatable'                         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'standby'                             => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'app_display'                         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'                          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'facility_resource_allocation_status' => new sfValidatorPass(array('required' => false)),
      'description'                         => new sfValidatorPass(array('required' => false)),
      'committed'                           => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'staffed'                             => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'servicing_clients'                   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'accepting_clients'                   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'allocatable'                         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'standby'                             => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'app_display'                         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'                          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_facility_resource_allocation_status_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agFacilityResourceAllocationStatus';
  }

  public function getFields()
  {
    return array(
      'id'                                  => 'Number',
      'facility_resource_allocation_status' => 'Text',
      'description'                         => 'Text',
      'committed'                           => 'Boolean',
      'staffed'                             => 'Boolean',
      'servicing_clients'                   => 'Boolean',
      'accepting_clients'                   => 'Boolean',
      'allocatable'                         => 'Boolean',
      'standby'                             => 'Boolean',
      'app_display'                         => 'Boolean',
      'created_at'                          => 'Date',
      'updated_at'                          => 'Date',
    );
  }
}
