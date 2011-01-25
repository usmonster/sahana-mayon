<?php use_javascript('agasti.js') ?>
<?php use_javascript('jQuery.fileinput.js') ?>
<?php
//note for devs: anytime you see *Name Of Event* it's a placeholder for where you should make
//the app display the name of the event the user is working in.
//
// This page is currently a stub!  The following random string is a marker for the stub.
// PnaODfcm3Kiz4MV4vzbtr4
// PLEASE REMOVE THIS COMMENT BLOCK WHEN YOU DEVELOP THIS PAGE!
//
//You should probably add a confirmation that the event was deployed here when it's first created.
?>
<h2> *Event Name* Client Management </h2>
<p>During an emergency response staff and volunteers working within the shelter system will use
  the client functionality of Agasti to manage client's sensitive information.</p>

Please select one of the following staff administration actions:<br />

<a href="<?php echo url_for('agClient/new'); ?>" class="buttonText" title="Create New Client">Create Client Record</a><br/>
<a href="<?php echo url_for('agClient/list'); ?>" class="buttonText" title="List Existing Clients">List Client Records</a><br/>
<a href="<?php echo url_for('agClient/out'); ?>" class="buttonText" title="Check Out Clients">Check Out Clients</a><br/>
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