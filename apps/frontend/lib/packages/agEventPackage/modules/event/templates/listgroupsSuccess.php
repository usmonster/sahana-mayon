<h2><?php if(isset($event)) {echo '<span style="color: #ff8f00">' . $event->event_name . ' </span>';} ?> Facilities Management</h2>
<?php
//note for devs: anytime you see *Name Of Event* it's my placeholder for where you should make
//the app display the name of the event the user is working in.
//
// This page is currently a stub!  The following random string is a marker for the stub.
// PnaODfcm3Kiz4MV4vzbtr4
// PLEASE REMOVE THIS COMMENT BLOCK WHEN YOU DEVELOP THIS PAGE!
?>

<h3>Select Facility Group</h3>
<p>*Here will be a list of the facility groups with reports on the status of the group,
  staff count, client count, and totals.  It's been floated that the percentage of the totals
  should also be included.  Clicking the group name brings you to fgroupdetailSuccess.</p>

<?php
$a = urlencode($event->event_name);
$b = urlencode('Floo&*$$#Spork');
echo $a;
echo $b;
//and a return to dashboard button.
?>

<h3>Facility Group Listing <?php if(isset($event)) {echo 'for the <span style="color: #ff8f00">' . $event->event_name . '</span> Event';} ?></h3>

<table>
  <thead>
    <tr>
      <th>Facility Group</th>
      <th>Facility Group Type</th>
      <th>Activation Sequence</th>
      <th>Facility Resource Count</th>
      <th>Staff</th>
      <th>Clients</th>
      <th>Total</th>
      <th>Allocation Status</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_event_facility_groups as $ag_event_facility_group): ?>
    <tr>
      <td><a href="<?php echo url_for('event/groupdetail?eid=' . $event->id . '&id=' . $ag_event_facility_group->getId()) ?>"><?php echo $ag_event_facility_group->getEventFacilityGroup() ?></a></td>
      <td><?php echo $ag_event_facility_group->getAgFacilityGroupType() ?></td>
      <td><?php echo 'lala';//$ag_event_facility_group->getAgFacilityGroupAllocationStatus() ?></td>
      <td><?php echo $ag_event_facility_group->getActivationSequence() ?></td>
      <td><?php echo count($ag_event_facility_group->getAgFacilityResource()) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<a href="<?php echo url_for('event/newgroup') ?>" class="buttonText" title="New Facility Group">New</a>
