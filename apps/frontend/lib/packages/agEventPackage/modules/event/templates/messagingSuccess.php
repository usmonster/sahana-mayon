<h2>Staff Messaging: <span class="highlightedText"><?php echo $event_name; ?> </span></h2>
<p>Use this page to send messages and receive responses to your staff for <span class="highlightedText"><?php echo $event_name; ?></span>.</p>
<?php
  $exportUrl = url_for('event/exportcontacts?event=' . urlencode($sf_data->getRaw('event_name'))) ;
  echo link_to('Export Staff Contact List', $exportUrl, array('class' => 'generalButton')); 
?>
<br />
<br />
<a href="#" class="generalButton">Import Staff Responses</a>
<br />
<br />
<a href="#" class="generalButton">Preview Confirmed Staff Pool</a>
<br />
<br />
<?php
  $deployUrl = url_for('event/deploystaff?event=' . urlencode($sf_data->getRaw('event_name'))) ;
  echo link_to('Deploy Staff to Facilities', $deployUrl, array('class' => 'generalButton')); 
?>