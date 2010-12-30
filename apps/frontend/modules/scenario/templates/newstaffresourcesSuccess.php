<h3>
  <span>
    Assign minimum and maximum staff resource requirements to the facilities within the
  </span>
  <span class="logName">
    <?php echo $scenarioFacilityGroup['scenario_facility_group'] ?>
  </span>
  <span>
    group:
  </span>
</h3>
<br />
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
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->setDefault('scenario_facility_resource_id', $scenarioFacilityResource->getId());
    $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->setDefault('staff_resource_type_id', $srt->getId());
  }
}

  include_partial('staffresourceform', array(
      'formsArray' => $formsArray,
      'scenarioFacilityGroupId' => $scenarioFacilityGroup->id,
     // 'ag_facility_resources' => $ag_facility_resources,
     // 'ag_allocated_facility_resources' => $ag_allocated_facility_resources
  ));
?>