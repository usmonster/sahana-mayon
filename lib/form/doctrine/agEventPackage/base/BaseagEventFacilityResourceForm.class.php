<?php

/**
 * agEventFacilityResource form base class.
 *
 * @method agEventFacilityResource getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventFacilityResourceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'facility_resource_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResource'), 'add_empty' => false)),
      'event_facility_group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityGroup'), 'add_empty' => false)),
      'activation_sequence'     => new sfWidgetFormInputText(),
      'created_at'              => new sfWidgetFormDateTime(),
      'updated_at'              => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'facility_resource_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResource'))),
      'event_facility_group_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityGroup'))),
      'activation_sequence'     => new sfValidatorInteger(array('required' => false)),
      'created_at'              => new sfValidatorDateTime(),
      'updated_at'              => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventFacilityResource', 'column' => array('facility_resource_id', 'event_facility_group_id')))
    );

    $this->widgetSchema->setNameFormat('ag_event_facility_resource[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventFacilityResource';
  }

}
