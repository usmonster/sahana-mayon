<?php

/**
 * agStaffAllocationStatus filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagStaffAllocationStatusFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'staff_allocation_status' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'             => new sfWidgetFormFilterInput(),
      'allocatable'             => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'commited'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'standby'                 => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'active'                  => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'app_display'             => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'staff_allocation_status' => new sfValidatorPass(array('required' => false)),
      'description'             => new sfValidatorPass(array('required' => false)),
      'allocatable'             => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'commited'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'standby'                 => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'active'                  => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'app_display'             => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_staff_allocation_status_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agStaffAllocationStatus';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'staff_allocation_status' => 'Text',
      'description'             => 'Text',
      'allocatable'             => 'Boolean',
      'commited'                => 'Boolean',
      'standby'                 => 'Boolean',
      'active'                  => 'Boolean',
      'app_display'             => 'Boolean',
      'created_at'              => 'Date',
      'updated_at'              => 'Date',
    );
  }
}
