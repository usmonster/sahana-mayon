<?php
/**
* agEmbeddedScenarioShiftForm.class.php
*
* Provides embedded subform for editing facility resources
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
*/

class agEmbeddedScenarioShiftForm extends agScenarioShiftForm
{
  /**
   * configure()
   *
   * Extends the base class's configure method to perform
   * additional tasks
   */
  public function configure()
  {
    parent::configure();

    /**
     * Remove unused fields
     */
    unset(
//        $this['facility_id'],
        $this['created_at'],
        $this['updated_at']
//        $this['ag_event_facility_group_list'],
//        $this['ag_scenario_facility_group_list']
//#      $this['description']
    );
  }

//  private static $staticLists;


  /**
   * setup()
   *
   * Extends base class's setup() method to do some additional
   * setup
   */
  public function setup()
  {
    parent::setup();
    $this->setWidget(
    'scenario_facility_resource_id',
    new sfWidgetFormDoctrineChoice(array(
      'model' => $this->getRelatedModelName('agScenarioFacilityResource'),
      'add_empty' => true,
      'method' => 'getAgFacilityResource',
      //'query' => $this::$staticLists['agFacilityResourceType']
        //does the group match? it should...
        )
    ));
    $this->setWidget(
    'task_id',
    new sfWidgetFormDoctrineChoice(array(
      'model' => $this->getRelatedModelName('agTask'),
      'add_empty' => true,
      'method' => 'getTask',
      //'query' => $this::$staticLists['agFacilityResourceType']
        )
    ));
    $this->setWidget(
    'staff_resource_type_id',
    new sfWidgetFormDoctrineChoice(array(
      'model' => $this->getRelatedModelName('agStaffResourceType'),
      'add_empty' => true,
      'method' => 'getStaffResourceType',
      //'query' => $this::$staticLists['agFacilityResourceType']
        )
    ));
    $this->setWidget(
    'deployment_algorithm_id',
    new sfWidgetFormDoctrineChoice(array(
      'model' => $this->getRelatedModelName('agDeploymentAlgorithm'),
      'add_empty' => true,
      'method' => 'getDeploymentAlgorithm',
      //'query' => $this::$staticLists['agFacilityResourceType']
        )
    ));
    $this->setWidget(
    'shift_status_id',
    new sfWidgetFormDoctrineChoice(array(
      'model' => $this->getRelatedModelName('agShiftStatus'),
      'add_empty' => true,
      'method' => 'getShiftStatus',
      //'query' => $this::$staticLists['agFacilityResourceType']
        )
    ));
    $this->widgetSchema->setLabels(array(
        'shift_template' =>'Name',
        'minutes_start_to_facility_activation' =>'Minutes to facility activation',
        'staff_resource_type_id' => 'Staff Type'
        ));
    $this->getWidget('task_length_minutes')->setAttribute('size', '8');
    $this->getWidget('break_length_minutes')->setAttribute('size', '8');
    $this->getWidget('minutes_start_to_facility_activation')->setAttribute('size', '8');


//      'shift_repeats'                        => new sfValidatorInteger(),
//      'max_staff_repeat_shifts'              => new sfValidatorInteger(),

    $resDeco = new agWidgetFormSchemaFormatterRow($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('row', $resDeco);
    $this->getWidgetSchema()->setFormFormatterName('row');

//
  }

}
