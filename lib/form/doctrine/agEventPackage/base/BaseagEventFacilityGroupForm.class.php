<?php

/**
 * agEventFacilityGroup form base class.
 *
 * @method agEventFacilityGroup getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventFacilityGroupForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                        => new sfWidgetFormInputHidden(),
      'event_id'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => false)),
      'event_facility_group'      => new sfWidgetFormInputText(),
      'facility_group_type_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityGroupType'), 'add_empty' => false)),
      'activation_sequence'       => new sfWidgetFormInputText(),
      'created_at'                => new sfWidgetFormDateTime(),
      'updated_at'                => new sfWidgetFormDateTime(),
      'ag_facility_resource_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResource')),
    ));

    $this->setValidators(array(
      'id'                        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_id'                  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'))),
      'event_facility_group'      => new sfValidatorString(array('max_length' => 64)),
      'facility_group_type_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityGroupType'))),
      'activation_sequence'       => new sfValidatorInteger(array('required' => false)),
      'created_at'                => new sfValidatorDateTime(),
      'updated_at'                => new sfValidatorDateTime(),
      'ag_facility_resource_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResource', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agEventFacilityGroup', 'column' => array('event_id', 'event_facility_group'))),
        new sfValidatorDoctrineUnique(array('model' => 'agEventFacilityGroup', 'column' => array('event_facility_group'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_event_facility_group[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventFacilityGroup';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_facility_resource_list']))
    {
      $this->setDefault('ag_facility_resource_list', $this->object->agFacilityResource->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagFacilityResourceList($con);

    parent::doSave($con);
  }

  public function saveagFacilityResourceList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_facility_resource_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agFacilityResource->getPrimaryKeys();
    $values = $this->getValue('ag_facility_resource_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agFacilityResource', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agFacilityResource', array_values($link));
    }
  }

}