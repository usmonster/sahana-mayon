<?php if ($xmlHttpRequest == true) : ?>
  <p>The activation status for <span class="highlightedText"><?php echo $facility_resource->getAgFacility()->facility_name . ' : ' . $facility_resource->getAgFacilityResourceType()->facility_resource_type ?></span> requires an activation time.</p>
  <p>Please use the form below to set the activation time.</p>
<?php  endif; ?>
<?php echo $facilityResourceActivationTimeForm; ?>
