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
          if (isset($scenarioFacilityGroups)){
            if(isset($scenarioFacilityGroups[$groupKey][$resouceKey][$staffKey])){

              $staffResourceForm->setDefault('minimum', $scenarioFacilityGroups[$groupKey][$resouceKey][$staffKey]->minimum);
              $staffResourceForm->setDefault('maximum', $scenarioFacilityGroups[$groupKey][$resouceKey][$staffKey]->minimum);
            }
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


<?php if(isset($scenarioFacilityGroups)){ ?>
<?php foreach ($scenarioFacilityGroups as $facilityGroup): ?>
<table class="singleTable">
  <thead>
      <caption><?php echo $facilityGroup->scenario_facility_group;?></caption>
  </thead>
  <tbody>
    <?php foreach ($facilityGroup->getAgScenarioFacilityResource() as $scenarioFacilityResource): ?>
    <tr>
      <th class="head" colspan="<?php echo (count($scenarioFacilityResource->getAgFacilityStaffResource()) * 3);?>"><?php echo $scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type); ?></th>
    </tr>
    <tr>
      <?php foreach ($scenarioFacilityResource->getAgFacilityStaffResource() as $key => $staffResourceType): ?>
      <th class="<?php echo (($key == 0) ? 'subHeadLeft' : 'subHeadMid'); ?>"><?php echo ucwords($staffResourceType->getAgStaffResourceType()->staff_resource_type); ?></th>
      <td>Min: <?php echo $staffResourceType->minimum_staff; ?></td>
      <td>Max: <?php echo $staffResourceType->maximum_staff; ?></td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br />
<?php endforeach; ?>
<?php
}
?>