<?php
use_javascript('agasti.js');
use_javascript('jQuery.fileinput.js');
use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
?>

<h2>Staff Management <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:staff_management&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Staff Management">?</a></h2>

<p>The Staff Management feature in Sahana Agasti is used to manage your available staff resources while planning before an emergency response.</p>
<h3>Please select one of the following actions:</h3>

<a href="<?php echo url_for('staff/new'); ?>" class="buttonText" title="Create New Staff">Add Staff</a><br/>

<a href="<?php echo url_for('staff/list'); ?>" class="buttonText" title="List Existing Staff">List Staff</a><br/>

<span style="display: inline-block; margin: 0px; padding: 0px" >
<?php echo link_to('Import Staff', 'staff/import', array('class' => 'buttonText', 'title' => 'Import Staff', 'id' => 'import')); ?>
  <form id="importForm" style="position: relative; display: inline-block" action="<?php echo url_for('staff/import') ?>" method="post" enctype="multipart/form-data">
    <div style="position: absolute; top: 0px; left: 0px; z-index: 1; width: 250px">
      <input  style="display: inline-block; color: #848484" class="inputGray" id="show" />
      <a class="linkButton" style="display: inline-block; padding: 3px">Browse</a>
    </div>
    <input type="file" name="import" id="fileUpload" />


    <input type="submit" name="submit" value="Submit" class="submitLinkButton" />
  </form>
</span>
<br/>


<a href="<?php echo url_for('staff/export'); ?>" class="buttonText" title="Export Staff">Export Staff</a><br/>

<?php
echo '<a href="' . public_path('wiki/doku.php?id=manual:user:staff') . '" target="new" class="buttonText" title="Help">Help</a><br/>';
?><br>

If you would like to search for staff, please use the search box on the top right.