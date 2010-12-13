<?php

/**
 * agClientNote form base class.
 *
 * @method agClientNote getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagClientNoteForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'client_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agClient'), 'add_empty' => false)),
      'time_stamp'     => new sfWidgetFormDateTime(),
      'event_staff_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventStaff'), 'add_empty' => false)),
      'client_note'    => new sfWidgetFormInputText(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'client_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agClient'))),
      'time_stamp'     => new sfValidatorDateTime(),
      'event_staff_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventStaff'))),
      'client_note'    => new sfValidatorPass(),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agClientNote', 'column' => array('client_id', 'time_stamp')))
    );

    $this->widgetSchema->setNameFormat('ag_client_note[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agClientNote';
  }

}