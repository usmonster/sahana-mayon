<?php

/**
 * agScenarioFacilityGroupForm extended class for creating scenario specific
 * facility groups
 *
 * @method agScenarioFacilityGroup getObject() Returns the current form's model object
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agScenarioFacilityGroupForm extends BaseagScenarioFacilityGroupForm
{

  /** @method configure()
   * Unset widgets that are auto-filled, unnecessary, or whose relations are not
   * properly defined without using embedded forms.
   * */
  public function configure()
  {
    unset($this['created_at'],
        $this['updated_at'],
        $this['ag_shift_template_list']
    );
  }

  /** @method configure()
   *  here we are creating/setting widgets/validation for the form
   * */
  public function setup()
  {
    sfProjectConfiguration::getActive()->loadHelpers(array ('Helper','Url', 'Asset', 'Tag'));
    $this->wikiUrl = url_for('@wiki');
    $this->setWidgets(
        array(
          'id' => new sfWidgetFormInputHidden(),
          'scenario_id' => new sfWidgetFormInputHidden(),
          'scenario_facility_group' => new sfWidgetFormInputText(
              array('label' => 'Status')
          ),
          'facility_group_type_id' => new sfWidgetFormDoctrineChoice(
              array(
                'model' => $this->getRelatedModelName('agFacilityGroupType'),
                'add_empty' => false,
                'label' => 'Facility Group Type <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:facility_group_type&do=export_xhtmlbody" class="tooltipTrigger" title="Facility Group Type">?</a>'
              )
          ),
          'facility_group_allocation_status_id' => new sfWidgetFormDoctrineChoice(
            array(
              'model' => $this->getRelatedModelName('agFacilityGroupAllocationStatus'),
              'add_empty' => false,
              'method' => 'getFacilityGroupAllocationStatus',
              'label' => 'Status <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:facility_group_status&do=export_xhtmlbody" class="tooltipTrigger" title="Facility Group Status">?</a>'
            )
          ),
          'activation_sequence' => new sfWidgetFormInputText(array('label' => 'Priority <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:deployment_priority&do=export_xhtmlbody" class="tooltipTrigger" title="Deployment Priority">?</a>'), array('class' => 'inputGraySmall')),
          //facility resource list needs to minus options that are in $currentoptions
          //'ag_facility_resource_list'          => new sfWidgetFormChoice(array
          //  ('choices' => $availtoptions,'multiple' => true),array('class' => 'widthAndHeight150')),
          'ag_facility_resource_list' => new sfWidgetFormInputHidden(),
          //sfWidgetFormDoctrineChoice(array
          //  ('multiple' => true, 'model' => 'agFacilityResource', 'expanded' => false),
          //   array('class' => 'widthAndHeight300')), //this will be hidden
//          'ag_facility_resource_order' => new sfWidgetFormInputHidden(),
          'values' => new sfWidgetFormInputHidden(),
        ////sfWidgetFormChoice(array
        //  ('choices' => $currentoptions,'multiple' => true),
        //  array('class' => 'widthAndHeight300')) //this will be hidden
        )
    );

    $defaultGroupAllocationStatus = agScenarioFacilityGroup::getDefaultAllocationStatus();
    $this->setDefaults(array('facility_group_allocation_status_id' => $defaultGroupAllocationStatus));

    $this->setValidators(
        array(
          'id' => new sfValidatorChoice(
              array(
                'choices' => array($this->getObject()->get('id')),
                'empty_value' => $this->getObject()->get('id'), 'required' => false
              )
          ),
          'scenario_id' => new sfValidatorDoctrineChoice(
              array('model' => $this->getRelatedModelName('agScenario'))
          ),
          'scenario_facility_group' => new sfValidatorString(array('trim' => true, 'max_length' => 64)),
          'facility_group_type_id' => new sfValidatorDoctrineChoice(
              array('model' => $this->getRelatedModelName('agFacilityGroupType'))
          ),
          'facility_group_allocation_status_id' => new sfValidatorDoctrineChoice(
              array('model' => $this->getRelatedModelName('agFacilityGroupAllocationStatus'))
          ),
          'activation_sequence' => new sfValidatorInteger(),
        //'ag_facility_resource_list'           => new sfValidatorDoctrineChoice(array
        //  ('multiple' => true, 'model' => 'agFacilityResource', 'required' => false)),
        //'ag_facility_resource_order'          => new sfValidatorChoice(array
        //  ('required' => false))
        )
    );
    
    $this->validatorSchema->setOption('allow_extra_fields', true);
    $this->validatorSchema->setPostValidator(
        new sfValidatorAnd(
            array(
              new sfValidatorDoctrineUnique(
                  array('model' => 'agScenarioFacilityGroup', 'column' => array('id'))
              ),
              new sfValidatorDoctrineUnique(
                  array(
                    'model' => 'agScenarioFacilityGroup',
                    'column' => array('scenario_id', 'scenario_facility_group')
                  )
              ),
              new sfValidatorDoctrineUnique(
                  array(
                    'model' => 'agScenarioFacilityGroup',
                    'column' => array('scenario_facility_group')
                  )
              ),
            )
        )
    );
   $this->getValidator('scenario_id')->setMessage('required', 'Your facility group must be associated with a scenario.');
   $this->getValidator('scenario_facility_group')->setMessage('required', 'You must enter a name for this Facility Group.');
   $this->getValidatorSchema()->getPostValidator()->setMessage('invalid', 'A Facility Group with the same name already exists.');

    $this->widgetSchema->setLabel('scenario_facility_group', 'Facility Group Name <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:facility_group_name&do=export_xhtmlbody" class="tooltipTrigger" title="Facility Group Name">?</a>');
    $this->widgetSchema->setNameFormat('ag_scenario_facility_group[%s]');

    $groupDeco = new agWidgetFormSchemaFormatterInlineBigTopLabel($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('groupFormDeco', $groupDeco);
    $this->getWidgetSchema()->setFormFormatterName('groupFormDeco');
    $this->getWidget('scenario_facility_group')->setAttribute('class', 'inputGray');
    $this->getWidget('activation_sequence')->setAttribute('class', 'inputGraySmall');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    //$this->setupInheritance();
  }

  /**
   *
   * @return string of the current models name
   */
  public function getModelName()
  {
    return 'agScenarioFacilityGroup';
  }

  /**
   *
   * @param sfDatabaseConnection $con
   * This is a private function that processes existing relationships
   * relevant to scenario facility groups, facility resources and their activation sequence
   */
//  protected function doSave($con = null)
//  {
//    $existing = $this->getObject()->getAgScenarioFacilityResource();
//    foreach ($existing as $rec) {
//      $current[] = $rec->facility_resource_id;
//    }
//    //$existing = $this->object->agFacilityResource->getPrimaryKeys();
//    $values = $this->getTaintedValues();
//    //all we need to save, is the allocated list: it's order included
//    //(this is proving to be clumsy while working with a listbox, jquery is prefered)
//    $alloc_array = json_decode($values['ag_facility_resource_order']);
//    unset($this['ag_facility_resource_order']);
//    unset($this['ag_facility_resource_list']);
//    parent::doSave($con);
//    if ($alloc_array) {
//      //current is what is currently in that facility group
//      //alloc_array is what the form is bringing in, so, we should do a diff.
//      if (isset($current)) {
//        $toDelete = array_diff($current, $alloc_array);
//      } else {
//        $current = array();
//        $toDelete = array_diff($current, $alloc_array);
//      }
//
//      if (count($toDelete) > 0) {
//        /** @todo clean this up, a subquery to delete on would be optimal */
//        $deleteRecs = agDoctrineQuery::create()
//                ->select('a.facility_resource_id')
//                ->from('agScenarioFacilityResource a')
//                ->whereIn('a.facility_resource_id', $toDelete)->execute();
//        foreach ($deleteRecs as $deletor) {
//          $deletor->delete();
//        }
//      }
//      foreach ($alloc_array as $key => $value) {
//        if (in_array($value, $current)) {
//
//          $agScenarioFacilityResource = Doctrine_Core::getTable('agScenarioFacilityResource')->findByDql('facility_resource_id = ? AND scenario_facility_group_id = ?', array($value, $this->getObject()->getId()))->getFirst();
//          $agScenarioFacilityResource->setActivationSequence($key + 1);
//          //= new agScenarioFacilityResource($value);
//          //if this already exists in the scenariofacilitygroup as a resource, don't do anything
//        } else {
//          //if there isn't an entry in agScenarioFacilityResource for this group/facility_resource...
//          //if this item exists, but the order is different. fail.
//          $agScenarioFacilityResource = new agScenarioFacilityResource();
//
//          $agScenarioFacilityResource->scenario_facility_group_id = $this->getObject()->getId();
//          $agScenarioFacilityResource->activation_sequence = $key + 1;
//          $agScenarioFacilityResource->facility_resource_id = $value;
//          $agScenarioFacilityResource->facility_resource_allocation_status_id = 2;
//          //ready / awaiting activation / committed
//        }
//        $agScenarioFacilityResource->save();
//      }
//    } else {
//      /**
//       * @todo there are no values, so we need to delete
//       *   if there aren't values in $values that exist in $checkArray, or if
//       *  those items have changed.. (activation order, is the only thing)
//       *  we need to update said item
//       */
//    }
//  }

  /**
   *
   * @param <type> $con
   * @param <type> $forms 
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    /**
     * @todo add a hook here to handle the selected facility resources' activation sequence.
     * currently, we select the activation of the facility GROUP, we need to also set the
     *  activation sequence of each individual facility resource
     */
  }

}