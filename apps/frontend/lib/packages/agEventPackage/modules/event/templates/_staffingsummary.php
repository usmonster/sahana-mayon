<?php
use_javascript('jquery-1-test.js');
use_javascript('jquery-test.js');
use_javascript('menu-test.js');
use_stylesheet('style-test.css');
?>

<?php
$results = $sf_data->getRaw('results');
if (!empty($results)): // print_r($results);
?>

  The following are shift staffing results for shifts that are in-progress at <?php echo date('Y-m-d H:i:s T', $reportTime) ?>.
  <br /><br />
  <a name="top"></a>
  <div><a href="#" class="expandall">Expand All</a> | <a href="#" class="collapseall">Collapse All</a></div><br>
  <ul id="menu1" class="menu_summary noaccordion" style="list-style-type: none">
  <?php foreach ($results as $efg_id => $groupinfo): ?>
    <li style="background-color:#ccc;  border:1px solid #ccc;; margin-bottom:13px; max-width:950px;"><a class="<?php echo "m" . $efg_id; ?>"><div style="display:inline-block;padding:10px 10px 5px; font-size:17px; color:#fff; font-weight:bold "> Facility Group: <?php echo $groupinfo['group_name']; ?></div>
        <div style="display:inline-block;float:right; padding:10px 10px 5px; font-size:17px; color:#fff; font-weight:bold ">Status: <?php echo $groupinfo['group_status']; ?></div></a>
      <ul style="display:none; list-style-type: none">

      <?php foreach ($groupinfo['facilities'] as $efr_id => $facilityinfo): ?>
        <li><div style="background-color:#d8d8d8; display:inline-block; margin:3px 10px;padding:5px;width:auto; border:1px solid #999; ">
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
                  </div></li>
      <?php endforeach; ?>
                    </ul>
                    <div class="grp_sumry" style="background-color:#fff;  margin:3px 10px 10px;padding:5px; border: 1px solid #999"> <!--Second -->
      <?php $restypeinfo = $groupinfo['staff_totals']['resource_types']; ?>
                      <table  cellpadding="5" class="sumry-tbl-info">
                        <tr><th>Resource Type</th><th>Staff Count</th><th>Min / Max</th></tr>
        <?php foreach ($restypeinfo as $staff_type_val => $rescinfo): ?>
                        <tr class="info"><td><?php echo $staff_type_val; ?></td><td><?php echo $rescinfo['staff_count']; ?></td><td><?php echo $rescinfo['minimum_staff'] . "/" . $rescinfo['maximum_staff']; ?></td>
                        </tr>
        <?php endforeach; ?>
                      </table>
                      <table  cellpadding="10" style="width:auto;  display:inline-block;border:1px solid #d8d8d8; margin: 10px; ">
                        <tr style="background-color:#fff; padding:5px; color:#999;  font-weight: bold"><td style="background-color:#999;color:#fff;padding:10px ;">Totals</td> <td>Staff Count:</td><td style="border-right: 1px solid #ccc;"><?php echo $groupinfo['staff_totals']['staff_count'] ?></td><td>Min/Max:</td><td><?php echo $groupinfo['staff_totals']['minimum_staff'] . "/" . $groupinfo['staff_totals']['maximum_staff']; ?></td></tr>

                      </table>
                    </div>
                  </li>
  <?php endforeach; ?>
                      </ul>
                      <br><div><a href="#" class="expandall">Expand All</a> | <a href="#" class="collapseall">Collapse All</a></div><br><br><br>

<?php elseif (is_null($reportTime)): ?>
<?php else: ?>
                            There is no shift staffing data available for this event at <?php echo date('Y-m-d H:i:s T', $reportTime) ?>.
<?php
                            endif;
//print_r($sf_data->getRaw('results'));
?>

