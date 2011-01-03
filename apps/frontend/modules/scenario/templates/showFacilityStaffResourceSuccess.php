<p>You have assigned the following staff requirements to the facilities in your facility groups:</p>
<table class="singleTable">
  <thead>
      <caption><?php echo $facilityGroup->scenario_facility_group; ?></caption>
  </thead>
  <tbody>
    <?php foreach ($facilityResources as $facilityResource): ?>
    <tr>
      <th class="head" colspan="<?php echo (count($facilityResource->getAgFacilityStaffResource()) * 3);?>"><?php echo $facilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($facilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type); ?></th>
    </tr>
    <tr>
      <?php foreach ($facilityResource->getAgFacilityStaffResource() as $key => $staffResourceType): ?>
      <th class="<?php echo (($key == 0) ? 'subHeadLeft' : 'subHeadMid'); ?>"><?php echo ucwords($staffResourceType->getAgStaffResourceType()->staff_resource_type); ?></th>
      <td>Min: <?php echo $staffResourceType->minimum_staff; ?></td>
      <td>Max: <?php echo $staffResourceType->maximum_staff; ?></td>
      <?php endforeach; ?>
    </tr>
    <?php  endforeach; ?>
  </tbody>
</table>