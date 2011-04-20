<?php

/**
 * agClientGroupMembership form base class.
 *
 * @method agClientGroupMembership getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagClientGroupMembershipForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'client_group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClientGroup'), 'add_empty' => false)),
      'client_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClient'), 'add_empty' => false)),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'client_group_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agClientGroup'))),
      'client_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agClient'))),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agClientGroupMembership', 'column' => array('client_group_id', 'client_id')))
    );

    $this->widgetSchema->setNameFormat('ag_client_group_membership[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agClientGroupMembership';
  }

}
