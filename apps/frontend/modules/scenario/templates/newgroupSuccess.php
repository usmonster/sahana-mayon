<?php
  $scenarioName = Doctrine::getTable('agScenario')
      ->findByDql('id = ?', $groupform->getDefault('scenario_id'))
      ->getFirst()->scenario;
?>
<h3>Create New Facility Group for the <span style="color: #ff8f00;"><?php echo $scenarioName; ?> </span> Scenario</h3>

<?php include_partial('groupform', array('groupform' => $groupform,'ag_facility_resources' => $ag_facility_resources, 'ag_allocated_facility_resources' => $ag_allocated_facility_resources)) ?>
