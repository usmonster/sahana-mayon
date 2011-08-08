<?php

/**
 * agFacilityStaffResource form base class.
 *
 * @method agFacilityStaffResource getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagFacilityStaffResourceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                            => new sfWidgetFormInputHidden(),
      'scenario_facility_resource_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityResource'), 'add_empty' => false)),
      'staff_resource_type_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => false)),
      'minimum_staff'                 => new sfWidgetFormInputText(),
      'maximum_staff'                 => new sfWidgetFormInputText(),
      'created_at'                    => new sfWidgetFormDateTime(),
      'updated_at'                    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'scenario_facility_resource_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agScenarioFacilityResource'))),
      'staff_resource_type_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'))),
      'minimum_staff'                 => new sfValidatorInteger(),
      'maximum_staff'                 => new sfValidatorInteger(),
      'created_at'                    => new sfValidatorDateTime(),
      'updated_at'                    => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agFacilityStaffResource', 'column' => array('scenario_facility_resource_id', 'staff_resource_type_id')))
    );

    $this->widgetSchema->setNameFormat('ag_facility_staff_resource[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agFacilityStaffResource';
  }

}
