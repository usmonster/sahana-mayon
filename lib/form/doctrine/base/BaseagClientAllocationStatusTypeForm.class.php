<?php

/**
 * agClientAllocationStatusType form base class.
 *
 * @method agClientAllocationStatusType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagClientAllocationStatusTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                 => new sfWidgetFormInputHidden(),
      'client_allocation_status_type'      => new sfWidgetFormInputText(),
      'client_allocation_status_type_desc' => new sfWidgetFormInputText(),
      'being_serviced'                     => new sfWidgetFormInputCheckbox(),
      'created_at'                         => new sfWidgetFormDateTime(),
      'updated_at'                         => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'client_allocation_status_type'      => new sfValidatorString(array('max_length' => 64)),
      'client_allocation_status_type_desc' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'being_serviced'                     => new sfValidatorBoolean(),
      'created_at'                         => new sfValidatorDateTime(),
      'updated_at'                         => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agClientAllocationStatusType', 'column' => array('client_allocation_status_type')))
    );

    $this->widgetSchema->setNameFormat('ag_client_allocation_status_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agClientAllocationStatusType';
  }

}
