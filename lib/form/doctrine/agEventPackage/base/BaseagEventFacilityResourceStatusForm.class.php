<?php

/**
 * agEventFacilityResourceStatus form base class.
 *
 * @method agEventFacilityResourceStatus getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventFacilityResourceStatusForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                     => new sfWidgetFormInputHidden(),
      'event_facility_resource_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityResource'), 'add_empty' => false)),
      'time_stamp'                             => new sfWidgetFormDateTime(),
      'facility_resource_allocation_status_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceAllocationStatus'), 'add_empty' => true)),
      'created_at'                             => new sfWidgetFormDateTime(),
      'updated_at'                             => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_facility_resource_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityResource'))),
      'time_stamp'                             => new sfValidatorDateTime(),
      'facility_resource_allocation_status_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceAllocationStatus'), 'required' => false)),
      'created_at'                             => new sfValidatorDateTime(),
      'updated_at'                             => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventFacilityResourceStatus', 'column' => array('event_facility_resource_id', 'time_stamp')))
    );

    $this->widgetSchema->setNameFormat('ag_event_facility_resource_status[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventFacilityResourceStatus';
  }

}
