<h2><span class="highlightedText"><?php echo $eventName ?></span> Management</h2>
<?php
//You should probably add a confirmation that the event was deployed here when it's first created.
?>
<h3>Congratulations, your event has been deployed</h3>


<p>The links below should really be buttons so they look nice:</p>

<a href="<?php echo url_for('event/staff?id=' . $event_id); ?>" class="linkButton" class="linkButton" title="Manage Event Staff">Staff Management</a><br/>

<em><a href="<?php echo url_for('agClient/index'); ?>" class="buttonText" title="Manage Clients">Client Data</a></em><br/>

<a href="<?php echo url_for('event/listgroups?event=' . urlencode($eventName)) ?>" class="linkButton" title="Facilities and Resources">Manage Facility Groups</a><br/>

<a href="<?php echo url_for('event/reporting'); ?>" class="buttonText" title="Manage Reports">Reporting</a><br/>
<br>
<a href="<?php echo url_for('event/resolution?id=' . $event_id); ?>" class="linkButton" title="Close Event">Resolve Event</a><br/>

<br />