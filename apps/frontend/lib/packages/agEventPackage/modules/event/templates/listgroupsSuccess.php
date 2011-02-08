<h2><?php if(isset($event)) {echo '<span style="color: #ff8f00">' . $event->event_name . ' </span>';} ?> Facilities Management</h2>
<?php
//and a return to dashboard button.
$a = $facilityGroupArray;
?>
<br />
<h3>Facility and Facility Group listing <?php echo ((isset($event)) ? 'for the <span style="color: #ff8f00">' . $event->event_name . '</span> Event' : 'for all Events'); ?></h3>

<table class="singleTable">
  <thead>
    <tr>
      <th class="head">Facility Name & Resource Type</th>
      <th class="head">Facility Code</th>
      <th class="head">Facility Status</th>
      <th class="head">Facility Activation Time</th>
      <th class="head">Facility Group</th>
      <th class="head">Facility Group Type</th>
      <?php
        if(!(isset($event))) {
          echo '<th class="head">Event</th>';
        }
      ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach($facilityGroupArray as $facilityGroup): ?>
      <?php foreach ($facilityGroup as $facility): ?>
      <tr>
        <td><?php echo $facility['f_facility_name'] . ": " . $facility['frt_facility_resource_type']; ?></td>
        <td><?php echo $facility['f_facility_code']; ?></td>
        <td><?php echo $facility['ras_facility_resource_allocation_status']; ?></td>
        <td><?php
            if(isset($facility['efrat_activation_time'])) {
              $timeSplit = explode(' ', $facility['efrat_activation_time']);
              echo $timeSplit[0];
            } else {
              echo '----';
            }
        ?></td>
        <td><a href="<?php echo url_for('event/groupdetail?event=' . urlencode($facility['e_event_name']) . '&group=' . urlencode($facility['efg_event_facility_group'])) ?>" class="linkMail" name="modal" title="Facility Group <?php echo $facility['efg_event_facility_group']; ?> for the <?php echo $facility['e_event_name']; ?> Scenario"><?php echo $facility['efg_event_facility_group'] ?></a></td>
        <td><?php echo $facility['fgt_facility_group_type'] ?></td>
        <?php
          if(!(isset($event))) { echo '<td>' . $facility['e_event_name'] . '</td>'; } ?>
      </tr>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </tbody>
</table>

<a href="<?php echo url_for('event/newgroup') ?>" class="buttonText" title="New Facility Group">New</a>