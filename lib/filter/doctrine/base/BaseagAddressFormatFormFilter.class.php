<?php

/**
 * agAddressFormat filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagAddressFormatFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'address_standard_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressStandard'), 'add_empty' => true)),
      'address_element_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAddressElement'), 'add_empty' => true)),
      'line_sequence'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'inline_sequence'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'pre_delimiter'       => new sfWidgetFormFilterInput(),
      'post_delimiter'      => new sfWidgetFormFilterInput(),
      'field_type_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFieldType'), 'add_empty' => true)),
      'is_required'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'address_standard_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agAddressStandard'), 'column' => 'id')),
      'address_element_id'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agAddressElement'), 'column' => 'id')),
      'line_sequence'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'inline_sequence'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'pre_delimiter'       => new sfValidatorPass(array('required' => false)),
      'post_delimiter'      => new sfValidatorPass(array('required' => false)),
      'field_type_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFieldType'), 'column' => 'id')),
      'is_required'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_address_format_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAddressFormat';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'address_standard_id' => 'ForeignKey',
      'address_element_id'  => 'ForeignKey',
      'line_sequence'       => 'Number',
      'inline_sequence'     => 'Number',
      'pre_delimiter'       => 'Text',
      'post_delimiter'      => 'Text',
      'field_type_id'       => 'ForeignKey',
      'is_required'         => 'Boolean',
      'created_at'          => 'Date',
      'updated_at'          => 'Date',
    );
  }
}
