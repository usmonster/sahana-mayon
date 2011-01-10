<h3>Sahana Agasti Geocode</h3>

the results of your geocoding:<br />

<strong>you tried to enter:</strong><?php var_dump($address) //number,street,address ?><br/>
<strong>the geoserver returned:</strong><?php var_dump($result) ?>



<?php if (isset($form)){
  echo "<h3>Create New GIS Entry</h3>";
  include_partial('form', array('form' => $form));
}