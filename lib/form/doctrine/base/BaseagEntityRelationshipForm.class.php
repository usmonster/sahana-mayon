<?php

/**
 * agEntityRelationship form base class.
 *
 * @method agEntityRelationship getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEntityRelationshipForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                          => new sfWidgetFormInputHidden(),
      'entity_id1'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('entity1'), 'add_empty' => false)),
      'entity_id2'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('entity2'), 'add_empty' => false)),
      'entity_relationship_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntityRelationshipType'), 'add_empty' => false)),
      'by_marriage'                 => new sfWidgetFormInputCheckbox(),
      'ex_relation'                 => new sfWidgetFormInputCheckbox(),
      'created_at'                  => new sfWidgetFormDateTime(),
      'updated_at'                  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'entity_id1'                  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('entity1'))),
      'entity_id2'                  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('entity2'))),
      'entity_relationship_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEntityRelationshipType'))),
      'by_marriage'                 => new sfValidatorBoolean(array('required' => false)),
      'ex_relation'                 => new sfValidatorBoolean(array('required' => false)),
      'created_at'                  => new sfValidatorDateTime(),
      'updated_at'                  => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEntityRelationship', 'column' => array('entity_id1', 'entity_id2', 'entity_relationship_type_id')))
    );

    $this->widgetSchema->setNameFormat('ag_entity_relationship[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEntityRelationship';
  }

}