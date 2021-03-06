<?php

/**
 * agMessageTemplateElement form base class.
 *
 * @method agMessageTemplateElement getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagMessageTemplateElementForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'message_template_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageTemplate'), 'add_empty' => false)),
      'message_element_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageElementType'), 'add_empty' => false)),
      'value'                   => new sfWidgetFormTextarea(),
      'created_at'              => new sfWidgetFormDateTime(),
      'updated_at'              => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'message_template_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageTemplate'))),
      'message_element_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agMessageElementType'))),
      'value'                   => new sfValidatorString(),
      'created_at'              => new sfValidatorDateTime(),
      'updated_at'              => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agMessageTemplateElement', 'column' => array('message_template_id', 'message_element_type_id')))
    );

    $this->widgetSchema->setNameFormat('ag_message_template_element[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agMessageTemplateElement';
  }

}
