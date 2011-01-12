<?php
  if($scenarioFacilityGroups <> null) {
    include_partial('facilityGroupTable', array('scenarioFacilityGroups' => $scenarioFacilityGroups, 'scenarioName' => $scenarioName));
  } else {
    echo '<h3>There are no facility groups associated with the <span style="color: #ff8f00">' . $scenarioName . '</span> scenario</h3><br />';
  }
?>
<h3>Create New Facility Group for the <span style="color: #ff8f00;"><?php echo $scenarioName; ?> </span> scenario</h3>
<div>
  <?php include_partial('groupform', array('groupform' => $groupform,'ag_facility_resources' => $ag_facility_resources, 'ag_allocated_facility_resources' => $ag_allocated_facility_resources)) ?>
</div>