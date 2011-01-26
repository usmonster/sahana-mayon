<h2>Create New Scenario</h2> <br>
<p>Scenarios are plans for emergency responders.  Using the Scenario Creator you'll add facilities,
  staff, and other resources to your plan.</p>
<p>First, facilities and staff resources must be available to group and plan with.  If you have not
yet added facilities and staff to Agasti use the links below to do so.  Otherwise, continue with
creating your scenario.</p>
<?php
echo '<a href="' . url_for('scenario/new') . '" class="buttonText" title="Create New Scenario"> Continue and Create a Scenario<a/><br/>';
echo '<a href="' . url_for('facility/index') . '" class="buttonText" title="Go to Facilities"> Exit Scenario Creator and Edit Facilities<a/><br/>';
echo '<a href="' . url_for('staff/index') . '" class="buttonText" title="Go to Staff"> Exit Scenario Creator and Edit Staff<a/><br/>';
?>