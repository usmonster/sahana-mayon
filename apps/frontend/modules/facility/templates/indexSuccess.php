<?php use_javascript('agasti.js') ?>
<?php use_javascript('jQuery.fileinput.js') ?>
<?php use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');?>
<h2>Facility Management <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:facility_management&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Facility Management">?</a></h2>
<p>The Facility Management feature in Sahana Agasti is used to manage your available facility resources while planning before an emergency response.</p>

  <h3>Please select one of the following actions:</h3>
  <?php
    echo
    link_to('Add Facility', 'facility/new', array('class' => 'generalButton', 'title' => 'Create New Facility')) . '<br/><br/>' .
    link_to('List Facilitiy Resources', 'facility/list', array('class' => 'generalButton', 'title' => 'List Existing Facility')) . '<br/><br/>';
  ?>
  <?php
      echo link_to(
          'Help',
          public_path('wiki/doku.php?id=manual:user:facilities'),
          array('target' => 'new', 'class' => 'generalButton', 'title' => 'Help')
      );
  ?>
  <br />
<p>If you would like to search for a facility, please use the search box on the top right.</p>
