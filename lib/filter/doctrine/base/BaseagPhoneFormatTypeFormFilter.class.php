<?php

/**
 * agPhoneFormatType filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagPhoneFormatTypeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'phone_format_type'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'app_display'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'validation'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'match_pattern'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'replacement_pattern' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'phone_format_type'   => new sfValidatorPass(array('required' => false)),
      'app_display'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'validation'          => new sfValidatorPass(array('required' => false)),
      'match_pattern'       => new sfValidatorPass(array('required' => false)),
      'replacement_pattern' => new sfValidatorPass(array('required' => false)),
      'created_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_phone_format_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPhoneFormatType';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'phone_format_type'   => 'Text',
      'app_display'         => 'Boolean',
      'validation'          => 'Text',
      'match_pattern'       => 'Text',
      'replacement_pattern' => 'Text',
      'created_at'          => 'Date',
      'updated_at'          => 'Date',
    );
  }
}
