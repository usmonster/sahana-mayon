<?php
  $results = $sf_data->getRaw('results');
  if (!empty($results)): // print_r($results);
?>

The following are shift staffing results for shifts that are in-progress at <?php
echo date('Y-m-d H:i:s T', $reportTime) ?>.
<br /><br />

<?php foreach ($results as $efg_id => $groupinfo): ?>
      <div style="background-color:#ccc; display:inline-block; border:1px solid #ccc">
        <div style="display:inline-block;padding:10px 10px 5px; font-size:17px; color:#fff; font-weight:bold "> Facility Group: <?php echo $groupinfo['group_name']; ?></div>
        <div style="display:inline-block;float:right; padding:10px 10px 5px; font-size:17px; color:#fff; font-weight:bold ">Status: <?php echo $groupinfo['group_status']; ?></div>
        <br>
  <?php foreach ($groupinfo['facilities'] as $efr_id => $facilityinfo): ?>
        <div style="background-color:#d8d8d8; display:inline-block; margin:3px 10px;padding:5px;width:auto; border:1px solid #999; ">
          <div style="display:inline-block; text-align:left;padding:5px 20px 5px 5px;">Facility: <?php echo $facilityinfo['facility_type'] ?>-<?php echo $facilityinfo['facility_code'] ?></div><div style="display:inline-block;padding:5px 20px 5px 10px; ">Facility Name: <?php echo $facilityinfo['facility_name'] ?></div><div style="display:inline-block;padding:5px; float:right ">Facility Status: <?php echo $facilityinfo['facility_status']; ?></div>
          <br>
          <table class="blueTable" cellpadding="5" style="width:auto; border: 1px solid #dadada">
            <tr class="head"><th>Staff Type</th><th>Staff Count</th><th>Min / Max</th><th>Shift Status</th><th>Staff Wave</th><th>Shift Start</th><th>Break Start</th><th>Shift End</th><th>Time Zone</th></tr>
      <?php foreach ($facilityinfo['shifts'] as $es_id => $shiftinfo): ?>
      <?php if ($shiftinfo['shift_disabled'] || $shiftinfo['shift_standby']): ?>
            <tr style="background-color:#EEE; padding:5px; border:1px solid #dadada">
        <?php else: ?>
            <tr style="background-color:#fff; padding:5px; border:1px solid #dadada">
        <?php endif; ?>
              <td><?php echo $shiftinfo['staff_type']; ?></td>
              <td>
          <?php if ($shiftinfo['shift_disabled']): ?>
                <span style="color: #999; border:1px solid #dadada">
            <?php else: ?>
            <?php
                  if ($shiftinfo['staff_count'] == $shiftinfo['maximum_staff']) {
                    echo "<span style=\"font-weight:bold; color:green\">";
                  } elseif (($shiftinfo['staff_count'] >= $shiftinfo['minimum_staff']) && $shiftinfo['staff_count'] < $shiftinfo['maximum_staff']) {
                    echo "<span style=\"font-weight:bold; color:green\">";
                  } elseif ($shiftinfo['staff_count'] < $shiftinfo['minimum_staff']) {
                    echo "<span style=\"font-weight:bold; color:red\">";
                  }
                  echo $shiftinfo['staff_count']; ?>
            <?php endif; ?>
                </span>
              </td><td><?php echo $shiftinfo['minimum_staff']; ?>/<?php echo $shiftinfo['maximum_staff']; ?></td><td><?php echo $shiftinfo['shift_status']; ?></td><td>
          <?php echo $shiftinfo['staff_wave']; ?></td><td><?php echo $shiftinfo['shift_start']; ?></td><td><?php echo $shiftinfo['break_start']; ?></td><td><?php echo $shiftinfo['shift_end']; ?></td><td><?php echo $shiftinfo['timezone']; ?></td>
              </tr>
      <?php endforeach; ?>
                </table>
              </div><br> 
  <?php endforeach; ?>
                </div><br><br><br>
<?php endforeach; ?>
<?php elseif (is_null($reportTime)): ?>
<?php else: ?>
There is no shift staffing data available for this event at <?php
echo date('Y-m-d H:m:s T', $reportTime) ?>.
<?php
                  endif;
//print_r($sf_data->getRaw('results'));
?>
