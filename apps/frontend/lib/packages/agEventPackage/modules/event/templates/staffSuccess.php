<?php
use_javascript('agasti.js');
use_javascript('agastiMessaging.js');
use_javascript('jQuery.fileinput.js');
use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
?>


<h2>Staff Management: <span class="highlightedText"><?php echo $event_name ?></span></h2>
<br/>
<h4>View Shift Staffing Summary for the  <span class="highlightedText"><?php echo $event_name; ?></span> event.</h4>

<br />
<?php echo "Current Event Status: <span class=\"highlightedText\">". $current_event_status . "</span><br />Zero Hour: <span class=\"highlightedText\">". $event_zero_hour_str . "</span>"?>
<br/>
<br/>

Please input a valid date/time to view your post-deployment staffing summary for the specified point in time. Any valid date/time string may be used (eg, '2015-01-01 13:00' or 'January 1, 2015 1:00 pm').

<br /><br />

<form action="<?php echo url_for('event/staff?event=' . $event_name) ?>" method="post" <?php $subForm->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php 
  echo $subForm;
?>
  <input class="continueButton" type="submit" value="Get Report" name="Retrieve Report"/>
</form>
<br />

<?php include_partial('staffingsummary', array('results' => $results, 'reportTime' => $reportTime)); ?>

<br />

<?php

$importUrl = url_for('event/active?event=' . urlencode($sf_data->getRaw('event_name')));
echo link_to($event_name . ' Event Management', $importUrl,
    array('class' => 'generalButton', 'title' => $event_name . ' Event Management'));
?>
