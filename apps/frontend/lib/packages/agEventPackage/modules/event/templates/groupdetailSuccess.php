<form action="<?php echo url_for('event/groupdetail?event=' . urlencode($eventFacilityGroup->getAgEvent()->event_name) . '&group=' . urlencode($eventFacilityGroup->event_facility_group)) . (isset($XmlHttpRequest) ? '" class="modalForm"' : ''); ?>" name="group_allocation_status" id="group_allocation_status" method="post">
  <label for="group_allocation_status" class="boldText labelGray" title="Change to update <?php echo $eventFacilityGroup->event_facility_group;?> status">Current Group Status:</label>
  <?php 
    $form->setDefault('group_allocation_status',$statusId);
    echo $form['group_allocation_status'];
  ?>
  <input type="hidden" name="event_facility_group_id" value="<?php echo $eventFacilityGroup->id;?>">
  <input type="submit" name="submit" value="Set Group Status" class="buttonWhite<?php echo (isset($XmlHttpRequest) ? ' modalSubmit' : '');?>">
</form>

<table class="singleTable" id="dialog">
  <tbody>
    <tr>
      <th class="head" colspan="4">
        <?php echo ucwords($eventFacilityGroup->getAgFacilityGroupType()->facility_group_type) . ' '; ?><span class="highlightedText"><?php echo $eventFacilityGroup->event_facility_group; ?></span>
      </th>
    </tr>
    <tr>
      <th class="subHead">Code</th>
      <th class="subHead">Name</th>
      <th class="subHead">Type</th>
      <th class="subHead">Status</th>
    </tr>
    <?php
      foreach($results as $key => $result) {
        echo '<tr>' . PHP_EOL;
        echo '<td>' . $result['fr_facility_resource_code'] . '</td>';
        echo '<td>' . $result['f_facility_name'] . '</td>';
        echo '<td>' . $result['frt_facility_resource_type'] . '</td>';
        $form->setDefault('resource_allocation_status', $result['ras_id']);
//        echo '<td><form action="' . url_for('event/groupdetail?event=' . urlencode($result['e_event_name']) . '&group=' . urlencode($result['efg_event_facility_group'])) . '" name="' . urlencode(strtolower(str_replace(' ', '_', $result['f_facility_name'] . ' ' . $result['frt_facility_resource_type']))). ' id="' . $key. '_resource_allocation_status" method="post">';
        echo '<td><form action="' . url_for('event/groupdetail?event=' . urlencode($result['e_event_name']) . '&group=' . urlencode($result['efg_event_facility_group'])) . '" name="' . $key. '_resource_allocation_status" id="' . $key. '_resource_allocation_status" method="post">';
        echo $form['resource_allocation_status']; //set the form value to $result['ras_id'] $result['ras_facility_resource_allocation_status'] . '</td>
        echo '<input type="hidden" id="event_facility_resource_id" name="event_facility_resource_id" value="' . $result['efr_id'] . '">';
        echo '<input type="hidden" id="facility_resource_code" name="facility_resource_code" value="' . $result['fr_facility_resource_code'] . '">';
        echo '<input type="submit" value="Set Status" name="submit" class="linkButton' . (isset($XmlHttpRequest) ? ' modalSubmit' : '') . '"></form></td>';
        echo '</tr>';
      }
    ?>
  </tbody>
<?php //echo javascript_include_tag('agModal.js'); ?>
</table>