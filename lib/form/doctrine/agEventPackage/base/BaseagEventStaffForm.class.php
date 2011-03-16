<?php

/**
 * agEventStaff form base class.
 *
 * @method agEventStaff getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventStaffForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                           => new sfWidgetFormInputHidden(),
      'event_id'                     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => false)),
      'staff_resource_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResource'), 'add_empty' => false)),
      'deployment_weight'            => new sfWidgetFormInputText(),
      'created_at'                   => new sfWidgetFormDateTime(),
      'updated_at'                   => new sfWidgetFormDateTime(),
      'ag_event_facility_shift_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEventShift')),
    ));

    $this->setValidators(array(
      'id'                           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_id'                     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'))),
      'staff_resource_id'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResource'))),
      'deployment_weight'            => new sfValidatorInteger(),
      'created_at'                   => new sfValidatorDateTime(),
      'updated_at'                   => new sfValidatorDateTime(),
      'ag_event_facility_shift_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEventShift', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventStaff', 'column' => array('event_id', 'staff_resource_id')))
    );

    $this->widgetSchema->setNameFormat('ag_event_staff[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventStaff';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_event_facility_shift_list']))
    {
      $this->setDefault('ag_event_facility_shift_list', $this->object->agEventFacilityShift->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagEventFacilityShiftList($con);

    parent::doSave($con);
  }

  public function saveagEventFacilityShiftList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_event_facility_shift_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agEventFacilityShift->getPrimaryKeys();
    $values = $this->getValue('ag_event_facility_shift_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agEventFacilityShift', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agEventFacilityShift', array_values($link));
    }
  }

}