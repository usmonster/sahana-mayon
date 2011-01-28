<h2>*Name of Event* Management</h2>
<?php
//note for devs: anytime you see *Name Of Event* it's a placeholder for where you should make
//the app display the name of the event the user is working in.
//
// This page is currently a stub!  The following random string is a marker for the stub.
// PnaODfcm3Kiz4MV4vzbtr4
// PLEASE REMOVE THIS COMMENT BLOCK WHEN YOU DEVELOP THIS PAGE!
//
//You should probably add a confirmation that the event was deployed here when it's first created.
?>

<p>The links below should really be buttons so they look nice:</p>
<a href="<?php echo url_for('event/staff'); ?>" class="buttonText" title="Manage Event Staff">Staff Management</a><br/>
<a href="<?php echo url_for('agClient/index'); ?>" class="buttonText" title="Manage Clients">Client Data</a><br/>
<a href="<?php echo url_for('event/fgroup'); ?>" class="buttonText" title="Facilities and Resources">Manage Facility Groups</a><br/>
<a href="<?php echo url_for('event/reporting'); ?>" class="buttonText" title="Manage Reports">Reporting</a><br/>
<br>
<a href="<?php echo url_for('event/resolution'); ?>" class="buttonText" title="Close Event">Resolve Event</a><br/>

<br />