<h2><span class="highlightedText"><?php echo $scenario_name ?></span> Scenario Review</h2>

<h3><?php echo $scenario_description ?></h3>
<!-- ideally the above should be 'editable text', i.e. when clicked on they convert to input fields -->
<br /><hr />
<a href="<?php echo url_for('scenario/edit?id=' . $scenario_id) ?>" class="linkButton">Modify Scenario Meta Information</a> <br /><br />
<a href="<?php echo url_for('scenario/listgroups?id=' . $scenario_id) ?>" class="linkButton">Manage Facilities/Groups</a> <br /><br />
<a href="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) ?>" class="linkButton">Staff Pool Definitions</a> <br /><br />
<a href="<?php echo url_for('scenario/shifttemplates?id=' . $scenario_id) ?>" class="linkButton">Shift Templates</a> <br /><br />
<a href="<?php echo url_for('scenario/shifts?id=' . $scenario_id) ?>" class="linkButton">Scenario Shifts</a> <br /><br />
<a href="<?php echo url_for('scenario/staffresources?id=' . $scenario_id) ?>" class="linkButton">Staff Resource Requirements</a> <br /><br />


<form action="<?php echo url_for('event/meta')?>" method="post" name="scenario">
  <input type="hidden" value="<?php echo $scenario_id ?>" id ="ag_scenario_list" name="ag_scenario_list" />
  <input type="submit" value="Deploy Scenario as Event" class="linkButton" />
</form>


  <hr />

  <a href="<?php echo url_for('scenario/list') ?>" class="linkButton">List Scenarios</a>
  <a href="<?php echo url_for('scenario/new') ?>" class="linkButton">Create Another Scenario</a>
