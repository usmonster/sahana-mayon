<h2>Create New Scenario</h2>
<p>Scenarios are plans for emergency responders.  Using the Scenario Creator you'll add facilities
and plan for staff resources to run them.</p>
<p>First, some resources should be loaded to plan with.  Staff and facilities have different
  rules for inclusion in Agasti; and though scenarios could be created
  <b>it is strongly recommended staff are loaded before scenarios are created</b>.</p>
  <p>Facilities can either be entered manually or importing.
    <b>Manually entered facilities should be entered before creating a scenario.</b><br>
    <b>Imported facilities are uploaded during or after the initial scenario creation.</b></p>


  <p>If you plan on manually entering your facilities, or staff have not been entered, use the links
below to do so.  Otherwise, continue with creating your scenario.</p>
<?php
echo '<a href="' . url_for('scenario/meta') . '" class="buttonText" title="Create New Scenario"> Continue and Create a Scenario<a/><br/>';
echo '<a href="' . url_for('facility/index') . '" class="buttonText" title="Go to Facilities"> Exit and Manage Facilities<a/><br/>';
echo '<a href="' . url_for('staff/index') . '" class="buttonText" title="Go to Staff"> Exit and Manage Staff<a/><br/>';
?>