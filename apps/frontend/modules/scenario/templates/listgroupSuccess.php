<h3>Facility Group Listing</h3>

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
      <td><a href="<?php echo url_for('scenario/editgroup?id='.$ag_scenario_facility_group->getId()) ?>"><?php echo $ag_scenario_facility_group->getScenarioFacilityGroup() ?></a></td>
      <td><?php echo $ag_scenario_facility_group->getAgFacilityGroupType() ?></td>
      <td><?php echo $ag_scenario_facility_group->getAgFacilityGroupAllocationStatus() ?></td>
      <td><?php echo $ag_scenario_facility_group->getActivationSequence() ?></td>
      <td><?php echo count($ag_scenario_facility_group->getAgFacilityResource()) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<a href="<?php echo url_for('scenario/newgroup') ?>" class="buttonText" title="New Facility Group">New</a>
