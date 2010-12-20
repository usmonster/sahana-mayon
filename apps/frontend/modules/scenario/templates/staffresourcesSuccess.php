<div style="float:left;"><h3>Required Staff Resources for </h3> <h4><?php echo $ag_scenario_facility_group->getScenarioFacilityGroup() ?></h4></div>

<table>
  <thead>
    <tr>
      <th>Facility Resource</th>
      <th>Staff Resource</th>
      <th>Minimum</th>
      <th>Maximum</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_staff_resources as $ag_staff_resource): ?>
    <tr>
      <td><a href="<?php echo url_for('scenario/editstaffresources?id='.$ag_staff_resource->getId()) ?>"><?php echo $ag_staff_resource->getAgFacilityResource()->getAgFacility() ?></a>
        <br /><?php echo $ag_staff_resource->getAgFacilityResource()->getCapacity() ?>
      </td>
      <td><?php echo $ag_staff_resource->getAgFacilityStaffResource()->getFirst() ?></td>
      <td><?php echo $ag_staff_resource->getAgFacilityStaffResource()->getFirst()->getMinimumStaff() ?><br /></td>
      <td><?php echo $ag_staff_resource->getAgFacilityStaffResource()->getFirst()->getMaximumStaff() ?><br /></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h3>Add Facility Resource Requirement</h3>
<?php include_partial('staffresourceform', array('staffresourceform' => $staffresourceform, 'ag_staff_resources' => $ag_staff_resources)) ?>