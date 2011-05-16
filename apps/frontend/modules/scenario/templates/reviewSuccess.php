<h2>Review Scenario: <span class="highlightedText"><?php echo $scenario_name ?></span></h2>
<?php if(isset($wizardDiv)){

  use_javascript('agMain.js');
  include_partial('wizard', array('wizardDiv' => $wizardDiv));

}
?>
<h3><?php echo $scenario_description ?></h3>
<!-- ideally the above should be 'editable text', i.e. when clicked on they convert to input fields -->
<br/>

<h4>Click the Step name to jump to the step in the Scenario Creator Wizard.</h4>

<table class="blueTable">
    <tr class="head">
        <th class="row1">Steps</th>
        <th>Description</th>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/meta?id=' . $scenario_id) ?>"
   title="Modify Basic Scenario Information">Scenario Name and Description</a></td>
        <td>Name: <span class="highlightedText"><?php echo $scenario_name ?></span><br>Description: <span class="highlightedText"><?php echo $scenario_description ?></span></td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/resourcetypes?id=' . $scenario_id) ?>"
   title="Edit Required Resource Types for Scenario">Manage Required Resource Types</a></td>
        <td>Selected Staff Resource Types: <span class="highlightedText"><?php echo $staffResourceTypeCt ?></span>
          <br>
          Selected Facility Resource Types: <span class="highlightedText"><?php echo $facilityResourceTypeCt ?></span>
        </td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/listgroup?id=' . $scenario_id) ?>"
   title="Edit Facility Groups">Manage Facility Groups</a></td>
        <td>Selected Facility Groups: <span class="highlightedText"><?php echo $facilityGroups ?></span>
          <br>
          Selected Facility Resources: <span class="highlightedText"><?php echo $facilities ?></span>
        </td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/staffresources?id=' . $scenario_id) ?>"
   title="Edit Staff Requirements">Staff Resource Requirements</a></td>
        <td>Completed Resource Requirement Definitions: <span class="highlightedText">
          <?php echo $completedResourceReqs ?></span> of <span class="highlightedText">
          <?php echo ($staffResourceTypeCt * $facilities) ?></span>
        </td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) ?>"
   title="Edit Staff Pool">Staff Pool Definitions</a></td>
        <td>This scenario has <span class="highlightedText">
          <?php echo $staffSearches ?></span> defined staff searches
        </td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/shifttemplates?id=' . $scenario_id) ?>"
   title="Edit Shift Templates">Shift Templates</a></td>
        <td>A total of <span class="highlightedText">
          <?php echo $shiftTemplates ?></span> shift templates have been defined for this scenario
        </td>
    </tr>
    <tr>
        <td><a  class="buttonText" href="<?php echo url_for('scenario/shifts?id=' . $scenario_id) ?>"
   title="View Scenario Shifts">Scenario Shifts</a></td>
        <td>A total of <span class="highlightedText">
          <?php echo $shifts ?></span> shifts have been generated covering
          <span class="highlightedText"><?php echo $operationTime ?></span> of operation
        </td>
    </tr>
</table>


 <br /> 

<form action="<?php echo url_for('event/meta')?>" method="post" name="scenario">
  <input type="hidden" value="<?php echo $scenario_id ?>" id ="ag_scenario_list" name="ag_scenario_list" />
  <input type="submit" value="Deploy Scenario as Event"  class="continueButton" />
</form>

<br />
  <hr class="ruleGray" />

  <a href="<?php echo url_for('scenario/list') ?>" class="continueButton">List Scenarios</a>
  <a href="<?php echo url_for('scenario/meta') ?>" class="continueButton">Create Another Scenario</a>
