<?php

/**
 * agStaffPerson form extends agPersonForm to include staff information
 *
 * @method agStaff getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     Nils Stolpe, CUNY SPS
 * @author     Charles Wisniewski, CUNY SPS
 */
class PluginagStaffTypeForm extends PluginagStaffResourceTypeForm {

public function setup()
  {
    
    $this->setWidgets(array(
      'id'                                 => new sfWidgetFormInputHidden(),
      'staff_resource_type'                => new sfWidgetFormInputText(),
      'staff_resource_type_abbr'           => new sfWidgetFormInputText(),
      'description'                        => new sfWidgetFormInputText(),
      'app_display'                        => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'                                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'staff_resource_type'                => new sfValidatorString(array('max_length' => 64)),
      'staff_resource_type_abbr'           => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'description'                        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_display'                        => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_stafftype[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

}