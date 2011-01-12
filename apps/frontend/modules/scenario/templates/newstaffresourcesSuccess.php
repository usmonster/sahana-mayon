<h3>
  <span>
    Assign minimum and maximum staff resource requirements to facility groups for the
  </span>
  <span class="logName">
    <?php echo $scenario->scenario ?>
  </span>
  <span>
    scenario:
  </span>
</h3>
<br />
<?php
  include_partial('staffresourceform', array(
      'formsArray' => $formsArray,
      'scenarioFacilityGroupId' => $scenarioFacilityGroup->id,
      'array' => $arrayBool,
      'scenario' => $scenario,
     // 'ag_facility_resources' => $ag_facility_resources,
     // 'ag_allocated_facility_resources' => $ag_allocated_facility_resources
  ));
?>