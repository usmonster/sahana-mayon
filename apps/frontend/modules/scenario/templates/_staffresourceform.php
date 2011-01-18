<?php

if (isset($formsArray)){
  foreach ($formsArray as $key => $f) {
    // Create array of form names.
    $groupNames[] = $key;
  }
}
?>

<form action="<?php echo url_for('scenario/staffresources?id=' . $scenario->id) ?>" method="post">

  <?php
    //this is the same form that should be used for edit and create. display entered values if the objects exist.
    //
    // since this is the partial, we have to refer to view layer items with $this
    //
    // Set up the container form and its formatter.

    //$a_record = new agFacilityStaffResource(); //get an existing record if it exists
    //echo editable_content_tag('span', $a_record,'minimum_staff');

    $facilityStaffResourceContainer = new sfForm();
    $facilityStaffResourceConDeco = new agWidgetFormSchemaFormatterSubContainerLabel($facilityStaffResourceContainer->getWidgetSchema());
    $facilityStaffResourceContainer->getWidgetSchema()->addFormFormatter('facilityStaffResourceConDeco', $facilityStaffResourceConDeco);
    $facilityStaffResourceContainer->getWidgetSchema()->setFormFormatterName('facilityStaffResourceConDeco');
    foreach ($formsArray as $groupKey => $facilityGroup) {
      // Set up subcontainer forms, one for each facility group.
      $groupForm = new sfForm();
      $groupFormDeco = new agWidgetFormSchemaFormatterInlineBlock($groupForm->getWidgetSchema());
      $groupForm->getWidgetSchema()->addFormFormatter('groupFormDeco', $groupFormDeco);
      $groupForm->getWidgetSchema()->setFormFormatterName('groupFormDeco');
      // More container forms to hold the staff requirement forms for each facility.
      foreach ($facilityGroup as $resourceKey => $facilityResources) {
        $resourceForm = new sfForm();

        //if(isset($scenarioFacilityGroups)){  get our existing real data... so how about if isset(facgroupholder[facility][stafftype][minimum] set those fields!
        //
        $resourceFormDeco = new agWidgetFormSchemaFormatterInlineLeftLabel($resourceForm->getWidgetSchema());
        $resourceForm->getWidgetSchema()->addFormFormatter('resourceFormDeco', $resourceFormDeco);
        $resourceForm->getWidgetSchema()->setFormFormatterName('resourceFormDeco');
        foreach ($facilityResources as $staffKey => $staffResourceForm) {
          // And here are the real forms, the ones that will hold fields and data rather than just other forms.

          $staffResourceFormDeco = new agWidgetFormSchemaFormatterInlineTopLabel($staffResourceForm->getWidgetSchema());
          $staffResourceForm->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
          $staffResourceForm->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');
          //$staffResourceForm->update
          //if we already have existing data, set the defaults here
          if (isset($scenarioFacilityGroups[$groupKey][$resouceKey][$staffKey])){
            $staffResourceForm->setDefault('minimum', $scenarioFacilityGroups[$groupKey][$resouceKey][$staffKey]->minimum);
            $staffResourceForm->setDefault('maximum', $scenarioFacilityGroups[$groupKey][$resouceKey][$staffKey]->minimum);
          }

          $resourceForm->embedForm($staffKey, $staffResourceForm);
        }
        $groupForm->embedForm($resourceKey, $resourceForm);
      }
      $facilityStaffResourceContainer->embedForm($groupKey, $groupForm);
    }
    echo $facilityStaffResourceContainer;
  ?>
  <br />
  <br />
  <input class="linkButton" type="submit" value="Save" />
  <input class="linkButton" type="submit" value="Save and Continue" name="Continue"/>
</form>