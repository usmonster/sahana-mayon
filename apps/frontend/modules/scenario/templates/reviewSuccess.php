<h2>"<?php echo $scenario_name ?>" Scenario Review</h2>

<h3>Fill In Meta Information</h3>
<p>List scenario name and description.  Then the Facility Groups and number.</p>
<br />
<a href="<?php echo url_for('scenario/listgroups?id=' . $scenario_id) ?>" class="linkButton">Manage Facilities/Groups</a>
<a href="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) ?>" class="linkButton">Staff Pool Definitions</a>
<a href="<?php echo url_for('scenario/shifttemplates?id=' . $scenario_id) ?>" class="linkButton">Shift Templates</a>
<a href="<?php echo url_for('scenario/shifts?id=' . $scenario_id) ?>" class="linkButton">Scenario Shifts</a>
<a href="<?php echo url_for('scenario/staffresources?id=' . $scenario_id) ?>" class="linkButton">Staff Resource Requirements</a>


<form action="<?php echo url_for('event/meta')?>" method="post" name="scenario">
  <input type="hidden" value="<?php echo $scenario_id ?>" id ="ag_scenario_list" name="ag_scenario_list" />
  <input type="submit" value="Deploy Scenario as Event" class="linkButton" />
</form>


  <hr />

  <a href="<?php echo url_for('scenario/list') ?>" class="linkButton">List Scenarios</a>
  <a href="<?php echo url_for('scenario/new') ?>" class="linkButton">Create Another Scenario</a>
