<?php
  use_javascript('agMain.js');
  use_javascript('jquery.ui.custom.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
  use_stylesheet('jquery/mayon.jquery.ui.css');
?>
<h2>Facility Groups: <span class="highlightedText"><?php echo $scenarioName ?> </span></h2>
<?php include_partial('wizard', array('wizardDiv' => $wizardDiv)); ?>
<h4>Click the group name to edit the group, or "Add New" to make a new group in the <span class="highlightedText"><?php echo $scenarioName;
?> </span> scenario.</h4>
<p>Facility Groups are collections of facility resources that complement the groupings of facilities
  in the real-world response plan.<a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:facility_group_types&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="What is a Facility Resource?">?</a></p>

<table class="staffTable">
    <tr class="head">
      <th>Facility Group <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:facility_group&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Facility Group">?</a></th>
      <th>Facility Group Type <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:facility_group_type&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Facility Group Type">?</a></th>
      <th>Allocation Status <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:allocation_status&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Allocation Status">?</a></th>
      <th>Deployment Priority <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:deployment_priority&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Deployment Priority">?</a></th>
      <th>Facility Resource Count <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:facility_resource_count&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Facility Resource Count">?</a></th>
    </tr>
  <tbody>
    <?php foreach ($ag_scenario_facility_groups as $ag_scenario_facility_group): ?>
      <tr>
        <td><a href="<?php echo url_for('scenario/fgroup?id=' . $ag_scenario_facility_group->scenario_id . '&groupid=' . $ag_scenario_facility_group->id ); ?>" class="continueButton">
          <?php echo $ag_scenario_facility_group->getScenarioFacilityGroup() ?></a></td>
      <td><?php echo $ag_scenario_facility_group->getAgFacilityGroupType() ?></td>
      <td><?php echo $ag_scenario_facility_group->getAgFacilityGroupAllocationStatus() ?></td>
      <td><?php echo $ag_scenario_facility_group->getActivationSequence() ?></td>
      <td><?php echo count($ag_scenario_facility_group->getAgFacilityResource()) ?></td>
    </tr>
    <?php endforeach; ?>
        </tbody>
      </table>
<hr class="ruleGray" />


 <a href="<?php echo url_for('scenario/fgroup?id=' . $scenario_id) ?>" class="continueButton" title="Add New Facility Group">Add New Facility Group</a>
 <a href="<?php echo url_for('scenario/staffresources?id=' . $scenario_id) ?>" class="continueButton" title="Continue">Continue</a>