<?php

/**
 * agEntityRelationship filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagEntityRelationshipFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'entity_id1'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('entity1'), 'add_empty' => true)),
      'entity_id2'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('entity2'), 'add_empty' => true)),
      'entity_relationship_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntityRelationshipType'), 'add_empty' => true)),
      'by_marriage'                 => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'ex_relation'                 => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'entity_id1'                  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('entity1'), 'column' => 'id')),
      'entity_id2'                  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('entity2'), 'column' => 'id')),
      'entity_relationship_type_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agEntityRelationshipType'), 'column' => 'id')),
      'by_marriage'                 => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'ex_relation'                 => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_entity_relationship_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEntityRelationship';
  }

  public function getFields()
  {
    return array(
      'id'                          => 'Number',
      'entity_id1'                  => 'ForeignKey',
      'entity_id2'                  => 'ForeignKey',
      'entity_relationship_type_id' => 'ForeignKey',
      'by_marriage'                 => 'Boolean',
      'ex_relation'                 => 'Boolean',
      'created_at'                  => 'Date',
      'updated_at'                  => 'Date',
    );
  }
}
