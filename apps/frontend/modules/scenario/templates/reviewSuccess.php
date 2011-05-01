<h2>Review Scenario: <span class="highlightedText"><?php echo $scenario_name ?></span></h2>

<h3><?php echo $scenario_description ?></h3>
<!-- ideally the above should be 'editable text', i.e. when clicked on they convert to input fields -->
<table class="reviewTable">
    <tr class="head">
        <th class="row1">Steps</th>
        <th>Description</th>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/meta?id=' . $scenario_id) ?>"
   title="Modify Basic Scenario Information">Scenario Name and Description</a></td>
        <td><div style="font-style:oblique">No statistical data is currently available.</div></td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/resourcetypes?id=' . $scenario_id) ?>"
   title="Edit Required Resource Types for Scenario">Manage Required Resource Types</a></td>
        <td><div style="font-style:oblique">No statistical data is currently available.</div></td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/listgroup?id=' . $scenario_id) ?>"
   title="Edit Facility Groups">Manage Facility Groups</a></td>
        <td><div style="font-style:oblique">No statistical data is currently available.</div></td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/staffresources?id=' . $scenario_id) ?>"
   title="Edit Staff Requirements">Staff Resource Requirements</a></td>
        <td><div style="font-style:oblique">No statistical data is currently available.</div></td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) ?>"
   title="Edit Staff Pool">Staff Pool Definitions</a></td>
        <td><div style="font-style:oblique">No statistical data is currently available.</div></td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/shifttemplates?id=' . $scenario_id) ?>"
   title="Edit Shift Templates">Shift Templates</a></td>
        <td><div style="font-style:oblique">No statistical data is currently available.</div></td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/shifts?id=' . $scenario_id) ?>"
   title="View Scenario Shifts">Scenario Shifts</a></td>
        <td><div style="font-style:oblique">No statistical data is currently available.</div></td>
    </tr>
</table>


 <br /> 

<form action="<?php echo url_for('event/meta')?>" method="post" name="scenario">
  <input type="hidden" value="<?php echo $scenario_id ?>" id ="ag_scenario_list" name="ag_scenario_list" />
  <input type="submit" value="Deploy Scenario as Event"  class="linkButton" />
</form>

<br />
  <hr />

  <a href="<?php echo url_for('scenario/list') ?>" class="linkButton">List Scenarios</a>
  <a href="<?php echo url_for('scenario/meta') ?>" class="linkButton">Create Another Scenario</a>
