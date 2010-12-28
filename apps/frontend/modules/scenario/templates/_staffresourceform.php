<?php
  $facilityStaffResourceContainer = new sfForm();
  $facilityStaffResourceConDeco = new agWidgetFormSchemaFormatterSubContainer($facilityStaffResourceContainer->getWidgetSchema());
  $facilityStaffResourceContainer->getWidgetSchema()->addFormFormatter('facilityStaffResourceConDeco', $facilityStaffResourceConDeco);
  $facilityStaffResourceContainer->getWidgetSchema()->setFormFormatterName('facilityStaffResourceConDeco');
  foreach ($staffResourceForms as $key => $srForm) {
    $facilityStaffResourceContainer->embedForm($key, $srForm);
  }
  foreach ($facilityStaffResourceContainer->getEmbeddedForms() as $key => $eForm) {
    echo '<div style="float: left; border: solid 1px #dadada; margin: 5px; padding: 5px;">';
    echo '<h4>' . $key . '</h4>';
    echo $eForm;
    echo '</div>';
  }
?>