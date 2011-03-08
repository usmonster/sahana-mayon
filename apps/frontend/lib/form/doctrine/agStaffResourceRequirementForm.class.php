<?php

/**
 * agStaffResourceRequirementForm extended agScenarioFacilityResource form base class, for assigning staff resource requirements
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agStaffResourceRequirementForm extends BaseagScenarioFacilityResourceForm
{
    /*
   * configure() extends the base method to remove unused fields
   */
  public function configure()
  {
    parent::configure();

    unset(
        $this['updated_at'],
        $this['created_at'],
        $this['activation_sequence'],
        $this['ag_staff_resource_type_list'],
        $this['facility_resource_allocation_status_id'],
        $this['facility_resource_id']
    );
  }
  public function setup()
  {
    parent::setup();

    /**
     *  unset fields that we will not be displaying in the for
     */
    unset(
        $this['updated_at'],
        $this['created_at'],
        $this['activation_sequence']
    );

    /**
     *  create the container form for all facility resources
     *   */
    $facilityResourceContainer = new sfForm(array(), array());

    /**
     *  get all facility resources contained in this facility group
     *   */
    $this->agFacilityResources = '';
//    if ($this->agFacilityResources = Doctrine::getTable('agScenarioFacilityResource')
//            ->createQuery('agSFR')
//            ->select('agSFR.*')
//            ->from('agScenarioFacilityResource aSFR')
//            ->where('scenario_facility_group_id = ?', $this->getObject()->getScenarioFacilityGroupId())
//            ->execute()) {
//
//      /**
//       *  for every existing facility resource, create an agEmbeddedFacilityResourceForm and embed it into $facilityResourceContainer
//       *   */
//      foreach ($this->agFacilityResources as $facilityResource) {
//        $facilityResourceForm = new agEmbeddedFacilityStaffResourceForm($facilityResource);
//
//        $facilityResourceId = $facilityResource->getId();
//        $facilityResourceContainer->embedForm($facilityResourceId, $facilityResourceForm);
//        $facilityResourceContainer->widgetSchema->setLabel($facilityResourceId, false);
//      }
//    }

    /**
     *  create a up to 2 new blank agEmbeddedFacilityResourceForm
     *  instances in case the user wants to add another staff resource type
     *   */
    for ($iNewForm = 0; $iNewForm < max(2-count($this->agFacilityResources), 1); $iNewForm++) {
      $facilityResourceForm = new agEmbeddedFacilityStaffResourceForm();

      $facilityResourceContainer->embedForm('new'.$iNewForm, $facilityResourceForm);
      $facilityResourceContainer->widgetSchema->setLabel('new'.$iNewForm, false);
    }

    /**
     *  embed the facility resource container form into the facility form
     *   */
    $this->embedForm('resource', $facilityResourceContainer);

    /**
     * Sort the widgets by using getPositions() and useFields().
     * Because useFields() also specifies all of the fields/widgets
     * that will be displayed, we have to take care to not get rid
     * of any fields.
     */
//    $formFields = $this->getWidgetSchema()->getPositions();
//    $useFields = array('facility_name', 'facility_code');
//    $useFields = array_merge($useFields, array_diff($formFields, $useFields));
//    $this->useFields($useFields);


    /**
     * Set labels on a few fields
     */
    $this->widgetSchema->setLabels(array(
      'resource' => 'Resources',
      'scenario_facility_groupd_id' => 'Facility Group',
      //'facility_code' => 'Facility Code'
    ));

    /**
     * Set the formatter of this form to
     * agWidgetFormSchemaFormatterSection
     */
    $sectionsDeco = new agWidgetFormSchemaFormatterSection($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('section', $sectionsDeco);
    $this->getWidgetSchema()->setFormFormatterName('section');
  }

  /**
   * saveEmbeddedForms() is a recursive function that saves all
   * forms supplied in the $forms parameter or in the form it is
   * called from
   *
   * @param $con database connection (optional)
   * @param $forms array of sfForm objects to save
   *
   * @return parent class's saveEmbeddedForms()
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms) {
      $forms = $this->embeddedForms;
    }

    if (is_array($forms)) {
      foreach ($forms as $key => $form) {
        /**
         *  Facility Resource section
         * */
        if ($form instanceof agEmbeddedFacilityStaffResourceForm) {
          if ($form->isNew()) {
            $newFacilityResource = $form->getObject();
            if ($newFacilityResource->capacity && $newFacilityResource->facility_resource_type_id
                && $newFacilityResource->facility_resource_status_id) {
              $newFacilityResource->setFacilityId($this->getObject()->getId());
              $newFacilityResource->save();
              $this->getObject()->getAgFacilityResource()->add($newFacilityResource);
              unset($forms[$key]);
            } else {
              unset($forms[$key]);
            }
          } else {
            $objFacilityResource = $form->getObject();
            if ($objFacilityResource->capacity && $objFacilityResource->facility_resource_type_id
                && $objFacilityResource->facility_resource_status_id) {
              $form->getObject()->save();
            } else {
              $form->getObject()->delete();
            }
            unset($forms[$key]);
            //$form->getObject()->setFacilityId($this->getObject()->getId());
          }
        }

      }
    }
    return parent::saveEmbeddedForms($con, $forms);
  }

}