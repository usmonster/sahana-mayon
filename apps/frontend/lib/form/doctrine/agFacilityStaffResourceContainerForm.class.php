<?php

/**
 *
 * Provides embedded subform for editing facility resources
 * 
 * agFacilityStaffResourceContainerForm class takes in an array of
 * forms with which to construct a set of embedded forms
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
class agFacilityStaffResourceContainerForm extends sfForm
{

  private $formsArray;
  private $facilityLabels;

/**
 *
 * @param type $formsArray array of forms
 * @param type $facilityLabels labels for the facility resource containers
 */
  public function __construct($formsArray = null, $facilityLabels = null) //we should disallow null from coming in as we NEED an array of forms
  {
    $this->formsArray = $formsArray;
    $this->facilityLabels = $facilityLabels;
    parent::__construct(array(), array(), array());
  }

  public function configure()
  {
    $this->widgetSchema->setNameFormat('staff_resource[%s]');
    $this->embedFacilityStaffResourceForms();
  }

  public function embedFacilityStaffResourceForms()
  {

    $facilityStaffResourceConDeco = new agWidgetFormSchemaFormatterSubContainerLabel($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('facilityStaffResourceConDeco', $facilityStaffResourceConDeco);
    $this->getWidgetSchema()->setFormFormatterName('facilityStaffResourceConDeco');
    foreach ($this->formsArray as $groupKey => $facilityGroup) {
      // Set up subcontainer forms, one for each facility group.
      $groupForm = new sfForm();
      $groupFormDeco = new agWidgetFormSchemaFormatterInlineBlock($groupForm->getWidgetSchema());
      $groupForm->getWidgetSchema()->addFormFormatter('groupFormDeco', $groupFormDeco);
      $groupForm->getWidgetSchema()->setFormFormatterName('groupFormDeco');
      // More container forms to hold the staff requirement forms for each facility.
      foreach ($facilityGroup as $facilityResourceKey => $facilityResources) {
        $facilityResourceLabel = $this->facilityLabels[$groupKey][$facilityResourceKey];
//                    $subSubKeyLabel = $resourceKey
//            ->getAgFacilityResource()
//                ->getAgFacility()->facility_name . 
//                ': ' . ucwords($scenarioFacilityResource
//                    ->getAgFacilityResource()
//                    ->getAgFacilityResourceType()->facility_resource_type) . 
//                    ' (' . $scenarioFacilityResource
//                    ->getAgFacilityResource()
//                        ->getAgFacility()->facility_code . ')';
        //TODO ^ add nice labels to facility resource containers.
        
        $resourceForm = new sfForm();

        //if(isset($scenarioFacilityGroups)){  get our existing real data...
        //so how about if isset(facgroupholder[facility][stafftype][minimum] set those fields!
        //
        $resourceFormDeco = new agFormFormatterInlineLeftLabel($resourceForm->getWidgetSchema());
        $resourceForm->getWidgetSchema()->addFormFormatter('resourceFormDeco', $resourceFormDeco);
        $resourceForm->getWidgetSchema()->setFormFormatterName('resourceFormDeco');

        foreach ($facilityResources as $staffKey => $staffResourceForm) {
          // And here are the real forms, the ones that will hold fields
          // and data rather than just other forms.

     //     $staffResourceFormDeco = new agWidgetFormSchemaFormatterInlineTopLabel($staffResourceForm->getWidgetSchema());
     //     $staffResourceForm->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
     //     $staffResourceForm->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');
          //$staffResourceForm->update
          //if we already have existing data, set the defaults here
          if (isset($scenarioFacilityGroups)) {
            if (isset($scenarioFacilityGroups[$groupKey][$resouceKey][$staffKey])) {

              $staffResourceForm->setDefault('minimum', $scenarioFacilityGroups[$groupKey][$resouceKey][$staffKey]->minimum);
              $staffResourceForm->setDefault('maximum', $scenarioFacilityGroups[$groupKey][$resouceKey][$staffKey]->minimum);
            }
          }

          $resourceForm->embedForm($staffKey, $staffResourceForm);
        }
        
        $groupForm->embedForm($facilityResourceLabel, $resourceForm);
      }
      $this->embedForm($groupKey, $groupForm);
    }
  }

  /**
   * this is the only saving that takes place since the sfForm has nothing but embedded forms
   * @param $con to maintain amorphism this interface is copied
   * @param $forms to maintain amorphism this interface is copied
   */
//  public function saveEmbeddedForms($con = null, $forms = null)
//  {
//    if (isset($this->embeddedForms['lucene_search'])) {
//      $form = $this->embeddedForms['lucene_search'];
//      $values = $this->values['lucene_search'];
//      $this->saveLuceneForm($form, $values);
//      unset($this->embeddedForms['lucene_search']);
//    }
//
//    if (isset($this->embeddedForms['staff_generator'])) {
//      $form = $this->embeddedForms['staff_generator'];
//      $values = $this->values['staff_generator'];
//      $this->saveStaffGenForm($form, $values);
//      unset($this->embeddedForms['staff_generator']);
//    }
//  }
}
