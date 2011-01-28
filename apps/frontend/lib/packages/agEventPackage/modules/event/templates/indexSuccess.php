<?php
// This page is currently a stub!  The following random string is a marker for the stub.
// PnaODfcm3Kiz4MV4vzbtr4
// PLEASE REMOVE THIS COMMENT BLOCK WHEN YOU DEVELOP THIS PAGE!
?>

<h2>Event Management</h2>
<p>'Event' refers to a specific activation of the plans made in the Scenario module.  The entire term
  of the response will be referred to as an individual event, which is named in the next step.
  Before activating you will have the opportunity to customize your settings and staff pool in 'Pre-Deployment'.
  You can remain in the pre-deployment steps as long as necessary until the event is 'live'; at which
  point facilities will be activated, shifts created, and staff and volunteers are contacted to report
  and begin response.</p>

<h3>Event Pre-Deployment</h3>
<b>Please select a scenario to base your event on:</b><br/>

<?php include_partial('scenarioForm', array('scenarioForm' => $scenarioForm)) ?>