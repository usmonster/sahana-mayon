<?php

/**
 * agClientGroupComposition filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagClientGroupCompositionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'client_group_id'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClientGroup'), 'add_empty' => true)),
      'client_group_composition_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClientGroupCompositionType'), 'add_empty' => true)),
      'member_count'                     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'client_group_id'                  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agClientGroup'), 'column' => 'id')),
      'client_group_composition_type_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agClientGroupCompositionType'), 'column' => 'id')),
      'member_count'                     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'                       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_client_group_composition_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agClientGroupComposition';
  }

  public function getFields()
  {
    return array(
      'id'                               => 'Number',
      'client_group_id'                  => 'ForeignKey',
      'client_group_composition_type_id' => 'ForeignKey',
      'member_count'                     => 'Number',
      'created_at'                       => 'Date',
      'updated_at'                       => 'Date',
    );
  }
}
