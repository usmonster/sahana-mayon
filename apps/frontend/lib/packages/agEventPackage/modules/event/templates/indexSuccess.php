<h2>Event Management</h2>
<p>'Event' refers to a specific activation of the plans made in the Scenario module.  The entire term
  of the response will be referred to as an individual event, which is named in the next step.
  Before activating you will have the opportunity to customize your settings and staff pool in 'Pre-Deployment'.
  You can remain in the pre-deployment steps as long as necessary until the event is 'live'; at which
  point facilities will be activated, shifts created, and staff and volunteers are contacted to report
  and begin response.</p>

<h3>Event Pre-Deployment</h3>
<strong>Please select a scenario to base your event on:</strong><br/>

<?php include_partial('scenarioForm', array('scenarioForm' => $scenarioForm)) ?>
<br/>
<?php
echo '<a href="' . public_path('wiki/doku.php?id=manual:user:event') . '" target="new" class="buttonText" title="Help">Help</a><br/>';
?>

<?php if ($ag_events) {
?>
<hr class="ruleGray"/>
  <h3>Existing Events</h3>

  <table>
    <thead>
      <tr>
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
        <?php
        $current_status = agEventFacilityHelper::returnCurrentEventStatus($ag_event->getId()); //[0]
        if ($current_status != "")
        {
          $cur_status = Doctrine::getTable('agEventStatusType')
            ->findByDql('id = ?', $current_status)
            ->getFirst()->event_status_type;
        }
        else{
          $cur_status = 'for some reason, this event does not have a status';
        }

        ?>

        <td><a href="<?php echo url_for('event/active?event=' . urlencode($ag_event->getEventName())) ?>" class="linkButton"><?php echo $ag_event->getEventName() ?></a></td>
        <td><?php #echo $ag_event->getAgEventScenario()->getFirst()->getAgScenario() ?></td>
        <td><?php echo $ag_event->getCreatedAt() ?></td>
        <td><?php echo $ag_event->getUpdatedAt() ?></td>
        <td><?php echo $cur_status; ?></td>
        <td><a href="<?php echo url_for('report/list') ?>" class="linkButton">reports</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>


<?php
    } //end if for ag event listing
?>
