<h2><span class="highlightedText"><?php echo $eventName ?></span> Management</h2>


<p>In order for staff to be assigned to facilities they must first report as being available.
  Agasti will use the Staff Deployment Priority rules created during pre-deployment to generate the
  staff for this event and create an export, using the "Generate All Staff Contact List" button below,
  for staff to be contacted.</p>

<a href="<?php echo url_for('event/staffin'); ?>" class="linkButton" title="Generate All Staff Contact List as Export">Generate All Staff Contact List</a><br />


<p>After staff have replied to the external messaging system please import their replies.</p>
<a href="<?php echo url_for('event/staffin'); ?>" class="linkButton" title="Import Staff Replies">Import Staff Replies</a><br />

<p>Finally, after replies have been imported Agasti will assign available staff to facilities.
  Assigned staff will be messaged a second time with their shift assignment and where to report.</p>
<a href="<?php echo url_for('event/staffin'); ?>" class="linkButton" title="Generate Staff Assignment Export">Generate Staff Assignment Export</a><br/>


<a href="<?php echo url_for('event/shifts?id=' . $event_id); ?>" class="linkButton" title="View Shifts for Event">View Shifts for Event</a><br/>
<hr />

<a href="<?php echo url_for('event/active?id=' . $event_id); ?>" class="linkButton" title="Back to Event Dashboard">Back to Event Dashboard</a><br/>

<?php
//and a return to dashboard button.
?>

