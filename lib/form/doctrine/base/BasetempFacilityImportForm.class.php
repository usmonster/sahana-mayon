<?php

/**
 * tempFacilityImport form base class.
 *
 * @method tempFacilityImport getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasetempFacilityImportForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                 => new sfWidgetFormInputHidden(),
      'facility_name'                      => new sfWidgetFormInputText(),
      'facility_code'                      => new sfWidgetFormInputText(),
      'facility_resource_type_abbr'        => new sfWidgetFormInputText(),
      'facility_resource_status'           => new sfWidgetFormInputText(),
      'facility_capacity'                  => new sfWidgetFormInputText(),
      'facility_activation_sequece'        => new sfWidgetFormInputText(),
      'facility_allocation_status'         => new sfWidgetFormInputText(),
      'facility_group'                     => new sfWidgetFormInputText(),
      'facility_group_allocation_status'   => new sfWidgetFormInputText(),
      'faciltiy_group_activation_sequence' => new sfWidgetFormInputText(),
      'work_email'                         => new sfWidgetFormInputText(),
      'work_phone'                         => new sfWidgetFormInputText(),
      'street_1'                           => new sfWidgetFormInputText(),
      'street_2'                           => new sfWidgetFormInputText(),
      'city'                               => new sfWidgetFormInputText(),
      'state'                              => new sfWidgetFormInputText(),
      'zip_code'                           => new sfWidgetFormInputText(),
      'borough'                            => new sfWidgetFormInputText(),
      'country'                            => new sfWidgetFormInputText(),
      'longitude'                          => new sfWidgetFormInputText(),
      'latitude'                           => new sfWidgetFormInputText(),
      'generalist_min'                     => new sfWidgetFormInputText(),
      'generalist_max'                     => new sfWidgetFormInputText(),
      'specialist_min'                     => new sfWidgetFormInputText(),
      'specialist_max'                     => new sfWidgetFormInputText(),
      'operator_min'                       => new sfWidgetFormInputText(),
      'operator_max'                       => new sfWidgetFormInputText(),
      'medical_nurse_min'                  => new sfWidgetFormInputText(),
      'medical_nurse_max'                  => new sfWidgetFormInputText(),
      'medical_other_min'                  => new sfWidgetFormInputText(),
      'medical_other_max'                  => new sfWidgetFormInputText(),
      'created_at'                         => new sfWidgetFormDateTime(),
      'updated_at'                         => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'facility_name'                      => new sfValidatorString(array('max_length' => 64, 'required' => false)),
      'facility_code'                      => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'facility_resource_type_abbr'        => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'facility_resource_status'           => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'facility_capacity'                  => new sfValidatorInteger(array('required' => false)),
      'facility_activation_sequece'        => new sfValidatorInteger(array('required' => false)),
      'facility_allocation_status'         => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'facility_group'                     => new sfValidatorString(array('max_length' => 64, 'required' => false)),
      'facility_group_allocation_status'   => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'faciltiy_group_activation_sequence' => new sfValidatorInteger(array('required' => false)),
      'work_email'                         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'work_phone'                         => new sfValidatorString(array('max_length' => 16, 'required' => false)),
      'street_1'                           => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'street_2'                           => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'city'                               => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'state'                              => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'zip_code'                           => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'borough'                            => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'country'                            => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'longitude'                          => new sfValidatorNumber(array('required' => false)),
      'latitude'                           => new sfValidatorNumber(array('required' => false)),
      'generalist_min'                     => new sfValidatorInteger(array('required' => false)),
      'generalist_max'                     => new sfValidatorInteger(array('required' => false)),
      'specialist_min'                     => new sfValidatorInteger(array('required' => false)),
      'specialist_max'                     => new sfValidatorInteger(array('required' => false)),
      'operator_min'                       => new sfValidatorInteger(array('required' => false)),
      'operator_max'                       => new sfValidatorInteger(array('required' => false)),
      'medical_nurse_min'                  => new sfValidatorInteger(array('required' => false)),
      'medical_nurse_max'                  => new sfValidatorInteger(array('required' => false)),
      'medical_other_min'                  => new sfValidatorInteger(array('required' => false)),
      'medical_other_max'                  => new sfValidatorInteger(array('required' => false)),
      'created_at'                         => new sfValidatorDateTime(),
      'updated_at'                         => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('temp_facility_import[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'tempFacilityImport';
  }

}