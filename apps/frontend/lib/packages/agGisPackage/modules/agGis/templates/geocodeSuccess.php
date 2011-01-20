<h3>Geocode Addresses</h3>

<p>There are <span class="logName"><?php echo $uncodedStaffCount; ?></span> staff records without geo-coding.</p>

<a href="<?php echo url_for('gis/geocode?type=staff') ?> " class="buttonSmall">Calculate Staff</a>
<br/>

<p>There are <span class="logName"><?php echo $uncodedFacilityCount; ?></span> facility records without geo-coding.</p>

<a href="<?php echo url_for('gis/geocode?type=staff') ?> " class="buttonSmall">Calculate Staff</a>
<br/>

<p>Geocoding is scored for how 'good' it is...below are your current results</p>

<p>There are <span class="logName"><?php echo $goodCount; ?></span> <span class="green">good</span> records.</p>
<a href="<?php echo url_for('reporting/goodgeo'); ?> " class="buttonSmall">view report</a>

<p>There are <span class="logName"><?php echo $zipCount; ?></span> <span class="orange">zip-code-only</span> records.</p>
<a href="<?php echo url_for('reporting/zipgeo'); ?> " class="buttonSmall">view report</a>

<p>There are <span class="logName"><?php echo $nonCount; ?></span> records <span class="fail">unable to be geocoded.</span>
  <a href="<?php echo url_for('reporting/ungeo'); ?> " class="buttonSmall">view report</a></p>


<?php
//if the page is being rendered after calculation, we want to get results of geocoding
//the above links should actually be javascript calls to the server, which will process in the background, once the server returns information display it in the section below
if (isset($coderesults)) {
?>

  <h3>Sahana Agasti Geocode</h3>

  the results of your geocoding:<br />

  <strong>you tried to enter:</strong><?php var_dump($address) //number,street,address    ?><br/>
  <strong>the geoserver returned:</strong><?php var_dump($result) ?>
<?php
}
?>