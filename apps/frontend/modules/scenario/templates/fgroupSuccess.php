<h2>Scenario Facility Groups</h2><br>
<?php
if ($scenarioFacilityGroups <> null) {
  include_partial('facilityGroupTable', array('scenarioFacilityGroups' => $scenarioFacilityGroups, 'scenarioName' => $scenarioName));
} else {
  echo '<h3>There are no facility groups associated with the <span class="highlightedText">' . $scenarioName . '</span> scenario</h3><br />';
}
?>
<h3>Create a New Facility Group for the <span class="highlightedText"><?php echo $scenarioName;
?> </span> Scenario</h3>
<p>Facility Groups are actually groupings of facility resources.  To create a facility group name
the group, assign the group type, allocation status, and the order in which is should be activated 
(activation sequence).</p>
<p><b>Note:</b> Facility resources should be created in the facility module.  If there are no records
  below use the "Plan" menu above to reach the Facility menu and add your facilities to Agasti.</p>
<div>
  <?php include_partial('groupform', array('groupform' => $groupform, 'ag_facility_resources' => $ag_facility_resources, 'ag_allocated_facility_resources' => $ag_allocated_facility_resources, 'scenario_id' => $scenario_id)) ?>
</div>
<p>Click "Save" to continue editing this group.  Click "Save and Continue" to save this group and
move to the next step.  Click "Save and Create Another" to save this grouping and create another
grouping.</p>
<b>Note:</b> facilities not grouped will not be available for activation when the Scenario is
deployed as an event.