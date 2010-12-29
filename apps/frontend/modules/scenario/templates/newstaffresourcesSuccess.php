<h3>
  Assign staff resource requirements to the facilities within
  <span class="logName">
    <?php echo $scenarioFacilityGroup['scenario_facility_group'] ?>
  </span>
  :
</h3>
<?php
foreach ($scenarioFacilityResources as $scenarioFacilityResource) {
  foreach ($staffResourceTypes as $srt) {
    $formsArray[$scenarioFacilityGroup['scenario_facility_group']][$scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type)][$srt->staff_resource_type] =
            new agFacilityStaffResourceForm();
    $staffResourceFormDeco = new agWidgetFormSchemaFormatterInlineLabels($formsArray[$scenarioFacilityGroup['scenario_facility_group']][$scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type)][$srt->staff_resource_type]->getWidgetSchema());
    $formsArray[$scenarioFacilityGroup['scenario_facility_group']][$scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type)][$srt->staff_resource_type]->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
    $formsArray[$scenarioFacilityGroup['scenario_facility_group']][$scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type)][$srt->staff_resource_type]->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');

    $formsArray[$scenarioFacilityGroup['scenario_facility_group']][$scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type)][$srt->staff_resource_type]->getWidget('minimum_staff')->setAttribute('class', 'inputGraySmall');
    $formsArray[$scenarioFacilityGroup['scenario_facility_group']][$scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type)][$srt->staff_resource_type]->getWidget('maximum_staff')->setAttribute('class', 'inputGraySmall');

    $formsArray[$scenarioFacilityGroup['scenario_facility_group']][$scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type)][$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('created_at');
    $formsArray[$scenarioFacilityGroup['scenario_facility_group']][$scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type)][$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('updated_at');
    $formsArray[$scenarioFacilityGroup['scenario_facility_group']][$scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type)][$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('scenario_facility_resource_id');
    $formsArray[$scenarioFacilityGroup['scenario_facility_group']][$scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type)][$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('id');
    $formsArray[$scenarioFacilityGroup['scenario_facility_group']][$scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type)][$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('staff_resource_type_id');
  }
}

  include_partial('staffresourceform', array(
      'formsArray' => $formsArray,
     // 'ag_facility_resources' => $ag_facility_resources,
     // 'ag_allocated_facility_resources' => $ag_allocated_facility_resources
  ));
?>




