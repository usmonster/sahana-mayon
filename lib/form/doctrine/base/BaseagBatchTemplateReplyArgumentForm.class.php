<?php

/**
 * agBatchTemplateReplyArgument form base class.
 *
 * @method agBatchTemplateReplyArgument getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagBatchTemplateReplyArgumentForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                        => new sfWidgetFormInputHidden(),
      'batch_template_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agBatchTemplate'), 'add_empty' => false)),
      'message_reply_argument_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageReplyArgument'), 'add_empty' => false)),
      'argument_sequence'         => new sfWidgetFormInputText(),
      'created_at'                => new sfWidgetFormDateTime(),
      'updated_at'                => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'batch_template_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agBatchTemplate'))),
      'message_reply_argument_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageReplyArgument'))),
      'argument_sequence'         => new sfValidatorInteger(),
      'created_at'                => new sfValidatorDateTime(),
      'updated_at'                => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agBatchTemplateReplyArgument', 'column' => array('batch_template_id', 'message_reply_argument_id'))),
        new sfValidatorDoctrineUnique(array('model' => 'agBatchTemplateReplyArgument', 'column' => array('batch_template_id', 'argument_sequence'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_batch_template_reply_argument[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agBatchTemplateReplyArgument';
  }

}