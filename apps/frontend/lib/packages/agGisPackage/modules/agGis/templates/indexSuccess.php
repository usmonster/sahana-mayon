

<h2>Geographic Information System Management</h2>
<p>The GIS module of Agasti 2.0 is used to manage geo-location.
  It works by first identifying a pair of geographical co-ordinates for each location in the records system; including staff and facility locations.
  Next, Agasti calculates the distance between the facilities and your staff.
  During the deployment of an event Agasti will use these distances to send your staff to the ideal location for your available resources.
</p>
<b>Please select one of the following actions:</b><br/>
<?php
echo '<a href="' . url_for('gis/geocode') . '" class="buttonText" title="Update Single Entry">Geocode Addresses<a/><br/>';
echo '<a href="' . url_for('gis/distance') . '" class="buttonText" title="Calculate Distances">Generated Calculated Distances Between Two Sets of Data</a><br/>';
echo '<a href="' . url_for('gis/config') . '" class="buttonText" title="GIS">GIS Settings</a><br/>';
//echo link_to('Excel', 'staff/excelDownload', array('class' => 'linkButton'))
// @todo gisquery link is only for testing.  Please remove after testing.

// For testing gis query functions only.
//echo '<a href="' . url_for('gis/gisquery') . '" class="buttonText" title="GIS">GIS Queries</a><br/>';
?>

