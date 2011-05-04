<?php

/**
 * agDefaultMessageTypeElement form base class.
 *
 * @method agDefaultMessageTypeElement getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagDefaultMessageTypeElementForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'message_type_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageType'), 'add_empty' => false)),
      'message_element_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageElementType'), 'add_empty' => false)),
      'created_at'              => new sfWidgetFormDateTime(),
      'updated_at'              => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'message_type_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageType'))),
      'message_element_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageElementType'))),
      'created_at'              => new sfValidatorDateTime(),
      'updated_at'              => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agDefaultMessageTypeElement', 'column' => array('message_type_id', 'message_element_type_id')))
    );

    $this->widgetSchema->setNameFormat('ag_default_message_type_element[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agDefaultMessageTypeElement';
  }

}
