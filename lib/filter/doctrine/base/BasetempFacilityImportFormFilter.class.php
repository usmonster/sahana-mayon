<?php

/**
 * tempFacilityImport filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasetempFacilityImportFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'facility_name'                      => new sfWidgetFormFilterInput(),
      'facility_code'                      => new sfWidgetFormFilterInput(),
      'facility_resource_type_abbr'        => new sfWidgetFormFilterInput(),
      'facility_resource_status'           => new sfWidgetFormFilterInput(),
      'facility_capacity'                  => new sfWidgetFormFilterInput(),
      'facility_activation_sequece'        => new sfWidgetFormFilterInput(),
      'facility_allocation_status'         => new sfWidgetFormFilterInput(),
      'facility_group'                     => new sfWidgetFormFilterInput(),
      'facility_group_allocation_status'   => new sfWidgetFormFilterInput(),
      'faciltiy_group_activation_sequence' => new sfWidgetFormFilterInput(),
      'work_email'                         => new sfWidgetFormFilterInput(),
      'work_phone'                         => new sfWidgetFormFilterInput(),
      'street_1'                           => new sfWidgetFormFilterInput(),
      'street_2'                           => new sfWidgetFormFilterInput(),
      'city'                               => new sfWidgetFormFilterInput(),
      'state'                              => new sfWidgetFormFilterInput(),
      'zip_code'                           => new sfWidgetFormFilterInput(),
      'borough'                            => new sfWidgetFormFilterInput(),
      'country'                            => new sfWidgetFormFilterInput(),
      'longitude'                          => new sfWidgetFormFilterInput(),
      'latitude'                           => new sfWidgetFormFilterInput(),
      'generalist_min'                     => new sfWidgetFormFilterInput(),
      'generalist_max'                     => new sfWidgetFormFilterInput(),
      'specialist_min'                     => new sfWidgetFormFilterInput(),
      'specialist_max'                     => new sfWidgetFormFilterInput(),
      'operator_min'                       => new sfWidgetFormFilterInput(),
      'operator_max'                       => new sfWidgetFormFilterInput(),
      'medical_nurse_min'                  => new sfWidgetFormFilterInput(),
      'medical_nurse_max'                  => new sfWidgetFormFilterInput(),
      'medical_other_min'                  => new sfWidgetFormFilterInput(),
      'medical_other_max'                  => new sfWidgetFormFilterInput(),
      'created_at'                         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'facility_name'                      => new sfValidatorPass(array('required' => false)),
      'facility_code'                      => new sfValidatorPass(array('required' => false)),
      'facility_resource_type_abbr'        => new sfValidatorPass(array('required' => false)),
      'facility_resource_status'           => new sfValidatorPass(array('required' => false)),
      'facility_capacity'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'facility_activation_sequece'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'facility_allocation_status'         => new sfValidatorPass(array('required' => false)),
      'facility_group'                     => new sfValidatorPass(array('required' => false)),
      'facility_group_allocation_status'   => new sfValidatorPass(array('required' => false)),
      'faciltiy_group_activation_sequence' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'work_email'                         => new sfValidatorPass(array('required' => false)),
      'work_phone'                         => new sfValidatorPass(array('required' => false)),
      'street_1'                           => new sfValidatorPass(array('required' => false)),
      'street_2'                           => new sfValidatorPass(array('required' => false)),
      'city'                               => new sfValidatorPass(array('required' => false)),
      'state'                              => new sfValidatorPass(array('required' => false)),
      'zip_code'                           => new sfValidatorPass(array('required' => false)),
      'borough'                            => new sfValidatorPass(array('required' => false)),
      'country'                            => new sfValidatorPass(array('required' => false)),
      'longitude'                          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'latitude'                           => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'generalist_min'                     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'generalist_max'                     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specialist_min'                     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specialist_max'                     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'operator_min'                       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'operator_max'                       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'medical_nurse_min'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'medical_nurse_max'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'medical_other_min'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'medical_other_max'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'                         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('temp_facility_import_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'tempFacilityImport';
  }

  public function getFields()
  {
    return array(
      'id'                                 => 'Number',
      'facility_name'                      => 'Text',
      'facility_code'                      => 'Text',
      'facility_resource_type_abbr'        => 'Text',
      'facility_resource_status'           => 'Text',
      'facility_capacity'                  => 'Number',
      'facility_activation_sequece'        => 'Number',
      'facility_allocation_status'         => 'Text',
      'facility_group'                     => 'Text',
      'facility_group_allocation_status'   => 'Text',
      'faciltiy_group_activation_sequence' => 'Number',
      'work_email'                         => 'Text',
      'work_phone'                         => 'Text',
      'street_1'                           => 'Text',
      'street_2'                           => 'Text',
      'city'                               => 'Text',
      'state'                              => 'Text',
      'zip_code'                           => 'Text',
      'borough'                            => 'Text',
      'country'                            => 'Text',
      'longitude'                          => 'Number',
      'latitude'                           => 'Number',
      'generalist_min'                     => 'Number',
      'generalist_max'                     => 'Number',
      'specialist_min'                     => 'Number',
      'specialist_max'                     => 'Number',
      'operator_min'                       => 'Number',
      'operator_max'                       => 'Number',
      'medical_nurse_min'                  => 'Number',
      'medical_nurse_max'                  => 'Number',
      'medical_other_min'                  => 'Number',
      'medical_other_max'                  => 'Number',
      'created_at'                         => 'Date',
      'updated_at'                         => 'Date',
    );
  }
}
