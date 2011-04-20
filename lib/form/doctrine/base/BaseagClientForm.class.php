<?php

/**
 * agClient form base class.
 *
 * @method agClient getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagClientForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'person_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => false)),
      'event_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => false)),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'))),
      'event_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'))),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agClient', 'column' => array('person_id', 'event_id')))
    );

    $this->widgetSchema->setNameFormat('ag_client[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agClient';
  }

}
