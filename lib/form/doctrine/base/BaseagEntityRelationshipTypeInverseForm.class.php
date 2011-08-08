<?php

/**
 * agEntityRelationshipTypeInverse form base class.
 *
 * @method agEntityRelationshipTypeInverse getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEntityRelationshipTypeInverseForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                  => new sfWidgetFormInputHidden(),
      'entity_relationship_type_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('entity_relation_type'), 'add_empty' => false)),
      'entity_inverse_relationship_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('entity_inverse_relation_type'), 'add_empty' => false)),
      'created_at'                          => new sfWidgetFormDateTime(),
      'updated_at'                          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'entity_relationship_type_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('entity_relation_type'))),
      'entity_inverse_relationship_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('entity_inverse_relation_type'))),
      'created_at'                          => new sfValidatorDateTime(),
      'updated_at'                          => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEntityRelationshipTypeInverse', 'column' => array('entity_relationship_type_id', 'entity_inverse_relationship_type_id')))
    );

    $this->widgetSchema->setNameFormat('ag_entity_relationship_type_inverse[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEntityRelationshipTypeInverse';
  }

}
