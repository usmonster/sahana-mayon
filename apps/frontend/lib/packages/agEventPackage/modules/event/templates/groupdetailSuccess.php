<form action="<?php echo url_for('event/groupdetail?event=' . urlencode($eventFacilityGroup->getAgEvent()->event_name) . '&group=' . urlencode($eventFacilityGroup->event_facility_group)) . (isset($XmlHttpRequest) ? '" class="modalForm"' : ''); ?>" name="group_allocation_status" id="group_allocation_status" method="post">
  <label for="group_allocation_status" class="boldText labelGray" title="Change to update <?php echo $eventFacilityGroup->event_facility_group;?> status">Current Group Status:</label>
  <?php 
    $form->setDefault('group_allocation_status',$statusId);
    echo $form['group_allocation_status'];
  ?>
  <input type="hidden" name="event_facility_group_id" value="<?php echo $eventFacilityGroup->id;?>">
  <input type="submit" name="submit" value="Set Group Status" class="buttonWhite<?php echo (isset($xmlHttpRequest) ? ' modalSubmit" id="facilitygroups' : '');?>">
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
    <?php foreach($results as $key => $result) : ?>
    <tr>
      <td><?php echo $result['f_facility_code'] ?></td>
      <td><?php echo $result['f_facility_name'] ?></td>
      <td><?php echo $result['frt_facility_resource_type'] ?></td>
      <?php $form->setDefault('resource_allocation_status', $result['ras_id']) ?>
      <td>
        <form action="<?php echo url_for('event/groupdetail?event=' . urlencode($result['e_event_name']) . '&group=' . urlencode($result['efg_event_facility_group'])) ?>" name="<?php echo $key ?>_resource_allocation_status" id="<?php echo $key ?>_resource_allocation_status" method="post">
          <?php  echo $form['resource_allocation_status']; ?>
          <input type="hidden" id="event_facility_resource_id" name="event_facility_resource_id" value="<?php echo $result['efr_id'] ?>">
          <input type="hidden" id="facility_resource_id" name="facility_resource_id" value="<?php echo $result['fr_id']?>">
          <input type="submit" value="Set Status" name="submit" class="continueButton <?php echo (isset($xmlHttpRequest) ? ' modalSubmit" id="facilityresource/' . $result['efr_id'] : '') ?>">
        </form>
      </td>
    </tr>
    <?php endforeach ?>
  </tbody>
<?php //echo javascript_include_tag('agModal.js'); ?>
</table>
<?php if ($xmlHttpRequest == true): ?>
<div id="modalAppend"></div>
<?php endif; ?>