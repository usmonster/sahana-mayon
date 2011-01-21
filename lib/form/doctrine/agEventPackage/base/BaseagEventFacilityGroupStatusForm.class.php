<?php

/**
 * agEventFacilityGroupStatus form base class.
 *
 * @method agEventFacilityGroupStatus getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventFacilityGroupStatusForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                  => new sfWidgetFormInputHidden(),
      'event_facility_group_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityGroup'), 'add_empty' => false)),
      'time_stamp'                          => new sfWidgetFormDateTime(),
      'facility_group_allocation_status_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityGroupAllocationStatus'), 'add_empty' => false)),
      'created_at'                          => new sfWidgetFormDateTime(),
      'updated_at'                          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_facility_group_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityGroup'))),
      'time_stamp'                          => new sfValidatorDateTime(),
      'facility_group_allocation_status_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityGroupAllocationStatus'))),
      'created_at'                          => new sfValidatorDateTime(),
      'updated_at'                          => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventFacilityGroupStatus', 'column' => array('event_facility_group_id', 'time_stamp')))
    );

    $this->widgetSchema->setNameFormat('ag_event_facility_group_status[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventFacilityGroupStatus';
  }

}