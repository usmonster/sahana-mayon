<?php

/**
 * agEventStatus form base class.
 *
 * @method agEventStatus getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventStatusForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'event_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => false)),
      'time_stamp'           => new sfWidgetFormDateTime(),
      'event_status_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventStatusType'), 'add_empty' => false)),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_at'           => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'))),
      'time_stamp'           => new sfValidatorDateTime(),
      'event_status_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventStatusType'))),
      'created_at'           => new sfValidatorDateTime(),
      'updated_at'           => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventStatus', 'column' => array('event_id', 'time_stamp')))
    );

    $this->widgetSchema->setNameFormat('ag_event_status[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventStatus';
  }

}