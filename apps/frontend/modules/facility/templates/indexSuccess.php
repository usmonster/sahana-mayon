<?php use_javascript('agasti.js') ?>
<?php use_javascript('jQuery.fileinput.js') ?>
<h2>Facility Management</h2>
<p>The Facility Management function of Agasti 2.0 is used to manage the available facility resources during emergency response preparation.
  In this module Emergency Managers have the ability to record their facility record and define its available resources.</p>
<b>Please select one of the following actions:</b><br />
<?php
echo
link_to('Create Facility', 'facility/new', array('class' => 'buttonText', 'title' => 'Create New Facility')) . '<br/>' .
 link_to('List Facilities', 'facility/list', array('class' => 'buttonText', 'title' => 'List Existing Facility')) . '<br/>';
?>
<span style="display: inline-block; margin: 0px; padding: 0px" >
  <?php echo link_to('Import Facilities', 'facilities/import', array('class' => 'buttonText', 'title' => 'Import Facilities', 'id' => 'import')); ?>
  <form id="importForm" style="position: relative; display: inline-block" action="<?php echo url_for('facility/import') ?>" method="post" enctype="multipart/form-data">
    <div style="position: absolute; top: 0px; left: 0px; z-index: 1; width: 250px">
      <input  style="display: inline-block; color: #848484" class="inputGray" id="show" />
      <a class="linkButton" style="display: inline-block; padding: 3px">Browse</a>
    </div>
    <input type="file" name="import" id="fileUpload" />
    <?php
    $labels = $filterForm->getWidgetSchema()->getLabels();
    $fields = $filterForm->getWidgetSchema()->getFields();
    $wSchema = $filterForm->getWidgetSchema();
    foreach ($fields as $key => $field) {
      echo '<label class ="filterButton">' . $labels[$key] . '</label>';
      echo $filterForm[$key];
    }
    ?>
    <input type="submit" name="submit" value="Submit" class="submitLinkButton" />
  </form>
</span>
<br/>

<?php echo link_to('Export Facilities', 'facility/facilityExport', array('class' => 'buttonText', 'title' => 'Export Facilities')); ?>
    <br />
<?php echo link_to('Help', public_path('wiki/doku.php?id=manual:user:facilities'), array('target' => 'new', 'class' => 'buttonText', 'title' => 'Help')); ?>
<br />
<p>If you would like to search for a facility, please use the search box on the top right.</p>
