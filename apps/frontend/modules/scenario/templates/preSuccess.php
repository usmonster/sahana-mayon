<?php use_javascript('agasti.js') ?>
<?php   use_javascript('jquery.ui.custom.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
  use_stylesheet('jquery/mayon.jquery.ui.css');?>

<h2>Create New Scenario</h2>
<p>Scenarios are plans for emergency response.  Using the Scenario Creator you'll add facilities
and plan for staff resources to run them.</p>
<p>Resources, such as staff and facilities, should be loaded into Sahana Agasti to plan with.  Staff and facilities have different
  rules for inclusion, and though scenarios could be built
  <strong>it is strongly recommended staff are loaded before scenarios are created</strong>.</p>
Click the tooltip '?' icon to view the recommended workflow. <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:scenario_pre&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Create New Scenario">?</a>


  <p>If you plan on manually entering your facilities, or staff have not been entered, use the links
below to do so.  Otherwise, continue with creating your scenario.</p>
<?php
echo '<a href="' . url_for('scenario/meta') . '" class="buttonText" title="Create New Scenario"> Continue and Create a Scenario<a/><br/>';
echo '<a href="' . url_for('facility/index') . '" class="buttonText" title="Go to Facilities"> Exit and Manage Facilities<a/><br/>';
echo '<a href="' . url_for('staff/index') . '" class="buttonText" title="Go to Staff"> Exit and Manage Staff<a/><br/>';
?>