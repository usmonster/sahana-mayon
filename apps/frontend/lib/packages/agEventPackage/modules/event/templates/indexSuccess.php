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

<?php if ($allEvents): ?>
<hr class="ruleGray"/>
<br>
  <h3>Existing Events</h3>

  <table class="blueTable" style="width:auto">
    <thead>
      <tr class="head">
        <th>Event Name</th>
        <th>Base Scenario</th>
        <th>Status</th>
        <th>Reports</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($allEvents as $event): ?>
      <tr>
        <td><a href="<?php echo url_for('event/active?event=' . urlencode($event['e_event_name'])) ?>" class="buttonText"><?php echo $event['e_event_name'] ?></a></td>
        <td><?php echo $event['s_scenario'] ?></td>
        <td><?php echo $event['est_event_status_type']; ?></td>
        <td><a href="<?php echo url_for('report/list') ?>" class="continueButton">reports</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>


<?php endif ?>
