<?php

/**
 * agDefaultMessageBatchTrigger form base class.
 *
 * @method agDefaultMessageBatchTrigger getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagDefaultMessageBatchTriggerForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                        => new sfWidgetFormInputHidden(),
      'message_trigger_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageTrigger'), 'add_empty' => false)),
      'message_batch_template_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageBatchTemplate'), 'add_empty' => false)),
      'created_at'                => new sfWidgetFormDateTime(),
      'updated_at'                => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'message_trigger_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageTrigger'))),
      'message_batch_template_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageBatchTemplate'))),
      'created_at'                => new sfValidatorDateTime(),
      'updated_at'                => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agDefaultMessageBatchTrigger', 'column' => array('message_trigger_id', 'message_batch_template_id')))
    );

    $this->widgetSchema->setNameFormat('ag_default_message_batch_trigger[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agDefaultMessageBatchTrigger';
  }

}