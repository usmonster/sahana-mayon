

  <h3>Geographic Information System Management</h3>
Hello, welcome to the gis management module of Sahana Agasti 2.0, Mayon
<br />
Please select one of the following gis administration actions:<br />
<?php
echo '<a href="' . url_for('gis/new') . '" class="buttonText" title="Update Single Entry">Update Single Entry<a/><br/>';
echo '<a href="' . url_for('gis/distance') . '" class="buttonText" title="Calculate Distances">Generated Calculated Distances between two sets of data</a><br/>';
echo '<a href="' . url_for('gis/listfacility') . '" class="buttonText" title="List Existing Facility Geo">View Facility Geo Information</a><br/>';
echo '<a href="' . url_for('gis/liststaff') . '" class="buttonText" title="List Exsting Staff Geo">View Staff Geo Information</a><br/>';
echo '<a href="' . url_for('gis/config') . '" class="buttonText" title="GIS">GIS Settings</a><br/>';
//echo link_to('Excel', 'staff/excelDownload', array('class' => 'linkButton'))
?>

