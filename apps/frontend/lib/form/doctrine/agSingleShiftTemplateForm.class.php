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
  public $scenario_id;

  public function __construct($scenario_id = null,$shiftTemplate = array())
  {
    if ($scenario_id == null) {
      throw new LogicException('you must provide a scenario_id to construct a shift template form');
    } else {
      $this->scenario_id = $scenario_id;
    }
    parent::__construct($shiftTemplate, array(), array());
  }
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
    sfProjectConfiguration::getActive()->loadHelpers(array ('Helper','Url', 'Asset', 'Tag'));
    $this->wikiUrl = url_for('@wiki');
    
    $this->setWidgets(
        array(
          'id' => new sfWidgetFormInputHidden(),
          //'shift_template' => new sfWidgetFormInputText(array(), array('class' => 'inputGray')),
          //'description' => new sfWidgetFormInputText(array(), array('class' => 'inputGray')),
          'scenario_id' => new sfWidgetFormInputHidden,

          'facility_resource_type_id' =>
          new sfWidgetFormDoctrineChoice(
            array(
              'model' => $this->getRelatedModelName('agFacilityResourceType'),
              'add_empty' => false,
              'method' => 'getFacilityResourceType',
              'label' => 'Facility Resource Type'
            )
          )
          ,
          'staff_resource_type_id' =>
          new sfWidgetFormDoctrineChoice(
            array(
              'model' => $this->getRelatedModelName('agStaffResourceType'),
              'add_empty' => false,
              'method' => 'getStaffResourceType',
              'label' => 'Staff Resource Type <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:staff_resources&do=export_xhtmlbody" class="tooltipTrigger" title="Search Name">?</a>'
            )
          )
          ,
          'task_id' => new sfWidgetFormDoctrineChoice(
            array(
              'model' => $this->getRelatedModelName('agTask'),
              'add_empty' => false, 'method' => 'getTask','label' => 'Job <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:scenario_job&do=export_xhtmlbody" class="tooltipTrigger" title="Scenario Job">?</a>'
            )
          ),

          'task_length_minutes' => new sfWidgetFormInputHidden(),
          'break_length_minutes' => new sfWidgetFormInputHidden(),
          'minutes_start_to_facility_activation' => new sfWidgetFormInputHidden(),

          'days_in_operation' => new sfWidgetFormInputText(
              array('label' => 'Days in Operation <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:days_in_op&do=export_xhtmlbody" class="tooltipTrigger" title="Days in Operation">?</a>'), array('class' => 'inputGray width30')
          ),
          'max_staff_repeat_shifts' => new sfWidgetFormInputText(
              array('label' => 'Consecutive Staff Shifts <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:consecutive_shifts&do=export_xhtmlbody" class="tooltipTrigger" title="Consecutive Staff Shifts">?</a>'), array('class' => 'inputGray width30')
          ),

          'shift_status_id' =>         new sfWidgetFormDoctrineChoice(
            array('label' => 'Shift Status <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:shift_status&do=export_xhtmlbody" class="tooltipTrigger" title="Shift Status">?</a>',
              'model' => $this->getRelatedModelName('agShiftStatus'),
              'add_empty' => false,
              'method' => 'getShiftStatus',
            )
          )

          ,
          'deployment_algorithm_id' =>         new sfWidgetFormDoctrineChoice(
            array(
              'label' => 'Deployment Method <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:deployment_method&do=export_xhtmlbody" class="tooltipTrigger" title="Deployment Method">?</a>',
              'model' => $this->getRelatedModelName('agDeploymentAlgorithm'),
              'add_empty' => false,
              'method' => 'getDeploymentAlgorithm',
            )
          )

        )
    );
    $this->getWidgetSchema()->setLabel('task_length_minutes', false);
    $this->getWidgetSchema()->setLabel('break_length_minutes', false);
    $this->getWidgetSchema()->setLabel('minutes_start_to_facility_activation', false);
    
    $this->getWidget('max_staff_repeat_shifts')->setAttribute('size', '8');
//    $this->getWidgetSchema()->moveField(
//        'max_staff_repeat_shifts',
//        sfWidgetFormSchema::AFTER,
//        'minutes_start_to_facility_activation'
//    );
    $this->getWidget('days_in_operation')->setAttribute('size', '8');
    $this->getWidget('shift_status_id');

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
          'days_in_operation' => new sfValidatorInteger(array('required' => false)),
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
    $this->setDefault('scenario_id', $this->scenario_id);
    $this->widgetSchema->setNameFormat('ag_shift_template[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    //$this->setupInheritance();

    $custDeco = new agWidgetFormSchemaFormatterInlineLeftLabel2($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');

  }

}
