<?php

/**
 * agMessageBatchTemplate form base class.
 *
 * @method agMessageBatchTemplate getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagMessageBatchTemplateForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'batch_template_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agBatchTemplate'), 'add_empty' => false)),
      'message_template_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageTemplate'), 'add_empty' => false)),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'batch_template_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agBatchTemplate'))),
      'message_template_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageTemplate'))),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agMessageBatchTemplate', 'column' => array('batch_template_id', 'message_template_id')))
    );

    $this->widgetSchema->setNameFormat('ag_message_batch_template[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agMessageBatchTemplate';
  }

}
