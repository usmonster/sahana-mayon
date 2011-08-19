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

Please input a valid data/time to view your staff summary.

<br /><br />

<form action="<?php echo url_for('event/staff?event=' . $event_name) ?>" method="post" <?php $subForm->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php 
  echo $subForm;
?>
  <input class="continueButton" type="submit" value="Report Time" name="Retrieve Report"/>
</form>
<br />

<?php include_partial('staffingsummary', array('results' => $results)); ?>

<br />

<?php

$importUrl = url_for('event/active?event=' . urlencode($sf_data->getRaw('event_name')));
echo link_to($event_name . ' Event Management', $importUrl,
    array('class' => 'generalButton', 'title' => $event_name . ' Event Management'));
?>
