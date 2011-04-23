<?php

/**
 * Agasti ag Default Resource Type Form Class - A class to generate either a new staff pool form or an
 * edit staff pool form
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 * */
class agDefaultResourceTypeForm extends sfForm
{

  protected $scenario_id;

  public function __construct($scenario_id)
  {
    $this->defaultStaffResourceTypes = NULL;//$defaultStaffResourceTypes;
    $this->defaultFacilityResourceTypes = NULL;//$defaultFacilityResourceTypes;
    if($scenario_id != NULL) $this->scenario_id = $scenario_id;
    else
      throw new LogicException ('you must have a scenario id');
    parent::__construct(array(), array(), array());
  }

  public function configure()
  {
    $this->widgetSchema->setNameFormat('default_resource_types[%s]');
    $staffTypeForm = new agDefaultScenarioStaffResourceTypeForm($this->defaultStaffResourceTypes);
    unset($staffTypeForm['created_at'], $staffTypeForm['updated_at']);
    $staffTypeForm->setWidget('scenario_id', new sfWidgetFormInputHidden());
    $staffTypeForm->setDefault('scenario_id', $this->scenario_id);
    $staffTypeForm->setWidget('staff_resource_type_id', new sfWidgetFormDoctrineChoice(array(
      'model' => 'agStaffResourceType',
      'method' => 'getStaffResourceType',
      'add_empty' => false,
      'label' => 'Staff Resource Types',
      'multiple' => true)));


    //set up default facility resource type form
    $facilityTypeForm = new agDefaultScenarioFacilityResourceTypeForm($this->defaultFacilityResourceTypes);
    unset($facilityTypeForm['created_at'], $facilityTypeForm['updated_at']);
    $facilityTypeForm->setWidget('scenario_id', new sfWidgetFormInputHidden());
    $facilityTypeForm->setDefault('scenario_id', $this->scenario_id);
    $facilityTypeForm->setWidget('facility_resource_type_id', new sfWidgetFormDoctrineChoice(array(
      'model' => 'agFacilityResourceType',
      'method' => 'getFacilityResourceType',
      'add_empty' => false,
      'label' => 'Facility Resource Types',
      'multiple' => true)));

//embed our forms
    $this->embedForm('staff_types', $staffTypeForm);
    $this->embedForm('facility_types', $facilityTypeForm);
  }

  /**
   * this is the only saving that takes place since the sfForm has nothing but embedded forms
   * @param $con to maintain amorphism this interface is copied
   * @param $forms to maintain amorphism this interface is copied
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (isset($this->embeddedForms['staff_types'])) {
      $form = $this->embeddedForms['staff_types'];
      $values = $this->values['staff_types'];
      $this->saveAForm($form, $values);
      unset($this->embeddedForms['staff_types']);
    }

    if (isset($this->embeddedForms['facility_types'])) {
      $form = $this->embeddedForms['facility_types'];
      $values = $this->values['facility_types'];
      $this->saveAForm($form, $values);
      unset($this->embeddedForms['facility_types']);
    }
  }

  /**
   * save the embedded lucene form
   * @param sfForm $form a form to process
   * @param mixed $values a set of values coming from a post
   */
  public function saveAForm($form, $values)
  {
    $form->updateObject($values);

    $form->getObject()->save();
  }

}

?>
