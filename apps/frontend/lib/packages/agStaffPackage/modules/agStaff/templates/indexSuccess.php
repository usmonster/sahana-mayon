<?php use_javascript('agasti.js') ?>
<?php use_javascript('jQuery.fileinput.js') ?>
<h2>Staff Management</h2>
<p>The Staff Management feature in Agasti 2.0 is used to manage your available staff resources while planning before an emergency response.</p>
<b>Please select one of the following actions: </b><br />

<a href="<?php echo url_for('staff/new'); ?>" class="buttonText" title="Create New Staff">Create Staff</a><br/>

<a href="<?php echo url_for('staff/list'); ?>" class="buttonText" title="List Existing Staff">List Staff</a><br/>

<span style="display: inline-block; margin: 0px; padding: 0px" >
  <a href="<?php echo url_for('facility/import') ?>" class="buttonText" title="Import Staff" id="import">Import Staff</a>
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