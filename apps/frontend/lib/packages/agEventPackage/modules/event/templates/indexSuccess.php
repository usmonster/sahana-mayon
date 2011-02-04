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
<br/>
<?php
echo '<a href="' . public_path('wiki/doku.php?id=manual:user:event') . '" target="new" class="buttonText" title="Help">Help</a><br/>';
?>

<?php if ($ag_events) {
?>

  <h3>Existing Events</h3>

  <table>
    <thead>
      <tr>
        <th>Id</th>
        <th>Event Name</th>
        <th>Scenario Base</th>
        <th>Created at</th>
        <th>Updated at</th>
        <th>Status</th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($ag_events as $ag_event): ?>
      <tr>
        <td><a href="<?php echo url_for('event/deploy?id=' . $ag_event->getId()) ?>"><?php echo $ag_event->getId() ?></a></td>
        <td><?php echo $ag_event->getEventName() ?></td>
        <td><?php echo $ag_event->getAgEventScenario()->getFirst()->getAgScenario() ?></td>
        <td><?php echo $ag_event->getCreatedAt() ?></td>
        <td><?php echo $ag_event->getUpdatedAt() ?></td>
        <td><?php echo $ag_event->getAgEventStatus()->getFirst()->getAgEventStatusType() ?></td>
        <td><a href="<?php echo url_for('report/list') ?>" class="linkButton">reports</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <a href="<?php //echo url_for('agEvent')  ?>">New</a>

<?php
    } //end if for ag event listing
?>