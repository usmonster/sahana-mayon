<?php

/**
 * agClientGroupCompositionType form base class.
 *
 * @method agClientGroupCompositionType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagClientGroupCompositionTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                 => new sfWidgetFormInputHidden(),
      'client_group_composition_type'      => new sfWidgetFormInputText(),
      'client_group_composition_type_desc' => new sfWidgetFormInputText(),
      'created_at'                         => new sfWidgetFormDateTime(),
      'updated_at'                         => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'client_group_composition_type'      => new sfValidatorString(array('max_length' => 32)),
      'client_group_composition_type_desc' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'                         => new sfValidatorDateTime(),
      'updated_at'                         => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agClientGroupCompositionType', 'column' => array('client_group_composition_type')))
    );

    $this->widgetSchema->setNameFormat('ag_client_group_composition_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agClientGroupCompositionType';
  }

}
