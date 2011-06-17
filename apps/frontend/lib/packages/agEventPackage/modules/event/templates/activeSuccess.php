<h2>Event Management: <span class="highlightedText"><?php echo $event_name ?></span></h2>
<?php
//You should probably add a confirmation that the event was deployed here when it's first created.
?>

<br />
<?php echo "Current Event Status: <span class=\"highlightedText\">".$upper_case_cur_Status."</span><br />Zero Hour: <span class=\"highlightedText\">". $zero_hour. "</span>"?>
<br/>
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


<br/>
<table class="blueTable">
    <tr class="head">
        <th class="row1">Actions</th>
        <th>Description</th>
    </tr>

    <?php if ($upper_case_cur_Status == "PRE-DEPLOYMENT"): ?>
    <tr>
        <td>
        <a class="buttonText" href="<?php echo url_for('event/meta?event=' . $urlEncodedEventName); ?>">Event Name and Zero Hour</a>
        </td>
        <td>Name: <span class="highlightedText"><?php echo $event_name
?></span>

            </td>
          </tr>
            <?php endif ?>
  

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
            <a class="buttonText" href="<?php echo url_for('event/staff?event=' . $urlEncodedEventName); ?>">Event Staff Management</a>
        </td>
        <td>
            Total Staff: <span class="highlightedText"><?php echo $eventStaffPool ?></span>
        </td>
    </tr>
    <tr>
        <td>
            <a class="buttonText" href="<?php echo url_for('event/listgroups?event=' . $urlEncodedEventName); ?>">Event Facility Management</a>
        </td>
        <td>
            Facility Groups: <span class="highlightedText"><?php echo $event_facility_groups ?></span><br />
            Facility Resources: <span class="highlightedText"><?php echo $event_facility_resources ?></span>
        </td>
    </tr>
    <tr>
        <td>
            <a class="buttonText" href="<?php echo $vesuvius_address; ?>" target="_blank">Client Information</a>
        </td>
        <td>
            <div>Click the Client Information link to jump to the Vesuvius client information
              tracking system.</div>
        </td>
    </tr>
    <tr>
        <td>
            <a class="buttonText" href="#">Reporting</a>
        </td>
        <td>
            <div style="font-style:oblique">No information currently available .</div>
        </td>
    </tr>
</table>
<br />
<br />






<a href="<?php echo url_for('event/resolution?event=' . $urlEncodedEventName); ?>" class="continueButton" title="Resolve Event">Resolve Event</a><br/>

<br />