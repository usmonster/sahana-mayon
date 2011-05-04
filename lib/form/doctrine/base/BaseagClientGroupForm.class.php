<?php

/**
 * agClientGroup form base class.
 *
 * @method agClientGroup getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagClientGroupForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'entity_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'), 'add_empty' => false)),
      'client_group_type_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClientGroupType'), 'add_empty' => false)),
      'client_group_leader_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClient'), 'add_empty' => false)),
      'created_at'             => new sfWidgetFormDateTime(),
      'updated_at'             => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'entity_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'))),
      'client_group_type_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agClientGroupType'))),
      'client_group_leader_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agClient'))),
      'created_at'             => new sfValidatorDateTime(),
      'updated_at'             => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agClientGroup', 'column' => array('entity_id')))
    );

    $this->widgetSchema->setNameFormat('ag_client_group[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agClientGroup';
  }

}
