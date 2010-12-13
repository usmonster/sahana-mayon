<?php

/**
 * agShiftTemplateMessage form base class.
 *
 * @method agShiftTemplateMessage getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagShiftTemplateMessageForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'shift_template_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftTemplate'), 'add_empty' => false)),
      'message_trigger_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageTrigger'), 'add_empty' => false)),
      'batch_template_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agBatchTemplate'), 'add_empty' => false)),
      'created_at'         => new sfWidgetFormDateTime(),
      'updated_at'         => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'shift_template_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftTemplate'))),
      'message_trigger_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageTrigger'))),
      'batch_template_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agBatchTemplate'))),
      'created_at'         => new sfValidatorDateTime(),
      'updated_at'         => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agShiftTemplateMessage', 'column' => array('shift_template_id', 'message_trigger_id', 'batch_template_id')))
    );

    $this->widgetSchema->setNameFormat('ag_shift_template_message[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agShiftTemplateMessage';
  }

}