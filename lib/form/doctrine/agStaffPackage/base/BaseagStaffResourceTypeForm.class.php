<?php

/**
 * agStaffResourceType form base class.
 *
 * @method agStaffResourceType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagStaffResourceTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                 => new sfWidgetFormInputHidden(),
      'staff_resource_type'                => new sfWidgetFormInputText(),
      'staff_resource_type_abbr'           => new sfWidgetFormInputText(),
      'description'                        => new sfWidgetFormInputText(),
      'app_display'                        => new sfWidgetFormInputCheckbox(),
      'created_at'                         => new sfWidgetFormDateTime(),
      'updated_at'                         => new sfWidgetFormDateTime(),
      'ag_staff_list'                      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agStaff')),
      'ag_skill_list'                      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agSkill')),
      'ag_scenario_facility_resource_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agScenarioFacilityResource')),
    ));

    $this->setValidators(array(
      'id'                                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'staff_resource_type'                => new sfValidatorString(array('max_length' => 64)),
      'staff_resource_type_abbr'           => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'description'                        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_display'                        => new sfValidatorBoolean(array('required' => false)),
      'created_at'                         => new sfValidatorDateTime(),
      'updated_at'                         => new sfValidatorDateTime(),
      'ag_staff_list'                      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaff', 'required' => false)),
      'ag_skill_list'                      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agSkill', 'required' => false)),
      'ag_scenario_facility_resource_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agScenarioFacilityResource', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agStaffResourceType', 'column' => array('staff_resource_type'))),
        new sfValidatorDoctrineUnique(array('model' => 'agStaffResourceType', 'column' => array('staff_resource_type_abbr'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_staff_resource_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agStaffResourceType';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_staff_list']))
    {
      $this->setDefault('ag_staff_list', $this->object->agStaff->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_skill_list']))
    {
      $this->setDefault('ag_skill_list', $this->object->agSkill->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_scenario_facility_resource_list']))
    {
      $this->setDefault('ag_scenario_facility_resource_list', $this->object->agScenarioFacilityResource->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagStaffList($con);
    $this->saveagSkillList($con);
    $this->saveagScenarioFacilityResourceList($con);

    parent::doSave($con);
  }

  public function saveagStaffList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_staff_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agStaff->getPrimaryKeys();
    $values = $this->getValue('ag_staff_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agStaff', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agStaff', array_values($link));
    }
  }

  public function saveagSkillList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_skill_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agSkill->getPrimaryKeys();
    $values = $this->getValue('ag_skill_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agSkill', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agSkill', array_values($link));
    }
  }

  public function saveagScenarioFacilityResourceList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_scenario_facility_resource_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agScenarioFacilityResource->getPrimaryKeys();
    $values = $this->getValue('ag_scenario_facility_resource_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agScenarioFacilityResource', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agScenarioFacilityResource', array_values($link));
    }
  }

}