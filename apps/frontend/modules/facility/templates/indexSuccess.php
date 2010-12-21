<h3>Facility Management</h3>
Hello, welcome to the facility management module of Sahana Agasti 2.0, Mayon
<br />
Please select one of the following facility administration actions:<br />
<?php
echo '<a href="' . url_for('facility/new') . '" class="buttonText" title="Create New Facility">Create Facility<a/><br/>';
echo '<a href="' . url_for('facility/list') . '" class="buttonText" title="List Existing Facility">List Facilities</a><br/>';
echo '<a href="' . url_for('facility/import') . '" class="buttonText" title="Import Facility">Import Facilities</a><br/>';
echo '<a href="' . url_for('facility/export') . '" class="buttonText" title="Export Facilities">Export Facilities</a><br/>';
echo '<a href="' . url_for('wiki/doku.php?id=manual:user:facilities') . '" target="new" class="buttonText" title="Help">Help</a><br/>';
?>
If you would like to search for a facility, please use the search box on the top right.
