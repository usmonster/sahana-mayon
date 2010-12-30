<h3>
  <span>
    Assign staff resource requirements to the facilities within the
  </span>
  <span class="logName">
    <?php echo $scenarioFacilityGroup['scenario_facility_group'] ?>
  </span>
  <span>
    group:
  </span>
</h3>
<?php
foreach ($scenarioFacilityResources as $scenarioFacilityResource) {
  foreach ($staffResourceTypes as $srt) {
    $subKey = $scenarioFacilityGroup['scenario_facility_group'];
    $subSubKey = $scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type);
    
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type] =
            new agEmbeddedAgFacilityStaffResourceForm();
    
    $staffResourceFormDeco = new agWidgetFormSchemaFormatterInlineLabels($formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema());
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');

    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidget('minimum_staff')->setAttribute('class', 'inputGraySmall');
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidget('maximum_staff')->setAttribute('class', 'inputGraySmall');
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->setLabel('minimum_staff', 'Min');
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->setLabel('maximum_staff', 'Max');

    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('created_at');
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('updated_at');
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('scenario_facility_resource_id');
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('id');
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('staff_resource_type_id');
  }
}

  include_partial('staffresourceform', array(
      'formsArray' => $formsArray,
     // 'ag_facility_resources' => $ag_facility_resources,
     // 'ag_allocated_facility_resources' => $ag_allocated_facility_resources
  ));
?>




