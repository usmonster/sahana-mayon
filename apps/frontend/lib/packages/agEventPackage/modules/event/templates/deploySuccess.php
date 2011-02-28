<?php
// This page is currently a stub!  The following random string is a marker for the stub.
?>
<h2><b><span class="highlightedText"><?php echo $eventName; ?></span></b> Pre-Deployment</h2>
<?php
//note for devs: anytime you see <b>Name Of Event</b> it's my placeholder for where you should make
//the app display the name of the event the user is working in.
?>
<p>The final steps in preparation are often the most critical.  
  In the final steps of pre-deployment, you'll ensure all geographic reference information for
  your staff and facility resources are up to date, and that the shifts for <span class="highlightedText"><?php echo $eventName; ?></span> event are
  up to date.</p>

<?php
$checkResults = $sf_data->getRaw('checkResults');
if (isset($checkResults)) {
  foreach ($checkResults as $label => $checkResult)
  {
    echo '<em>' . $label . '</em>: ';
    if (is_array($checkResult))
    {
      echo implode(', ', $checkResult);
    } else {
      echo $checkResult;
    }
      echo '<br />';
  }
}
?>
<br />
<form action="<?php echo url_for('event/deploy?id=' . $event_id) ?> " method="post">

<a href="<?php echo url_for('event/gis'); ?>" class="linkButton" title="Update GIS Data">Generate Geo Information</a>

<a href="#" class="linkButton" title="Generate Shifts">Generate Shifts</a>

<?php
//We should have some warnings here in case you're missing something.
?>
<a href="#" class="linkButton" title="Save">Save</a>

<input type="submit" value="Save and Deploy" class="linkButton"/>

</form>