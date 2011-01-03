<?php
  $scenarioName = Doctrine::getTable('agScenario')
      ->findByDql('id = ?', $groupform->getDefault('scenario_id'))
      ->getFirst()->scenario;

  $scenarioFacilityGroups = Doctrine::getTable('agScenarioFacilityGroup')
      ->findByDql('scenario_id = ?', $groupform->getDefault('scenario_id'))
      ->getData();

  if($groupform->getDefault('scenario_id') <> null) {
    include_partial('facilityGroupTable', array('scenarioFacilityGroups' => $scenarioFacilityGroups, 'scenarioName' => $scenarioName));
  }
?>
<h3>Create New Facility Group for the <span style="color: #ff8f00;"><?php echo $scenarioName; ?> </span> scenario</h3>
<div>
  <?php include_partial('groupform', array('groupform' => $groupform,'ag_facility_resources' => $ag_facility_resources, 'ag_allocated_facility_resources' => $ag_allocated_facility_resources)) ?>
</div>