<h2>Organization Management</h2>

<p>The organization function in Agasti 2.0 is used to record information on government and non-governement organizations who may be involved or affect an emergency response.
  This data is recorded on staff records and used for staff deployment in the event of an emergency.</p>
Please select one of the following actions:<br />
<?php
echo '<a href="' . url_for('organization/new') . '" class="buttonText" title="Create New Organization">Create Organization<a/><br/>';
echo '<a href="' . url_for('organization/list') . '" class="buttonText" title="List Existing Organization">List Organization</a><br/>';
?>
If you would like to search for an organization, please use the search box on the top right.