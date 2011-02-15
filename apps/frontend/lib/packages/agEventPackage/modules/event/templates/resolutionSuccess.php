<h2><span class="highlightedText"><?php echo $eventName ?></span> Resolution</h2>

<p>In order to resolve an event all associated facilities must be closed.</p>
<p>There are <b>Number</b> active facility groups.  Please close these facilities before ending
  the event.</p>

<a href="<?php echo url_for('event/active?id=' . $event_id); ?>" class="linkButton">Cancel</a>

<?php include_partial('resForm', array('resForm' => $resForm, 'event_id' => $event_id, 'eventName' => $eventName)) ?>
