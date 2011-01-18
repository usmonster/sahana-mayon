

<h2>Geographic Information System Management</h2>
<p>The GIS module of Agasti 2.0 is used to manage geo-location.
  It works by first identifying a pair of geographical co-ordinates for each location in the records system; including staff and facility locations.
  Next, Agasti calculates the distance between the facilities and your staff.
  During the deployment of an event Agasti will use these distances to send your staff to the ideal location for your available resources.
</p>
Please select one of the following actions:<br/>
<?php
echo '<a href="' . url_for('gis/new') . '" class="buttonText" title="Update Single Entry">Update Single Entry<a/><br/>';
echo '<a href="' . url_for('gis/distance') . '" class="buttonText" title="Calculate Distances">Generated Calculated Distances between two sets of data</a><br/>';
echo '<a href="' . url_for('gis/listfacility') . '" class="buttonText" title="List Existing Facility Geo">View Facility Geo Information</a><br/>';
echo '<a href="' . url_for('gis/liststaff') . '" class="buttonText" title="List Exsting Staff Geo">View Staff Geo Information</a><br/>';
echo '<a href="' . url_for('gis/config') . '" class="buttonText" title="GIS">GIS Settings</a><br/>';
//echo link_to('Excel', 'staff/excelDownload', array('class' => 'linkButton'))
?>

