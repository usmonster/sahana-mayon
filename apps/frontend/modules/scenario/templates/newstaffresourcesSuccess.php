<h3>Hello</h3>
<?php
  foreach ($staffResourceTypes as $srt) {
    $staffResourceForms[$srt->staff_resource_type] = new agFacilityStaffResourceForm();
    
    $staffResourceFormDeco = new agWidgetFormSchemaFormatterInlineLabels($staffResourceForms[$srt->staff_resource_type]->getWidgetSchema());
    $staffResourceForms[$srt->staff_resource_type]->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
    $staffResourceForms[$srt->staff_resource_type]->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');

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




