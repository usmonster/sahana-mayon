<?php use_javascript('agasti.js') ?>
<?php use_javascript('jQuery.fileinput.js') ?>
<h3>Staff Management</h3>
Hello, welcome to the staff management module of Sahana Agasti 2.0, Mayon
<br />
Please select one of the following staff administration actions:<br />

<a href="<?php echo url_for('staff/new'); ?>" class="buttonText" title="Create New Staff">Create Staff</a><br/>
<a href="<?php echo url_for('staff/list'); ?>" class="buttonText" title="List Existing Staff">List Staff</a><br/>
<span style="display: inline-block; margin: 0px; padding: 0px" >
  <a href="<?php echo url_for('staff/import') ?>" class="buttonText" title="Import Staff" id="import">Import Staff</a>
  <form id="importForm" style="position: relative; display: inline-block" action="staff/import" method="post" enctype="multipart/form-data">
    <div style="position: absolute; top: 0px; left: 0px; z-index: 1; width: 250px">
      <input  style="display: inline-block; color: #848484" class="inputGray" id="show" />
      <a class="linkButton" style="display: inline-block; padding: 3px">Browse</a>
    </div>
    <input type="file" name="import" id="fileUpload" style="text-align: right; opacity: 0; -moz-opacity:0; filter: alpha(opacity=0); z-index: 2; position: relative; width: 190px" />
    <input type="submit" name="submit" value="Submit" class="linkButton" style="display: inline-block; z-index: 3; position: relative;"/>
  </form>
</span>
<br/>
<a href="<?php echo url_for('staff/export'); ?>" class="buttonText" title="Export Staff">Export Staff</a><br/>
If you would like to search for staff, please use the search box on the top right.