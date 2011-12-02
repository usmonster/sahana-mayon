<?php use_javascript('agasti.js') ?>
<?php use_javascript('jQuery.fileinput.js') ?>
<?php
use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
?>
<h2>Organization Management <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:organization_management&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Organization Management">?</a></h2>

<p>The Organization Management function in <?php echo sfConfig::get('sf_application_name'); ?> is used to record information on government and
  non-government organizations who may be involved with an emergency response.
  This data is recorded on staff records and used for staff deployment when planning
  for and responding to an emergency.</p>

<h3>Please select one of the following actions:</h3>
<?php
echo '<a href="' . url_for('organization/new') . '" class="generalButton"title="Create New Organization">Create Organization<a/><br/><br/>';
echo '<a href="' . url_for('organization/list') . '" class="generalButton" title="List Existing Organization">List Organization</a><br/><br/>';
echo '<a href="' . public_path('wiki/doku.php?id=manual:user:organizations') . '" target="new" class="generalButton" title="Help">Help</a><br/><br/>';
?>