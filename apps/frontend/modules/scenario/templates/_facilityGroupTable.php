<h3>Current Facility Groups for the <span style="color: #ff8f00"><?php echo $scenarioName; ?></span> Scenario</h3><br>
<table class="singleTable">
  <tbody>
    <?php
      foreach ($scenarioFacilityGroups as $scenarioFacilityGroup) {
        echo '<tr>';
        echo '<th class="head">' . $scenarioFacilityGroup->scenario_facility_group . '</th>';
        echo '</tr>';
        foreach ($scenarioFacilityGroup->getAgScenarioFacilityResource() as $scenarioFacilityResource) {
          echo '<tr>';
          echo '<td>' . $scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': '
                  . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type);
          echo '</td>';
          echo '</tr>';
        }
      }

    ?>
  </tbody>
</table>
<br />
