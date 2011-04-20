<?php

/**
 * agClientGroupComposition form base class.
 *
 * @method agClientGroupComposition getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagClientGroupCompositionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                               => new sfWidgetFormInputHidden(),
      'client_group_id'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClientGroup'), 'add_empty' => false)),
      'client_group_composition_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClientGroupCompositionType'), 'add_empty' => false)),
      'member_count'                     => new sfWidgetFormInputText(),
      'created_at'                       => new sfWidgetFormDateTime(),
      'updated_at'                       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'client_group_id'                  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agClientGroup'))),
      'client_group_composition_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agClientGroupCompositionType'))),
      'member_count'                     => new sfValidatorInteger(),
      'created_at'                       => new sfValidatorDateTime(),
      'updated_at'                       => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agClientGroupComposition', 'column' => array('client_group_id', 'client_group_composition_type_id')))
    );

    $this->widgetSchema->setNameFormat('ag_client_group_composition[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agClientGroupComposition';
  }

}
