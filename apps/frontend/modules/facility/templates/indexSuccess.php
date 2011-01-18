<h2>Facility Management</h2>
<p>The Facility Management function of Agasti 2.0 is used to manage the available facility resources during emergency response preparation.
  In this module Emergency Managers have the ability to record their facility record and define its available resources.</p>
Please select one of the following actions:<br />
<?php
echo '<a href="' . url_for('facility/new') . '" class="buttonText" title="Create New Facility">Create Facility<a/><br/>';
echo '<a href="' . url_for('facility/list') . '" class="buttonText" title="List Existing Facility">List Facilities</a><br/>';
echo '<a href="' . url_for('facility/import') . '" class="buttonText" title="Import Facility">Import Facilities</a><br/>';
echo '<a href="' . url_for('facility/export') . '" class="buttonText" title="Export Facilities">Export Facilities</a><br/>';
?>
If you would like to search for a facility, please use the search box on the top right.