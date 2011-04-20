<?php

/**
 * agEventFacilityDistribution form base class.
 *
 * @method agEventFacilityDistribution getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventFacilityDistributionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'event_service_area_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventServiceArea'), 'add_empty' => false)),
      'event_facility_group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityGroup'), 'add_empty' => false)),
      'created_at'              => new sfWidgetFormDateTime(),
      'updated_at'              => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_service_area_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventServiceArea'))),
      'event_facility_group_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityGroup'))),
      'created_at'              => new sfValidatorDateTime(),
      'updated_at'              => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventFacilityDistribution', 'column' => array('event_service_area_id', 'event_facility_group_id')))
    );

    $this->widgetSchema->setNameFormat('ag_event_facility_distribution[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventFacilityDistribution';
  }

}
