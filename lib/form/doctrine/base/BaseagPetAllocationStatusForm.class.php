<?php

/**
 * agPetAllocationStatus form base class.
 *
 * @method agPetAllocationStatus getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPetAllocationStatusForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                            => new sfWidgetFormInputHidden(),
      'pet_id'                        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPet'), 'add_empty' => false)),
      'time_stamp'                    => new sfWidgetFormDateTime(),
      'event_facility_resource_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityResource'), 'add_empty' => false)),
      'pet_allocation_status_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPetAllocationStatusType'), 'add_empty' => false)),
      'created_at'                    => new sfWidgetFormDateTime(),
      'updated_at'                    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'pet_id'                        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPet'))),
      'time_stamp'                    => new sfValidatorDateTime(),
      'event_facility_resource_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityResource'))),
      'pet_allocation_status_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPetAllocationStatusType'))),
      'created_at'                    => new sfValidatorDateTime(),
      'updated_at'                    => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agPetAllocationStatus', 'column' => array('pet_id', 'time_stamp')))
    );

    $this->widgetSchema->setNameFormat('ag_pet_allocation_status[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPetAllocationStatus';
  }

}
