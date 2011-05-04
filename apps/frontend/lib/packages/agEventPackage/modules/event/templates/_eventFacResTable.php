<table class="expandableTable">
  <thead>
    <tr>
      <th>Facility Code</th>
      <th>Resource Type</th>
      <th>Status</th>
      <th>Activation Time</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($facilityResourceArray as $facRes) : ?>
    <tr>
      <td title="<?php echo $facRes['f_facility_name']; ?>"><?php echo $facRes['f_facility_code'] ?></td>
      <td><?php echo $facRes['frt_facility_resource_type'] ?></td>
      <td class="set100"><a href="<?php echo url_for('event/eventfacilityresource?eventFacilityResourceId='. $facRes['efr_id']); ?>"class="textToForm linkText" name="resourceStatus" id="res_stat_id_<?php echo $facRes['efr_id']; ?>"><?php echo $facRes['ras_facility_resource_allocation_status'] ?></a></td>
      <td class="set110"><a href="<?php echo url_for('event/eventfacilityresource?eventFacilityResourceId='. $facRes['efr_id']); ?>" class="textToForm linkText" name="resourceActivationTime" id="res_act_id_<?php echo $facRes['efr_id']; ?>"><?php echo (isset($facRes['efrat_activation_time']) ? date('m/d/Y H:i', $facRes['efrat_activation_time']) : '----'); ?></a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
