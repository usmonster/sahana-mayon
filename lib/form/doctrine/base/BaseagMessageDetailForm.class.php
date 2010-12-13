<?php

/**
 * agMessageDetail form base class.
 *
 * @method agMessageDetail getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagMessageDetailForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'message_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessage'), 'add_empty' => false)),
      'time_stamp'        => new sfWidgetFormDateTime(),
      'message_status_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageStatus'), 'add_empty' => false)),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'message_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessage'))),
      'time_stamp'        => new sfValidatorDateTime(),
      'message_status_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageStatus'))),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agMessageDetail', 'column' => array('message_id', 'time_stamp')))
    );

    $this->widgetSchema->setNameFormat('ag_message_detail[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agMessageDetail';
  }

}