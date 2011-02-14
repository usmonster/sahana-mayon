<table class="singleTable" id="dialog">
  <tbody>
    <tr>
      <th class="head" colspan="4"><?php echo ucwords($eventFacilityGroup->getAgFacilityGroupType()->facility_group_type) . ' '; ?><span style="color: #ff8f00"><?php echo $eventFacilityGroup->event_facility_group; ?></span> Facilities</th>
    </tr>
    <tr>
      <th class="subHead">Code</th>
      <th class="subHead">Name</th>
      <th class="subHead">Type</th>
      <th class="subHead">Status</th>
    </tr>
    <?php
      foreach($results as $result) {
        echo '<tr>' . PHP_EOL;
        echo '<td>' . $result['f_facility_code'] . '</td>';
        echo '<td>' . $result['f_facility_name'] . '</td>';
        echo '<td>' . $result['frt_facility_resource_type'] . '</td>';
        echo '<td>' . $result['ras_facility_resource_allocation_status'] . '</td>';
        echo '</tr>';
      }
    ?>
  </tbody>
</table>

