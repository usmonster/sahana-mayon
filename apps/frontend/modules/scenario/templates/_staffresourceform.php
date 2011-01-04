<?php
  foreach ($formsArray as $key => $f) {
    // Create array of form names.
    $groupNames[] = $key;
  }
  $a = $scenario;
?>
<form action="<?php echo url_for('scenario/facilityStaffResourceCreate' . '?scenarioId=' . $scenario->id) ?>" method="post">
  <?php
    // Set up the container form and its formatter.
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
        $resourceFormDeco = new agWidgetFormSchemaFormatterInlineLeftLabel($resourceForm->getWidgetSchema());
        $resourceForm->getWidgetSchema()->addFormFormatter('resourceFormDeco', $resourceFormDeco);
        $resourceForm->getWidgetSchema()->setFormFormatterName('resourceFormDeco');
        foreach ($facilityResources as $staffKey => $staffResourceForm) {
          // And here are the real forms, the ones that will hold fields and data rather than just other forms.
          $staffResourceFormDeco = new agWidgetFormSchemaFormatterInlineTopLabel($staffResourceForm->getWidgetSchema());
          $staffResourceForm->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
          $staffResourceForm->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');
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