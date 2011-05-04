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
    $formatter = new agFormatterInlineLists($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('defaultFormDeco', $formatter);
    $this->getWidgetSchema()->setFormFormatterName('defaultFormDeco');
    $this->embedStaffForm();
    $this->embedFacilityForm();

    //set up default facility resource type form
  }

  public function embedStaffForm(){

  $staffTypeForm =  new sfForm();
//  $staffTypeForm->
   $staffConDeco = new agWidgetFormSchemaFormatter2($staffTypeForm->getWidgetSchema());
   $staffTypeForm->getWidgetSchema()->addFormFormatter('staffConDeco', $staffConDeco);
   $staffTypeForm->getWidgetSchema()->setFormFormatterName('staffConDeco');
      

    unset($staffTypeForm['created_at'], $staffTypeForm['updated_at']);
    $staffTypeForm->setWidget('scenario_id', new sfWidgetFormInputHidden());
    $staffTypeForm->setDefault('scenario_id', $this->scenario_id);
    $staffTypeForm->setWidget('staff_resource_type_id', new sfWidgetFormDoctrineChoice(array(
      'model' => 'agStaffResourceType',
      'method' => 'getStaffResourceType',
      'add_empty' => false,
      'label' => 'Staff Resource Types',
      'multiple' => true,
      'expanded' => true,
      'order_by' => array('staff_resource_type', 'asc')
      )));

    $staffDefaults = agDoctrineQuery::create()
            ->select('dfrt.staff_resource_type_id')
            ->from('agDefaultScenarioStaffResourceType dfrt')
            ->where('dfrt.scenario_id = ?',  $this->scenario_id)
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      $staffTypeForm->setDefault('staff_resource_type_id', $staffDefaults);
    $this->embedForm('staff_types', $staffTypeForm);
  }
  public function embedFacilityForm(){
   $facilityTypeForm =  new sfForm();  
   $staffConDeco = new agWidgetFormSchemaFormatter2($facilityTypeForm->getWidgetSchema());
   $facilityTypeForm->getWidgetSchema()->addFormFormatter('staffConDeco', $staffConDeco);
   $facilityTypeForm->getWidgetSchema()->setFormFormatterName('staffConDeco');

   // $facilityTypeForm = new agDefaultScenarioFacilityResourceTypeForm($this->defaultFacilityResourceTypes);
    unset($facilityTypeForm['created_at'], $facilityTypeForm['updated_at']);
    $facilityTypeForm->setWidget('scenario_id', new sfWidgetFormInputHidden());
    $facilityTypeForm->setDefault('scenario_id', $this->scenario_id);
    $facilityTypeForm->setWidget('facility_resource_type_id', new sfWidgetFormDoctrineChoice(array(
      'model' => 'agFacilityResourceType',
      'method' => 'getFacilityResourceType',
      'add_empty' => false,
      'label' => 'Facility Resource Types',
      'multiple' => true,
      'expanded' => true,
      'order_by' => array('facility_resource_type', 'asc')
      )));

      $facilityDefaults = agDoctrineQuery::create()
            ->select('dfrt.facility_resource_type_id')
            ->from('agDefaultScenarioFacilityResourceType dfrt')
            ->where('dfrt.scenario_id = ?',  $this->scenario_id)
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

      $facilityTypeForm->setDefault('facility_resource_type_id', $facilityDefaults);
    $this->embedForm('facility_types', $facilityTypeForm);
  }


}

?>
