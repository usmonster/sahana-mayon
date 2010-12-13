<h3>Organization Management</h3>

Hello, welcome to the organization management module of Sahana Agasti 2.0, Mayon
<br />
Please select one of the following organization administration actions:<br />
<?php
echo '<a href="' . url_for('organization/new') . '" class="buttonText" title="Create New Organization">Create Organization<a/><br/>';
echo '<a href="' . url_for('organization/list') . '" class="buttonText" title="List Existing Organization">List Organization</a><br/>';
?>
If you would like to search for an organization, please use the search box on the top right.