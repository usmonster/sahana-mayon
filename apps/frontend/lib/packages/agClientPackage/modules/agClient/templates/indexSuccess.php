<?php use_javascript('agasti.js') ?>
<?php use_javascript('jQuery.fileinput.js') ?>
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
<h2> *Event Name* Client Management </h2>
<p>During an emergency response staff and volunteers working within the shelter system will use
  the client functionality of Agasti to manage client's sensitive information.</p>

Please select one of the following staff administration actions:<br />

<a href="<?php echo url_for('agClient/in'); ?>" class="buttonText" title="Check In Client">Check In Client</a><br/>
<a href="<?php echo url_for('agClient/out'); ?>" class="buttonText" title="Check Out Clients">Check Out Clients</a><br/>
<a href="<?php echo url_for('agClient/list'); ?>" class="buttonText" title="List Existing Clients">List Client Records</a><br/>

If you would like to search for clients, please use the search box on the top right.