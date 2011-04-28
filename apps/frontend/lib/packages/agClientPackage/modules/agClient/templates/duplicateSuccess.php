<?php
//note for devs: anytime you see *Name Of Event* it's my placeholder
//for where you should make the app display the name of the event
//the user is working in.
//
// This page is currently a stub!  The following random string is
// a marker for the stub.
// PnaODfcm3Kiz4MV4vzbtr4
// PLEASE REMOVE THIS COMMENT BLOCK WHEN YOU DEVELOP THIS PAGE!
?>

<h2> *Event Name* Client Management </h2>

<h3>Duplication Verification</h3>

<p>The client information you entered may be a duplicate record of the 
  information already existing in Agasti.  Please check the information
  below and confirm the duplicate:

  <?php // include_partial('form', array('form' => $form)) ?>

<p>The new client information and the possible dupe should be displayed 
  side-by-side with the corresponding buttons below them:</p>
<strong>Keep New Client (update old record)</strong><br>
<strong>Keep Found Client (discard new record)</strong><br>
<strong>Keep Both (the record is not a duplicate)</strong>