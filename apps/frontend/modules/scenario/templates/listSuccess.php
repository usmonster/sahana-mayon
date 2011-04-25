<table class="staffTable">
    <caption>Scenarios
    <?php
    // Output the current staff members being shown, as well total number in the list.
    echo '(' . count($ag_scenarios) . ') total';//$pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count();
    ?>
  </caption>

  <thead>
    <tr class="head">
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
        <td><a href="<?php echo url_for('scenario/review?id=' . $ag_scenario->getId()) ?>" class="linkButton">
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
    <br>
    <div>
      <a class="linkButton" href="<?php echo url_for('scenario/meta') ?>">New</a>
    </div>