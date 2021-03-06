<?php

/**
 * agEventFacilityGroupForm extended class for creating event specific
 * facility groups
 *
 * @method agEventFacilityGroup getObject() Returns the current form's model object
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEventFacilityGroupForm extends BaseagEventFacilityGroupForm
{

  /** @method configure()
   * Unset widgets that are auto-filled, unnecessary, or whose relations are
   * not properly defined without using embedded forms.
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

    $this->setWidgets(
        array(
          'id' => new sfWidgetFormInputHidden(),
          'event_id' => new sfWidgetFormDoctrineChoice(
              array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => false)
          ),
          'event_facility_group' => new sfWidgetFormInputText(array('label' => 'Status')),
          'facility_group_type_id' => new sfWidgetFormDoctrineChoice(
              array(
                'model' => $this->getRelatedModelName('agFacilityGroupType'),
                'add_empty' => false
              )
          ),
          'facility_group_allocation_status_id' => new sfWidgetFormDoctrineChoice(
              array(
                'model' => $this->getRelatedModelName('agFacilityGroupAllocationStatus'),
                'add_empty' => false,
                'method' => 'getFacilityGroupAllocationStatus',
                'label' => 'Status'
              )
          ),
          'activation_sequence' => new sfWidgetFormInputText(),
          //facility resource list needs to minus options that are in $currentoptions
          //'ag_facility_resource_list'          => new sfWidgetFormChoice(array
          //  ('choices' => $availtoptions,'multiple' => true),
          //  array('class' => 'widthAndHeight150')),
          'ag_facility_resource_list' => new sfWidgetFormInputHidden(),
          //  sfWidgetFormDoctrineChoice(array('multiple' => true,
          //  'model' => 'agFacilityResource', 'expanded' => false),
          //  array('class' => 'widthAndHeight150')), //this will be hidden
          'ag_facility_resource_order' => new sfWidgetFormInputHidden(),
        //  sfWidgetFormChoice(array('choices' => $currentoptions,
        //  'multiple' => true),array('class' => 'widthAndHeight150')) //this will be hidden
        )
    );


    $this->setValidators(
        array(
          'id' => new sfValidatorChoice(
              array(
                'choices' => array($this->getObject()->get('id')),
                'empty_value' => $this->getObject()->get('id'),
                'required' => false
              )
          ),
          'event_id' => new sfValidatorDoctrineChoice(
              array('model' => $this->getRelatedModelName('agEvent'))
          ),
          'event_facility_group' => new sfValidatorString(array('trim' => true, 'max_length' => 64)),
          'facility_group_type_id' => new sfValidatorDoctrineChoice(
              array('model' => $this->getRelatedModelName('agFacilityGroupType'))
          ),
          'facility_group_allocation_status_id' => new sfValidatorDoctrineChoice(
              array('model' => $this->getRelatedModelName('agFacilityGroupAllocationStatus'))
          ),
          'activation_sequence' => new sfValidatorInteger(),
        //'ag_facility_resource_list'
        //           => new sfValidatorDoctrineChoice(array
        //             ('multiple' => true, 'model' => 'agFacilityResource', 'required' => false)),
        //'ag_facility_resource_order'
        //          => new sfValidatorChoice(array('required' => false))
        )
    );
    $this->validatorSchema->setOption('allow_extra_fields', true);
    $this->validatorSchema->setPostValidator(
        new sfValidatorAnd(
            array(
              new sfValidatorDoctrineUnique(
                  array('model' => 'agEventFacilityGroup', 'column' => array('id'))
              ),
              new sfValidatorDoctrineUnique(
                  array(
                    'model' => 'agEventFacilityGroup',
                    'column' => array('event_id', 'event_facility_group')
                  )
              ),
              new sfValidatorDoctrineUnique(
                  array(
                    'model' => 'agEventFacilityGroup',
                    'column' => array('event_facility_group')
                  )
              ),
            )
        )
    );
    $this->widgetSchema->setLabel('event_facility_group', 'Facility Group Name');
    $this->widgetSchema->setNameFormat('ag_event_facility_group[%s]');

    $custDeco = new agWidgetFormSchemaFormatterShift($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    //$this->setupInheritance();
  }

  /**
   *
   * @return string of the current models name
   */
  public function getModelName()
  {
    return 'agEventFacilityGroup';
  }

  /**
   *
   * @param sfDatabaseConnection $con
   * This is a private function that processes existing relationships
   * relevant to event facility groups, facility resources and their
   * activation sequence
   */
  protected function doSave($con = null)
  {
    $existing = $this->getObject()->getAgEventFacilityResource();
    foreach ($existing as $rec) {
      $current[] = $rec->facility_resource_id;
    }
    //$existing = $this->object->agFacilityResource->getPrimaryKeys();
    $values = $this->getTaintedValues();
    //all we need to save, is the allocated list: it's order included
    //(this is proving to be clumsy while working with a listbox, jquery is prefered)
    $alloc_array = json_decode($values['ag_facility_resource_order']);
    unset($this['ag_facility_resource_order']);
    unset($this['ag_facility_resource_list']);
    parent::doSave($con);
    if ($alloc_array) {
      //current is what is currently in that facility group
      //alloc_array is what the form is bringing in, so, we should do a diff.
      if (isset($current)) {
        $toDelete = array_diff($current, $alloc_array);
      } else {
        $current = array();
        $toDelete = array_diff($current, $alloc_array);
      }

      if (count($toDelete) > 0) {
        /** @todo clean this up, a subquery to delete on would be optimal */
        $deleteRecs = agDoctrineQuery::create()
                ->select('a.facility_resource_id')
                ->from('agEventFacilityResource a')
                ->whereIn('a.facility_resource_id', $toDelete)->execute();
        foreach ($deleteRecs as $deletor) {
          $deletor->delete();
        }
      }
      foreach ($alloc_array as $key => $value) {
        if (in_array($value, $current)) {
          $agEventFacilityResource = Doctrine_Core::getTable('agEventFacilityResource')
                  ->findByDql(
                      'facility_resource_id = ? AND event_facility_group_id = ?',
                      array($value, $this->getObject()->getId())
                  )
                  ->getFirst();
          $agEventFacilityResource->setActivationSequence($key + 1);
          //= new agEventFacilityResource($value);
          //if this already exists in the eventfacilitygroup as a resource, don't do anything
        } else {
          //if there isn't an entry in agEventFacilityResource for this group/facility_resource...
          //if this item exists, but the order is different. fail.
          $agEventFacilityResource = new agEventFacilityResource();

          $agEventFacilityResource->event_facility_group_id = $this->getObject()->getId();
          $agEventFacilityResource->activation_sequence = $key + 1;
          $agEventFacilityResource->facility_resource_id = $value;
          $agEventFacilityResource->facility_resource_allocation_status_id = 4;
        }
        $agEventFacilityResource->save();
      }
    } else {
      /**
       * @todo there are no values, so we need to delete
       *   if there aren't values in $values that exist in $checkArray, or if
       *  those items have changed.. (activation order, is the only thing)
       *  we need to update said item
       */
    }
  }

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