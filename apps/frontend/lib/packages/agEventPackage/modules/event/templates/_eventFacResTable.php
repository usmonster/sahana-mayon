<table style="width: 100%;" class="singleTable">
  <thead>
    <tr>
      <td>Facility Name</td>
      <td>Resource Type</td>
      <td>Facility Code</td>
      <td>Status</td>
      <td>Activation Time</td>
    </tr>
  </thead>
  <tbody>
  <?php foreach($facilityResourceArray as $facRes) : ?>
    <tr>
      <td><?php echo $facRes['f_facility_name'] ?></td>
      <td><?php echo $facRes['frt_facility_resource_type'] ?></td>
      <td><?php echo $facRes['f_facility_code'] ?></td>
      <td><a href=""class="textToForm linkText" name="resourceStatus"><?php echo $facRes['ras_facility_resource_allocation_status'] ?></a></td>
      <td><a href="" class="textToForm linkText" name="resourceActTime"><?php echo (isset($facRes['efrat_activation_time']) ? date('Y-m-d H:i:s', $facRes['efrat_activation_time']) : '----'); ?></a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
