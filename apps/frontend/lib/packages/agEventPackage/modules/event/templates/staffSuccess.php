<h2><span class="highlightedText"><?php echo $event_name ?></span> Staff Management</h2>

<div class="infoHolder">
<p>In order for staff to be deployed to locations, you need staff, get some by making some staff resource pools, or existing current ones</p>
<a href="<?php echo url_for('event/staffpool?event=' . $event_name); ?>" class="continueButton" title="View Staff Resource Pools for Event">Modify Staff Resource Pools for Event</a><br/>

<p>Then you want to make sure there are shifts for these people to work in:</p>
<a href="<?php echo url_for('event/shifts?event=' . $event_name); ?>" class="continueButton" title="View Shifts for Event">View Shifts for Event</a><br/>

<p>In order for staff to be assigned to facilities they must first report as being available.
  Agasti will use the Staff Deployment Priority rules created during pre-deployment to generate the
  staff for this event and create an export, using the "Generate All Staff Contact List" button below,
  for staff to be contacted.</p>

<?php
  $exportUrl = url_for('event/exportcontacts?event=' . urlencode($sf_data->getRaw('event_name'))) ;
  echo link_to('Export Staff Contact List', $exportUrl, array('class' => 'continueButton')); 
//all links on this page should use the link_to helper
  ?>
<br />


<p>After staff have replied to the external messaging system please import their replies.</p>
<?php
  $importUrl = url_for('event/importreplies?event=' . urlencode($sf_data->getRaw('event_name'))) ;
  echo link_to('Import Replies', $importUrl, array('class' => 'continueButton')); 
//all links on this page should use the link_to helper
  ?>



<p>Finally, after replies have been imported Agasti will assign available staff to facilities.
  Assigned staff will be messaged a second time with their shift assignment and where to report.</p>
<a href="<?php echo url_for('event/staffin'); ?>" class="continueButton" title="Generate Staff Assignment Export">Generate Staff Assignment Export</a><br/>

</div>

<hr class="ruleGray" />

<a href="<?php echo url_for('event/active?event=' . $event_name); ?>" class="continueButton" title="Back to Event Dashboard">Back to Event Dashboard</a><br/>

