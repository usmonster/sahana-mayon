<?php
// This page is currently a stub!  The following random string is a marker for the stub.
// PnaODfcm3Kiz4MV4vzbtr4
// PLEASE REMOVE THIS COMMENT BLOCK WHEN YOU DEVELOP THIS PAGE!
?>
<h2><b><span style="color: #ff8f00"><?php echo $eventName; ?></span></b> Pre-Deployment</h2>
<?php
//note for devs: anytime you see <b>Name Of Event</b> it's my placeholder for where you should make
//the app display the name of the event the user is working in.
?>
<p>The final steps in preparation are often the most critical.  
  In the final steps of pre-deployment, you'll ensure all geographic reference information for
  your staff and facility resources are up to date, and that the shifts for <span style="color: #ff8f00"><?php echo $eventName; ?></span> event are
  up to date.</p>

<a href="<?php echo url_for('event/gis'); ?>" class="buttonText" title="Update GIS Data">Generate Geo Information</a><br/>

<b>Button for Shift generation.</b><br>

<b>Warnings in case something is missing.</b><br>

<b>Button to save w/o deploying. </b><br>

<b>Button to deploy The Event.</b>