<h3>Current Facility Groups for the <span style="color: #ff8f00"><?php echo $scenarioName; ?></span> Scenario</h3><br>
<table class="singleTable">
  <tbody>
    <?php
      foreach ($scenarioFacilityGroups as $facilityGroup) {
        echo '<tr>';
        echo '<th class="head"><a href="' . url_for('scenario/fgroup?id=' . $facilityGroup->scenario_id) . '/' . $facilityGroup->id .'">' . $facilityGroup->scenario_facility_group . '</a></th>';
        echo '</tr>';
        foreach ($facilityGroup->getAgScenarioFacilityResource() as $scenarioFacilityResource) {
          echo '<tr>';
          echo '<td>' . $scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': '
                  . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type);
          echo '</td>';
          echo '</tr>';
        }
      }

    ?>
    <tr>
      <?php
      if(is_numeric($group_id)){
      ?>
      <th class="head"><a href="<?php echo url_for('scenario/fgroup?id=' . $facilityGroup->scenario_id) ?>" class="linkButton">Create New Facility Group</a></th>
      <?php
      }
      ?>
    </tr>
  </tbody>
</table>
<br />
