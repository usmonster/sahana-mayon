<?php

/**
 * agEmbeddedFacilityResourceForm
 *
 * Provides embedded subform for editing facility resources
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Ilya Gulko, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEmbeddedFacilityResourceForm extends agFacilityResourceForm
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
        $this['facility_id'],
        $this['created_at'],
        $this['updated_at'],
        $this['ag_event_facility_group_list'],
        $this['ag_scenario_facility_group_list']
#      $this['description']
    );
  }

  /**
   * This static variable will hold dropdown choices that may be
   * reused if this form is embedded more than once.
   */
  private static $staticLists;

  /**
   * setup()
   *
   * Extends base class's setup() method to do some additional
   * setup
   */
  public function setup()
  {
    parent::setup();

    /**
     * Prefetch dropdown choices and store them in $staticLists in
     * case we need to use them later.
     */
    if (!isset($this::$staticLists)) {
      $lists = array('agFacilityResourceType', 'agFacilityResourceStatus');

      foreach ($lists as $list) {
        $this::$staticLists[$list] = Doctrine::getTable($list)->createQuery($list)->execute();
      }
    }

    /**
     * Prepopulate the Facility Resource Status widget with the
     * prefetched data.
     */
    $this->setWidget(
        'facility_resource_status_id',
        new sfWidgetFormDoctrineChoice(array(
          'model' => $this->getRelatedModelName('agFacilityResourceStatus'),
          'add_empty' => true,
          'method' => 'getFacilityResourceStatus',
          'query' => $this::$staticLists['agFacilityResourceStatus']
        ))
    );

    /**
     * Prepopulate the Facility Resource Type widget with the
     * prefetched data.
     */
    $this->setWidget(
        'facility_resource_type_id',
        new sfWidgetFormDoctrineChoice(array(
          'model' => $this->getRelatedModelName('agFacilityResourceType'),
          'add_empty' => true,
          'method' => 'getFacilityResourceType',
          'query' => $this::$staticLists['agFacilityResourceType']
            )
        )
    );

    /**
     * Add the Capacity field and Facility Code and add the inputGray style to it
     */
    $this->setWidget('capacity', new sfWidgetFormInputText(array(), array('class' => 'inputGray')));
    $this->setWidget('facility_resource_code', new sfWidgetFormInputText(array(), array('class' => 'inputGray')));
    /**
     * Set the validators for all visible fields to not required.
     * This will allow us to delete facility resource records by
     * leaving the fields blank.
     */
    $this->setValidator('capacity', new sfValidatorInteger(array('required' => false)));
    $this->setValidator('facility_resource_code', new sfValidatorPass(array('required' => false)));
    $this->setValidator(
        'facility_resource_status_id',
        new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFacilityResourceStatus'))));
    $this->setValidator(
        'facility_resource_type_id',
        new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agFacilityResourceType'))));

    /**
     * Set the formatter to agWidgetFormSchemaFormatterRow so that
     * fields are displayed horizontally.
     */
    $resDeco = new agWidgetFormSchemaFormatterRow($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('row', $resDeco);
    $this->getWidgetSchema()->setFormFormatterName('row');

    /**
     * Remove labels from all visible fields
     */
    $this->widgetSchema->setLabel('facility_resource_type_id', 'Resource Type');
    $this->widgetSchema->setLabel('facility_resource_status_id', 'Resource Status');
    $this->widgetSchema->setLabel('capacity', 'Capacity');
  }

}
