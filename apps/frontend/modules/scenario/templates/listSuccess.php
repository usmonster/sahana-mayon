<h3>Scenario Listing</h3>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Scenario</th>
      <th>Description</th>
      <th>Facility Groups</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_scenarios as $ag_scenario): ?>
      <tr>
        <td><a href="<?php echo url_for('scenario/edit?id=' . $ag_scenario->getId()) ?>" class="linkButton">
          <?php echo $ag_scenario->getId() ?></a></td>
      <td><?php echo $ag_scenario->getScenario() ?></td>
      <td><?php echo $ag_scenario->getDescription() ?></td>
      <td><?php echo count($ag_scenario->getAgScenarioFacilityGroup()) ?></td>
      <td><?php echo $ag_scenario->getCreatedAt() ?></td>
      <td><?php echo $ag_scenario->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
        </tbody>
      </table>

      <a class="linkButton" href="<?php echo url_for('scenario/new') ?>">New</a>
