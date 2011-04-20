<?php

/**
 * agEventShift form base class.
 *
 * @method agEventShift getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventShiftForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                   => new sfWidgetFormInputHidden(),
      'event_facility_resource_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityResource'), 'add_empty' => false)),
      'staff_resource_type_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => false)),
      'minimum_staff'                        => new sfWidgetFormInputText(),
      'maximum_staff'                        => new sfWidgetFormInputText(),
      'minutes_start_to_facility_activation' => new sfWidgetFormInputText(),
      'task_length_minutes'                  => new sfWidgetFormInputText(),
      'break_length_minutes'                 => new sfWidgetFormInputText(),
      'task_id'                              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftTask'), 'add_empty' => false)),
      'shift_status_id'                      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftStatus'), 'add_empty' => true)),
      'staff_wave'                           => new sfWidgetFormInputText(),
      'deployment_algorithm_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agDeploymentAlgorithm'), 'add_empty' => false)),
      'created_at'                           => new sfWidgetFormDateTime(),
      'updated_at'                           => new sfWidgetFormDateTime(),
      'ag_staff_event_list'                  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEventStaff')),
    ));

    $this->setValidators(array(
      'id'                                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_facility_resource_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityResource'))),
      'staff_resource_type_id'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'))),
      'minimum_staff'                        => new sfValidatorInteger(),
      'maximum_staff'                        => new sfValidatorInteger(),
      'minutes_start_to_facility_activation' => new sfValidatorInteger(),
      'task_length_minutes'                  => new sfValidatorInteger(),
      'break_length_minutes'                 => new sfValidatorInteger(),
      'task_id'                              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftTask'))),
      'shift_status_id'                      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agShiftStatus'), 'required' => false)),
      'staff_wave'                           => new sfValidatorInteger(array('required' => false)),
      'deployment_algorithm_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agDeploymentAlgorithm'))),
      'created_at'                           => new sfValidatorDateTime(),
      'updated_at'                           => new sfValidatorDateTime(),
      'ag_staff_event_list'                  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEventStaff', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_event_shift[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventShift';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_staff_event_list']))
    {
      $this->setDefault('ag_staff_event_list', $this->object->agStaffEvent->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagStaffEventList($con);

    parent::doSave($con);
  }

  public function saveagStaffEventList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_staff_event_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agStaffEvent->getPrimaryKeys();
    $values = $this->getValue('ag_staff_event_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agStaffEvent', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agStaffEvent', array_values($link));
    }
  }

}
