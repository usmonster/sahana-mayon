<?php use_javascript('agasti.js') ?>
<?php
use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
?>

<h2>Scenario Management</h2>
<p>In Sahana Agasti scenarios are plans. Using scenarios Emergency Managers are able
  to plan the deployment of staff and facility resources during emergency response.
  These scenarios complement existing response plans.  Once created, scenarios can be
  customized and modified as the emergency response plan evolves.</p>
<p>In an actual emergency the scenario is activated and becomes an Event.  Events
  deploy resources to the affected areas as per the scenarios they are based on.</p>
<h3>Please select one of the following scenario actions:</h3>
<?php
echo '<a href="' . url_for('scenario/pre') . '" class="generalButton" title="Create New Scenario">Create Scenario<a/><br/><br/>';
echo '<a href="' . url_for('scenario/list') . '" class="generalButton" title="List Existing Scenarios">List Scenarios</a><br/><br/>';
echo '<a href="' . url_for('scenario/grouptype') . '" class="generalButton" title="Facility Group Types">Manage Facility Group Types</a><a href="' . url_for('@wiki') . '/doku.php?id=tooltip:facility_group_types&do=export_xhtmlbody" class="tooltipTrigger" title="Manage Facility Group Type"> ?</a><br/><br/>';
echo '<a href="' . public_path('wiki/doku.php?id=manual:user:scenario') . '" target="new" class="generalButton" title="Help">Help</a><br/><br/>';
?>
Some users may find it helpful to use the Wiki Tutorial the first time they create a scenario:
<br/>
<br/>
<?php
echo '<a href="' . public_path('wiki/doku.php?id=manual:user:scenario:Tutorial') . '" target="new" class="generalButton" title="Help">Scenario Creator Tutorial</a><br/>';
?><br>