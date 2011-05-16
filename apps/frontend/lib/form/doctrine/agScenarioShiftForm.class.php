<?php

/**
 * Creates Scenario Shift form
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agScenarioShiftForm extends BaseagScenarioShiftForm
{


  public $scenario_id;

    public function __construct($scenario_id = null,$scenarioShift = array())
  {
    if ($scenario_id == null) {
      throw new LogicException('you must provide a scenario_id to construct a shift form');
    } else {
      $this->scenario_id = $scenario_id;
    }
    parent::__construct($scenarioShift, array(), array());
  }  
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    foreach ($taintedValues as $key => $scenarioShift) {
      if (!isset($this[$key])) {
          
        parent::bind($scenarioShift, $taintedFiles);
        //$this->addShiftTemplateForm($key);
      }
    }

  }
  
  public function configure()
  {
    unset($this['created_at'],
        $this['updated_at']
    );
  }

  public function setup()
  {
    unset($this['id'],
        $this['created_at'],
        $this['updated_at']
    );
      //get default staff resource types for this scenario
      $dsrt = agScenarioResourceHelper::returnDefaultStaffResourceTypes($this->scenario_id);
      if (count($dsrt) > 0) {
        $defaultStaffResourceTypes = $dsrt;
      } else {
        $defaultStaffResourceTypes =
                agDoctrineQuery::create()
                ->select('srt.id, srt.staff_resource_type')
                ->from('agStaffResourceType srt')
                ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
      }
      foreach($defaultStaffResourceTypes as $dsrt){
        $defaultStaffTypes[$dsrt['srt_id']] = $dsrt['srt_staff_resource_type'];
      }

        $scenarioFacilityResources =
                agDoctrineQuery::create()
                ->select('sfg.id, sfr.id, f.facility_name')
                ->from('agScenarioFacilityGroup sfg')
                ->leftJoin('sfg.agScenarioFacilityResource sfr')
                ->leftJoin('sfr.agFacilityResource fr')
                ->leftJoin('fr.agFacility f')
                ->where('sfg.scenario_id = ?', $this->scenario_id)
                ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
      foreach($scenarioFacilityResources as $facilityResource){
        $facilityResources[$facilityResource['sfr_id']] = $facilityResource['f_facility_name'];
      }
    
    $this->setWidgets(array
      (
      'id' => new sfWidgetFormInputHidden(),

      'scenario_facility_resource_id' => new sfWidgetFormChoice(array(
        'choices' => $facilityResources),
              array('label' => 'Scenario Facility Resource','class' => 'filter')
              ),
      
      
      'staff_resource_type_id' => new sfWidgetFormChoice(array(
        'choices' => $defaultStaffTypes),
              array('label' => 'Staff Resource Type','class' => 'filter')
              ),    
      
      
      'task_id' => new sfWidgetFormDoctrineChoice(array
        ('model' => $this->getRelatedModelName('agTask'), 'add_empty' => false)),
      'task_length_minutes' => new sfWidgetFormInputHidden(),
      'break_length_minutes' => new sfWidgetFormInputHidden(),
      'minutes_start_to_facility_activation' => new sfWidgetFormInputHidden(),
      'minimum_staff' => new sfWidgetFormInputText(array(),array('class' => 'inputGray')),
      'maximum_staff' => new sfWidgetFormInputText(array(),array('class' => 'inputGray')),
      'staff_wave' => new sfWidgetFormInputText(array(),array('class' => 'inputGray')),
      'shift_status_id' => new sfWidgetFormDoctrineChoice(array
        ('model' => $this->getRelatedModelName('agShiftStatus'), 'add_empty' => false)),
      'deployment_algorithm_id' => new sfWidgetFormDoctrineChoice(array
        ('model' => $this->getRelatedModelName('agDeploymentAlgorithm'), 'add_empty' => false)),
    )
        );


    $this->setValidators(array(
//      'id' => new sfValidatorChoice(array('choices' => array
//      ($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'scenario_facility_resource_id' => new sfValidatorDoctrineChoice(array
        ('model' => $this->getRelatedModelName('agScenarioFacilityResource'))),
      'staff_resource_type_id' => new sfValidatorDoctrineChoice(array
        ('model' => $this->getRelatedModelName('agStaffResourceType'))),
      'task_id' => new sfValidatorDoctrineChoice(array
        ('model' => $this->getRelatedModelName('agTask'))),
      'task_length_minutes' => new sfValidatorInteger(), //(array('max' => 10)),
      'break_length_minutes' => new sfValidatorInteger(), //(array('max' => 10)),
      'minutes_start_to_facility_activation' => new sfValidatorInteger(), //(array('max' => 20)),
      'minimum_staff' => new sfValidatorInteger(), //(array('max' => 5)),
      'maximum_staff' => new sfValidatorInteger(), //(array('max' => 5)),
      'staff_wave' => new sfValidatorInteger(), //(array('max' => 5)),
      'shift_status_id' => new sfValidatorDoctrineChoice(array
        ('model' => $this->getRelatedModelName('agShiftStatus'))),
      'deployment_algorithm_id' => new sfValidatorDoctrineChoice(array
        ('model' => $this->getRelatedModelName('agDeploymentAlgorithm'))),
    ));

    $this->validatorSchema->setOption('allow_extra_fields', true);

    
    //change the name format of this form so the sliders are applied nicely
    $this->widgetSchema->setNameFormat('shift_template[0][%s]');

    $custDeco = new agWidgetFormSchemaFormatterInlineLeftLabel2($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    //$this->setupInheritance();
  }

}
