<h3>Scenario Management</h3>
Hello, welcome to the scenario management module of Sahana Agasti 2.0, Mayon
<br />
Please select one of the following scenario administration actions:<br />
<?php
echo '<a href="' . url_for('scenario/new') . '" class="buttonText" title="Create New Scenario">Create Scenario<a/><br/>';
echo '<a href="' . url_for('scenario/list') . '" class="buttonText" title="List Existing Scenarios">List Scenarios</a><br/>';
echo '<a href="' . url_for('scenario/newgroup') . '" class="buttonText" title="Create New Facility Group">Create New Facility Group</a><br/>';
echo '<a href="' . url_for('scenario/listgroup') . '" class="buttonText" title="List Facility Groups">List Facility Groups</a><br/>';
echo '<a href="' . url_for('scenario/grouptype') . '" class="buttonText" title="Manage Facility Group Types">Manage Facility Group Types</a><br/>';
echo '<a href="' . url_for('scenario/scenarioshiftlist') . '" class="buttonText" title="List Scenario Shift">List Scenario Shift</a><br/>';

?>