<?php
  $facilityStaffResourceContainer = new sfForm();
  $facilityStaffResourceConDeco = new agWidgetFormSchemaFormatterSubContainer($facilityStaffResourceContainer->getWidgetSchema());
  $facilityStaffResourceContainer->getWidgetSchema()->addFormFormatter('facilityStaffResourceConDeco', $facilityStaffResourceConDeco);
  $facilityStaffResourceContainer->getWidgetSchema()->setFormFormatterName('facilityStaffResourceConDeco');
  foreach ($staffResourceForms as $key => $srForm) {
    echo '<div>';
    $facilityStaffResourceContainer->embedForm($key, $srForm);
    echo '</div>';
  }
  echo $facilityStaffResourceContainer;
?>