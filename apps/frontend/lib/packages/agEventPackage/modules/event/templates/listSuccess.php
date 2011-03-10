<h2>Event Listing</h2>

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
      <td><a href="<?php echo url_for('event/deploy?event=' . $ag_event->getEventName()); ?>"><?php echo $ag_event->getEventName() ?></a></td>
      <td><?php echo $ag_event->getAgEventScenario()->getFirst()->getAgScenario() ?></td>
      <td><?php echo $ag_event->getCreatedAt() ?></td>
      <td><?php echo $ag_event->getUpdatedAt() ?></td>
      <td><?php echo $ag_event->getAgEventStatus()->getFirst()->getAgEventStatusType() ?></td>
      <td><a href="<?php echo url_for('report/list') ?>" class="linkButton">reports</a></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('agEvent') ?>">New</a>
