<?php

/**
 * agFacilityResource form base class.
 *
 * @method agFacilityResource getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagFacilityResourceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                              => new sfWidgetFormInputHidden(),
      'facility_id'                     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacility'), 'add_empty' => false)),
      'facility_resource_type_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceType'), 'add_empty' => false)),
      'facility_resource_status_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceStatus'), 'add_empty' => false)),
      'capacity'                        => new sfWidgetFormInputText(),
      'created_at'                      => new sfWidgetFormDateTime(),
      'updated_at'                      => new sfWidgetFormDateTime(),
      'ag_event_facility_group_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEventFacilityGroup')),
      'ag_scenario_facility_group_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agScenarioFacilityGroup')),
    ));

    $this->setValidators(array(
      'id'                              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'facility_id'                     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacility'))),
      'facility_resource_type_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceType'))),
      'facility_resource_status_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceStatus'))),
      'capacity'                        => new sfValidatorInteger(),
      'created_at'                      => new sfValidatorDateTime(),
      'updated_at'                      => new sfValidatorDateTime(),
      'ag_event_facility_group_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEventFacilityGroup', 'required' => false)),
      'ag_scenario_facility_group_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agScenarioFacilityGroup', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agFacilityResource', 'column' => array('facility_id', 'facility_resource_type_id')))
    );

    $this->widgetSchema->setNameFormat('ag_facility_resource[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agFacilityResource';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_event_facility_group_list']))
    {
      $this->setDefault('ag_event_facility_group_list', $this->object->agEventFacilityGroup->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['ag_scenario_facility_group_list']))
    {
      $this->setDefault('ag_scenario_facility_group_list', $this->object->agScenarioFacilityGroup->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagEventFacilityGroupList($con);
    $this->saveagScenarioFacilityGroupList($con);

    parent::doSave($con);
  }

  public function saveagEventFacilityGroupList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_event_facility_group_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agEventFacilityGroup->getPrimaryKeys();
    $values = $this->getValue('ag_event_facility_group_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agEventFacilityGroup', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agEventFacilityGroup', array_values($link));
    }
  }

  public function saveagScenarioFacilityGroupList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_scenario_facility_group_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agScenarioFacilityGroup->getPrimaryKeys();
    $values = $this->getValue('ag_scenario_facility_group_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agScenarioFacilityGroup', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agScenarioFacilityGroup', array_values($link));
    }
  }

}