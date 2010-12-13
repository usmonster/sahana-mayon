<?php

/**
 * agPet filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagPetFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'pet_name'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'event_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => true)),
      'sex_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSex'), 'add_empty' => true)),
      'species_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSpecies'), 'add_empty' => true)),
      'age'                  => new sfWidgetFormFilterInput(),
      'age_date_recorded'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'physical_description' => new sfWidgetFormFilterInput(),
      'created_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'pet_name'             => new sfValidatorPass(array('required' => false)),
      'event_id'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEvent'), 'column' => 'id')),
      'sex_id'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agSex'), 'column' => 'id')),
      'species_id'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agSpecies'), 'column' => 'id')),
      'age'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'age_date_recorded'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'physical_description' => new sfValidatorPass(array('required' => false)),
      'created_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_pet_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPet';
  }

  public function getFields()
  {
    return array(
      'id'                   => 'Number',
      'pet_name'             => 'Text',
      'event_id'             => 'ForeignKey',
      'sex_id'               => 'ForeignKey',
      'species_id'           => 'ForeignKey',
      'age'                  => 'Number',
      'age_date_recorded'    => 'Date',
      'physical_description' => 'Text',
      'created_at'           => 'Date',
      'updated_at'           => 'Date',
    );
  }
}
