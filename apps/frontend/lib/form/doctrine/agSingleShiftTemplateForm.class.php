<?php

/**
 * Extension of shift templates
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
class agSingleShiftTemplateForm extends agShiftTemplateForm
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
        $this['created_at'],
        $this['updated_at']
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
    $this->setWidgets(
        array(
          'id' => new sfWidgetFormInputHidden(),
          //'shift_template' => new sfWidgetFormInputText(array(), array('class' => 'inputGray')),
          //'description' => new sfWidgetFormInputText(array(), array('class' => 'inputGray')),
          'scenario_id' => new sfWidgetFormInputHidden(),
          'facility_resource_type_id' => new sfWidgetFormDoctrineChoice(
            array(
              'model' => $this->getRelatedModelName('agFacilityResourceType'),
              'add_empty' => false,'method' => 'getFacilityResourceType','label' => 'Facility Resource Type'
            //'query' => $this::$staticLists['agFacilityResourceType']
            )

          ),
          'staff_resource_type_id' => new sfWidgetFormDoctrineChoice(
            array(
              'model' => $this->getRelatedModelName('agStaffResourceType'),
              'add_empty' => false,
              'method' => 'getStaffResourceType','label' => 'Staff Resource Type'
            )
          ),
          'task_id' => new sfWidgetFormDoctrineChoice(
            array(
              'model' => $this->getRelatedModelName('agTask'),
              'add_empty' => false, 'method' => 'getTask','label' => 'Job'
            )
          ),
          'task_length_minutes' => new sfWidgetFormInputText(
              array('label'=> 'Shift Length'), array('class' => 'inputGray width30')
          ),
          'break_length_minutes' => new sfWidgetFormInputText(
              array('label' => 'Break Length'), array('class' => 'inputGray width30')
          ),
          'minutes_start_to_facility_activation' => new sfWidgetFormInputText(
              array('label' => 'Shifts Start Time'), array('class' => 'inputGray width30')
          ),
          'shift_repeats' => new sfWidgetFormInputText(
              array('label' => 'Days Facility Open For'), array('class' => 'inputGray width30')
          ),
          'max_staff_repeat_shifts' => new sfWidgetFormInputText(
              array('label' => 'Person Shift Repeats'), array('class' => 'inputGray width30')
          ),

          'shift_status_id' =>         new sfWidgetFormDoctrineChoice(
            array('label' => 'Shift Status',
              'model' => $this->getRelatedModelName('agShiftStatus'),
              'add_empty' => false,
              'method' => 'getShiftStatus',
            )
          )

          ,
          'deployment_algorithm_id' =>         new sfWidgetFormDoctrineChoice(
            array(
              'label' => 'Deployment Algorithm',
              'model' => $this->getRelatedModelName('agDeploymentAlgorithm'),
              'add_empty' => false,
              'method' => 'getDeploymentAlgorithm',
            )
          )

        )
    );
    $this->getWidget('task_length_minutes')->setAttribute('size', '8');
    $this->getWidget('break_length_minutes')->setAttribute('size', '8');
    $this->getWidget('minutes_start_to_facility_activation')->setAttribute('size', '8');
    $this->getWidget('max_staff_repeat_shifts')->setAttribute('size', '8');
//    $this->getWidgetSchema()->moveField(
//        'max_staff_repeat_shifts',
//        sfWidgetFormSchema::AFTER,
//        'minutes_start_to_facility_activation'
//    );
    $this->getWidget('shift_repeats')->setAttribute('size', '8');
    $this->getWidget('shift_status_id')->setAttribute('class', 'shifttemplateleft');

    $this->setValidators(
        array(
          'id' => new sfValidatorChoice(
              array(
                'choices' => array($this->getObject()->get('id')),
                'empty_value' => $this->getObject()->get('id'),
                'required' => false)
          ),
          'scenario_id' => new sfValidatorDoctrineChoice(
              array('required' => false, 'model' => $this->getRelatedModelName('agScenario'))
          ),
          'facility_resource_type_id' => new sfValidatorDoctrineChoice(
              array(
                'required' => false,
                'model' => $this->getRelatedModelName('agFacilityResourceType')
              )
          ),
          'staff_resource_type_id' => new sfValidatorDoctrineChoice(
              array(
                'required' => false,
                'model' => $this->getRelatedModelName('agStaffResourceType')
              )
          ),
          'task_id' => new sfValidatorDoctrineChoice(
              array(
                'required' => false,
                'model' => $this->getRelatedModelName('agTask')
              )
          ),
          'task_length_minutes' => new sfValidatorInteger(array('required' => false)),
          //normally this is time coming in, maybe it should be and we add a validator in transit
          'break_length_minutes' => new sfValidatorInteger(array('required' => false)),
          'minutes_start_to_facility_activation' => new sfValidatorInteger(
              array('required' => false)
          ),
          'shift_repeats' => new sfValidatorInteger(array('required' => false)),
          'max_staff_repeat_shifts' => new sfValidatorInteger(
              array('required' => false)
          ),
          // new agValidatorDaytoMinute(array('required' => false)),
          'shift_status_id' => new sfValidatorDoctrineChoice(
              array('required' => false, 'model' => $this->getRelatedModelName('agShiftStatus'))
          ),
          'deployment_algorithm_id' => new sfValidatorDoctrineChoice(
              array(
                'required' => false,
                'model' => $this->getRelatedModelName('agDeploymentAlgorithm')
              )
          ),
          'created_at' => new sfValidatorDateTime(),
          'updated_at' => new sfValidatorDateTime(),
        )
    );

    $this->widgetSchema->setNameFormat('ag_shift_template[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

  }

}
