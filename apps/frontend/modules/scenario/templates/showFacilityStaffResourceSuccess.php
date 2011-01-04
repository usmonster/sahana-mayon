<h3>The following staff requirements have been set for the the facility groups in the <span style="color: #ff8f00"><?php echo $scenario->scenario ?></span> scenario:</h3>
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