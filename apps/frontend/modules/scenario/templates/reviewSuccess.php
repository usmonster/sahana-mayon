<?php
// This page is currently a stub!  The following random string is a marker for the stub.
// PnaODfcm3Kiz4MV4vzbtr4
// PLEASE REMOVE THIS COMMENT BLOCK WHEN YOU DEVELOP THIS PAGE!
?>

<h2>"<?php echo $scenario_name ?>" Scenario Review</h2>
<p>List scenario name and description.  Then the Facility Groups and number.</p>

<h3>Facility groups:</h3>
<table>
  <thead>
    <tr>
      <th>Facility Group</th>
      <th>Facility Group Type</th>
      <th>Allocation Status</th>
      <th>Activation Sequence</th>
      <th>Facility Resource Count</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_scenario_facility_groups as $ag_scenario_facility_group): ?>
      <tr>
        <td><a href="<?php echo url_for('scenario/editgroup?id=' . $ag_scenario_facility_group->getId()) ?>"><?php echo $ag_scenario_facility_group->getScenarioFacilityGroup() ?></a></td>
        <td><?php echo $ag_scenario_facility_group->getAgFacilityGroupType() ?></td>
        <td><?php echo $ag_scenario_facility_group->getAgFacilityGroupAllocationStatus() ?></td>
        <td><?php echo $ag_scenario_facility_group->getActivationSequence() ?></td>
        <td><?php echo count($ag_scenario_facility_group->getAgFacilityResource()) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>


  <a href="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) ?>" class="linkButton">Staff Pool Definitions</a>
  <a href="<?php echo url_for('scenario/shifttemplates?id=' . $scenario_id) ?>" class="linkButton">Staff Shifts</a>
  <a href="<?php echo url_for('scenario/staffresources?id=' . $scenario_id) ?>" class="linkButton">Staff Resource Requirements</a>