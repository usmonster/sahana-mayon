<h2>Geocode Addresses</h2>
<?php
//note for devs: anytime you see <b>Name Of Event</b> it's my placeholder for where you should make
//the app display the name of the event the user is working in.
?>

<p>Geocoding is the process of finding geographic coordinates for an address.  In order to deploy
  staff to facilities near to their home or work address Agasti must determine the distance between
  the staff member and facility; but first, Agasti has to find the locations of the addresses.  Below
  are the records without geocoding.  Click the buttons to begin coding staff or facilities.</p>

<p>There are <span class="logName"><?php echo $uncodedStaffCount; ?></span> staff records without geo-coding.

  <a href="<?php echo url_for('gis/geocode?type=staff') ?> " class="buttonSmall">Calculate Staff</a></p>

<p>There are <span class="logName"><?php echo $uncodedFacilityCount; ?></span> facility records without geo-coding.

  <a href="<?php echo url_for('gis/geocode?type=staff') ?> " class="buttonSmall">Calculate Facilities</a></p>
<br/>

<h3>Geocode Scoring</h3>
<p>Geocoding is scored by how accurate it is based on available address information.
  Below are the current results.  Click the button to create a report of the affected records.</p>

<p>There are <span class="logName"><?php echo $goodCount; ?></span> <span class="green">good</span> records.
  <a href="<?php echo url_for('report/goodgeo'); ?> " class="buttonSmall">View Report</a></p>

<p>There are <span class="logName"><?php echo $zipCount; ?></span> <span class="orange">zip-code-only</span> records.
  <a href="<?php echo url_for('report/zipgeo'); ?> " class="buttonSmall">View Report</a></p>

<p>There are <span class="logName"><?php echo $nonCount; ?></span> records <span class="fail">unable to be geocoded.</span>
  <a href="<?php echo url_for('report/ungeo'); ?> " class="buttonSmall">View Report</a></p>


<?php
//if the page is being rendered after calculation, we want to get results of geocoding
//the above links should actually be javascript calls to the server, which will process in the background, once the server returns information display it in the section below
if (isset($coderesults)) {
?>

  <h3>Sahana Agasti Geocode</h3>

  the results of your geocoding:<br />

  <strong>you tried to enter:</strong><?php var_dump($address) //number,street,address       ?><br/>
  <strong>the geoserver returned:</strong><?php var_dump($result) ?>
<?php
}
?>