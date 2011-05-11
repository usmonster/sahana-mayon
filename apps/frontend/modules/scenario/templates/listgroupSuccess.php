<?php
  use_javascript('agMain.js');
  use_javascript('jquery.ui.custom.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
  use_stylesheet('jquery/mayon.jquery.ui.css');
?>
<h2>Facility Groups: <span class="highlightedText"><?php echo $scenarioName ?> </span></h2>
<?php include_partial('wizard', array('wizardDiv' => $wizardDiv)); ?>
<p>Facility Groups are collections of facility resources that complement the groupings of facilities
  in the real-world response plan.<a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:facility_group_types&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="What is a Facility Resource?">?</a></p>

<table class="staffTable">
    <tr class="head">
      <th>Facility Group</th>
      <th>Facility Group Type</th>
      <th>Allocation Status</th>
      <th>Activation Sequence</th>
      <th>Facility Resource Count</th>
    </tr>
  <tbody>
    <?php foreach ($ag_scenario_facility_groups as $ag_scenario_facility_group): ?>
      <tr>
        <td><a href="<?php echo url_for('scenario/fgroup?id=' . $ag_scenario_facility_group->scenario_id . '&groupid=' . $ag_scenario_facility_group->id ); ?>" class="linkButton">
          <?php echo $ag_scenario_facility_group->getScenarioFacilityGroup() ?></a></td>
      <td><?php echo $ag_scenario_facility_group->getAgFacilityGroupType() ?></td>
      <td><?php echo $ag_scenario_facility_group->getAgFacilityGroupAllocationStatus() ?></td>
      <td><?php echo $ag_scenario_facility_group->getActivationSequence() ?></td>
      <td><?php echo count($ag_scenario_facility_group->getAgFacilityResource()) ?></td>
    </tr>
    <?php endforeach; ?>
        </tbody>
      </table>
<hr />


 <a href="<?php echo url_for('scenario/fgroup?id=' . $scenario_id) ?>" class="linkButton" title="Add New Facility Group">Add New Facility Group</a>
 <a href="<?php echo url_for('scenario/staffresources?id=' . $scenario_id) ?>" class="linkButton" title="Continue">Continue</a>