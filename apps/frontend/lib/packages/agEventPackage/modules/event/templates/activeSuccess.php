<h2>Event Management: <span class="highlightedText"><?php echo $event_name ?></span></h2>
<?php
//You should probably add a confirmation that the event was deployed here when it's first created.
?>
<h3>Congratulations, Your Event Has Been Deployed</h3>
<br />
<?php
$urlEncodedEventName = urlencode($sf_data->getRaw('event_name'));
if (isset($blackOutFacilities)) {
?>

  <p><span class="highlightedText">Please Note!</span> the shifts at these facilities, in these facility groups have not been affected (their activation times have not been changed).</p>
  <a href="<?php echo url_for('event/fgroup?event=' . $urlEncodedEventName) ?>" class="continueButton" title="Facilities and Resources">Maually set Facility Resource Activation Times</a><br/>
<?php
  foreach ($blackOutFacilities as $facilityKey => $facility) {

  }
}
?>


<!--<a href="<?php echo url_for('event/meta?event=' . $urlEncodedEventName); ?>" class="continueButton" title="Change Event Metadata">Change Event Metadata</a><br/><br/>

<a href="<?php echo url_for('event/staff?event=' . $urlEncodedEventName); ?>" class="continueButton" class="continueButton" title="Manage Event Staff">Staff Management</a><br/>

<em><a href="<?php echo url_for('agClient/index'); ?>" class="buttonText" title="Manage Clients">Client Data</a></em><br/>

<a href="<?php echo url_for('event/listgroups?event=' . $urlEncodedEventName) ?>" class="continueButton" title="Facilities and Resources">Manage Facility Groups</a><br/>

<em><a href="<?php echo url_for('event/reporting'); ?>" class="buttonText" title="Manage Reports">Reporting</a></em><br/>
<br>-->


<br/>
<table class="blueTable">
  <tr class="head">
    <th class="row1">Actions</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>
      <a class="buttonText" href="<?php echo url_for('event/meta?event=' . $urlEncodedEventName); ?>">Event Name and Zero Hour</a>
    </td>
    <td>Name: <span class="highlightedText"><?php echo $event_name ?></span>
      <br />Description: <span class="highlightedText"><?php echo $event_description ?></span>
    </td>
  </tr>
  <tr>
    <td>
      <a class="buttonText" href="<?php echo url_for('event/staff?event=' . $urlEncodedEventName); ?>">Event Staff Management</a>
    </td>
    <td>
      Total Staff: <span class="highlightedText"><?php echo $eventStaffPool ?></span>
    </td>
  </tr>
  <tr>
    <td>
      <a class="buttonText" href="<?php echo url_for('event/messaging?event=' . $urlEncodedEventName); ?>">Event Staff Messaging</a>
    </td>
    <td>
      Total Staff: <span class="highlightedText"><?php echo $eventStaffPool ?></span><br /> 
      Confirmed Staff: <span class="highlightedText"><?php echo $eventAvailableStaff ?></span>
    </td>
  </tr>
  <tr>
    <td>
      <a class="buttonText" href="<?php echo url_for('event/listgroups?event=' . $urlEncodedEventName); ?>">Event Facility Management</a>
    </td>
    <td>
      <div style="font-style:oblique">No statistical data is currently available.</div>
    </td>
  </tr>
  <tr>
    <td>
      <a class="buttonText" href="#">Client Information</a>
    </td>
    <td>
      <div style="font-style:oblique">No statistical data is currently available.</div>
    </td>
  </tr>
  <tr>
    <td>
      <a class="buttonText" href="#">Reporting</a>
    </td>
    <td>
      <div style="font-style:oblique">No statistical data is currently available.</div>
    </td>
  </tr>
</table>
<br />
<br />






<a href="<?php echo url_for('event/resolution?event=' . $urlEncodedEventName); ?>" class="continueButton" title="Resolve Event">Resolve Event</a><br/>

<br />