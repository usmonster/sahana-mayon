<h3>Required Staff Resources for <span style="color: #ff8f00;"><?php echo $ag_scenario_facility_group->getScenarioFacilityGroup() ?></span></h3>

<table class="singleTable">
  <thead>
    <tr>
      <th class="head" style="padding: 2px 4px;">Facility Resource</th>
      <th class="head" style="padding: 2px 4px;">Facility Resource Capacity</th>
      <th class="head" style="padding: 2px 4px;">Staff Resource</th>
      <th class="head" style="padding: 2px 4px;">Minimum</th>
      <th class="head" style="padding: 2px 4px;">Maximum</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_staff_resources as $ag_staff_resource): ?>
    <tr>
      <td><a href="<?php echo url_for('scenario/editstaffresources?id='.$ag_staff_resource->getId()) ?>"><?php echo ucwords($ag_staff_resource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type) ?></a></td>
      <td><?php //echo $ag_staff_resource->getAgFacilityResource()->getCapacity() ?></td>
      <td><?php //echo $ag_staff_resource->getAgFacilityStaffResource()->getFirst()->getAgStaffResourceType()->staff_resource_type ?></td>
      <td><?php //echo $ag_staff_resource->getAgFacilityStaffResource()->getFirst()->getMinimumStaff() ?><br /></td>
      <td><?php //echo $ag_staff_resource->getAgFacilityStaffResource()->getFirst()->getMaximumStaff() ?><br /></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h3>Add Facility Resource Requirement</h3>
<?php include_partial('staffresourceform', array('staffresourceform' => $staffresourceform, 'ag_staff_resources' => $ag_staff_resources)) ?>