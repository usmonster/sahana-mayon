<?php (!$eventshiftform->isNew()) ? $action = 'Edit' :$action = 'Create New'; ?>
<h3><?php echo $action; ?> Event Shift</h3>

<?php include_partial('eventshiftform', array('eventshiftform' => $eventshiftform, 'event_id' => $event_id, 'eventName' => $eventName)) ?>
