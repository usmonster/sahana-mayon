<?php

/**
 * agScenarioFacilityResource form base class.
 *
 * @method agScenarioFacilityResource getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagScenarioFacilityResourceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                     => new sfWidgetFormInputHidden(),
      'facility_resource_id'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResource'), 'add_empty' => false)),
      'scenario_facility_group_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityGroup'), 'add_empty' => false)),
      'facility_resource_allocation_status_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceAllocationStatus'), 'add_empty' => false)),
      'activation_sequence'                    => new sfWidgetFormInputText(),
      'created_at'                             => new sfWidgetFormDateTime(),
      'updated_at'                             => new sfWidgetFormDateTime(),
      'ag_staff_resource_type_list'            => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResourceType')),
    ));

    $this->setValidators(array(
      'id'                                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'facility_resource_id'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResource'))),
      'scenario_facility_group_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityGroup'))),
      'facility_resource_allocation_status_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceAllocationStatus'))),
      'activation_sequence'                    => new sfValidatorInteger(array('required' => false)),
      'created_at'                             => new sfValidatorDateTime(),
      'updated_at'                             => new sfValidatorDateTime(),
      'ag_staff_resource_type_list'            => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResourceType', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agScenarioFacilityResource', 'column' => array('facility_resource_id', 'scenario_facility_group_id')))
    );

    $this->widgetSchema->setNameFormat('ag_scenario_facility_resource[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agScenarioFacilityResource';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_staff_resource_type_list']))
    {
      $this->setDefault('ag_staff_resource_type_list', $this->object->agStaffResourceType->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagStaffResourceTypeList($con);

    parent::doSave($con);
  }

  public function saveagStaffResourceTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_staff_resource_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agStaffResourceType->getPrimaryKeys();
    $values = $this->getValue('ag_staff_resource_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agStaffResourceType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agStaffResourceType', array_values($link));
    }
  }

}