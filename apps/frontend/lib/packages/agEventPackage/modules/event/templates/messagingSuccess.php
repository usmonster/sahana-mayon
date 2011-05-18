<h2>Staff Messaging: <span class="highlightedText"><?php echo $event_name; ?> </span></h2>
<p>Use this page to send messages and receive responses to your staff for <span class="highlightedText"><?php echo $event_name; ?></span>.</p>
<?php
  $exportUrl = url_for('event/exportcontacts?event=' . urlencode($sf_data->getRaw('event_name'))) ;
  echo link_to('Export Staff Contact List', $exportUrl, array('class' => 'generalButton')); 
  //on click of this button, set the div content to 'exporting data, please wait'
if(isset($exportComplete)){
  echo $exportComplete . ' staff records exported, please send this file to your messaging vendor.';
}
  ?>
<span class="infoText" style="padding-left: 10px;">status of export here</span>
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