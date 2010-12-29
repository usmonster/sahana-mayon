<h3>
  Assign staff resource requirements to the facilities within
  <span class="logName">
    <?php echo $scenarioFacilityGroup['scenario_facility_group'] ?>
  </span>
  :
</h3>
<?php
echo '<h3>' . $scenarioFacilityGroup['scenario_facility_group'] . '</h3>';
$facilityResources = $scenarioFacilityGroup->getAgFacilityResource();

foreach ($facilityResources as $facilityResource) {
  $a[] = $facilityResource;
}
  foreach ($staffResourceTypes as $srt) {
    $staffResourceForms[$srt->staff_resource_type] = new agFacilityStaffResourceForm();
    
    $staffResourceFormDeco = new agWidgetFormSchemaFormatterInlineLabels($staffResourceForms[$srt->staff_resource_type]->getWidgetSchema());
    $staffResourceForms[$srt->staff_resource_type]->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
    $staffResourceForms[$srt->staff_resource_type]->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');

    $staffResourceForms[$srt->staff_resource_type]->getWidget('minimum_staff')->setAttribute('class', 'inputGraySmall');
    $staffResourceForms[$srt->staff_resource_type]->getWidget('maximum_staff')->setAttribute('class', 'inputGraySmall');

    $staffResourceForms[$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('created_at');
    $staffResourceForms[$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('updated_at');
    $staffResourceForms[$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('scenario_facility_resource_id');
    $staffResourceForms[$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('id');
    $staffResourceForms[$srt->staff_resource_type]->getWidgetSchema()->offsetUnset('staff_resource_type_id');
  }
  include_partial('staffresourceform', array(
      'staffResourceForms' => $staffResourceForms,
     // 'ag_facility_resources' => $ag_facility_resources,
     // 'ag_allocated_facility_resources' => $ag_allocated_facility_resources
  ));
?>




