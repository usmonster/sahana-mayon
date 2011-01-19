<?php

/**
 * 
 *
 * @method agStaffResourceType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
class PluginagEmbeddedAgStaffResourceTypeForm extends PluginagStaffResourceTypeForm
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                 => new sfWidgetFormInputHidden(),
      'staff_resource_type'                => new sfWidgetFormInputText(array(), array('class' => 'inputGray')),
      'staff_resource_type'                => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agStaffResourceType'), array('class' => 'inputGray')),
//      'staff_resource_type_abbr'           => new sfWidgetFormInputText(),
//      'description'                        => new sfWidgetFormInputText(),
//      'app_display'                        => new sfWidgetFormInputCheckbox(),
//      'created_at'                         => new sfWidgetFormDateTime(),
//      'updated_at'                         => new sfWidgetFormDateTime(),
//      'ag_staff_list'                      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agStaff')),
//      'ag_skill_list'                      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agSkill')),
//      'ag_scenario_facility_resource_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agScenarioFacilityResource')),
    ));

    $this->setValidators(array(
      'id'                                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'staff_resource_type'                => new sfValidatorString(array('max_length' => 64)),
//      'staff_resource_type_abbr'           => new sfValidatorString(array('max_length' => 10, 'required' => false)),
//      'description'                        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
//      'app_display'                        => new sfValidatorBoolean(array('required' => false)),
//      'created_at'                         => new sfValidatorDateTime(),
//      'updated_at'                         => new sfValidatorDateTime(),
//      'ag_staff_list'                      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaff', 'required' => false)),
//      'ag_skill_list'                      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agSkill', 'required' => false)),
//      'ag_scenario_facility_resource_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agScenarioFacilityResource', 'required' => false)),
    ));
  }
}