<?php
  use_javascript('agMain.js');
?>
<h3>Facility Group Listing</h3>

<table class="staffTable"">
    <tr class="head">
      <th>Facility Group</th>
      <th>Facility Group Type</th>
      <th>Allocation Status</th>
      <th>Activation Sequence</th>
      <th>Facility Resource Count</th>
    </tr>
  <tbody>
    <?php foreach ($ag_scenario_facility_groups as $ag_scenario_facility_group): ?>
      <tr>
        <td><a href="<?php echo url_for('scenario/fgroup?id=' . $ag_scenario_facility_group->scenario_id) . '?groupid=' . $ag_scenario_facility_group->id ?>" class="linkButton">
          <?php echo $ag_scenario_facility_group->getScenarioFacilityGroup() ?></a></td>
      <td><?php echo $ag_scenario_facility_group->getAgFacilityGroupType() ?></td>
      <td><?php echo $ag_scenario_facility_group->getAgFacilityGroupAllocationStatus() ?></td>
      <td><?php echo $ag_scenario_facility_group->getActivationSequence() ?></td>
      <td><?php echo count($ag_scenario_facility_group->getAgFacilityResource()) ?></td>
    </tr>
    <?php endforeach; ?>
        </tbody>
      </table>
<hr />
    <a href="<?php echo url_for('scenario/fgroup?id=' . $ag_scenario_facility_group->scenario_id) ?>" class="linkButton" title="New Facility Group">New</a>