<?php use_javascript('agasti.js') ?>
<?php use_javascript('jQuery.fileinput.js') ?>
<?php use_javascript('Menu_Shape.js') ?>
<h3>Staff Management</h3>
Hello, welcome to the staff management module of Sahana Agasti 2.0, Mayon
<br />
Please select one of the following staff administration actions:<br />

<a href="<?php echo url_for('staff/new'); ?>" class="buttonText" title="Create New Staff">Create Staff</a><br/>
<a href="<?php echo url_for('staff/list'); ?>" class="buttonText" title="List Existing Staff">List Staff</a><br/>
<span class="displayInlineBlock noMargin noPadding">
  <a href="<?php echo url_for('staff/import') ?>" class="buttonText" title="Import Staff" id="import">Import Staff</a>
  <form id="importForm" action="staff/import" method="post" enctype="multipart/form-data">
    <div>
      <input class="inputGray displayInlineBlock" id="show" />
      <a class="linkButton displayInlineBlock padding3Px">Browse</a>
    </div>
    <input type="file" name="import" id="fileUpload" class="clientIndexInputUpload"/>
    <input type="submit" name="submit" value="Submit" class="linkButton clientIndexSubmit"/>
  </form>
</span>
<br/>
<a href="<?php echo url_for('staff/export'); ?>" class="buttonText" title="Export Staff">Export Staff</a><br/>
If you would like to search for staff, please use the search box on the top right.