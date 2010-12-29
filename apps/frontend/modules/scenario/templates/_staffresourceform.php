<?php
  $facilityStaffResourceContainer = new sfForm();
  $facilityStaffResourceConDeco = new agWidgetFormSchemaFormatterSubContainer($facilityStaffResourceContainer->getWidgetSchema());
  $facilityStaffResourceContainer->getWidgetSchema()->addFormFormatter('facilityStaffResourceConDeco', $facilityStaffResourceConDeco);
  $facilityStaffResourceContainer->getWidgetSchema()->setFormFormatterName('facilityStaffResourceConDeco');
  foreach ($formsArray as $groupKey => $facilityGroup) {
    $groupForm = new sfForm();
    $groupFormDeco = new agWidgetFormSchemaFormatterInlineBlock($groupForm->getWidgetSchema());
    $groupForm->getWidgetSchema()->addFormFormatter('groupFormDeco', $groupFormDeco);
    $groupForm->getWidgetSchema()->setFormFormatterName('groupFormDeco');
    foreach ($facilityGroup as $resourceKey => $facilityResources) {
      $resourceForm = new sfForm();
      $resourceFormDeco = new agWidgetFormSchemaFormatterInlineTopLabel($resourceForm->getWidgetSchema());
      $resourceForm->getWidgetSchema()->addFormFormatter('resourceFormDeco', $resourceFormDeco);
      $resourceForm->getWidgetSchema()->setFormFormatterName('resourceFormDeco');
      foreach ($facilityResources as $staffKey => $staffResource) {
        $resourceForm->embedForm($staffKey, $staffResource);
      }
      $groupForm->embedForm($resourceKey, $resourceForm);
    }
    $facilityStaffResourceContainer->embedForm($groupKey, $groupForm);
  }
//  foreach ($facilityStaffResourceContainer->getEmbeddedForms() as $gForm) {
//    echo '<div style="float: left; border: solid 1px #dadada; margin: 5px 0px; padding: 5px;">';
//    foreach ($gForm->getEmbeddedForms() as $key => $fForm) {
//      echo '<div>';
//      echo '<h4>' . $key . '</h4>';
//      echo $fForm;
//      echo '</div>';
//    }
//    echo '</div>';
//  }
  echo $facilityStaffResourceContainer;
  echo new agFacilityStaffResourceForm();
?>