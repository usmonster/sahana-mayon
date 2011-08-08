<?php use_javascript('jquery.ui.custom.js'); ?>
<form action="<?php echo url_for('scenario/resourcetypes' . '?id=' .$scenario_id) ?>" method="post">
  <div class="inlineListWrapper">
    <?php
//      foreach($resourceForm->getEmbeddedForms() as $eForm) {
//        echo $eForm;
//      }
    echo $resourceForm;
    ?>
  </div>
  <br />
  <h3>Optional: Facility Import</h3>
  <h4>Should you want to import facilities for this scenario click "Import Facilities" to upload.</h4>
  <p>If you have selected Resource Types before importing be sure to click "Save" below before clicking
  "Import Facilities".</p>
  <div>
    <a id="fileImportReplacer" class="generalButton" href="<?php echo url_for('scenario/facilityimport?id=' . $scenario_id) ?>" title="Import Facilities">Import Facilities</a><a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:facility_import&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Import Facilities"> ?</a>
    <p id="replaceMe"></p>
  </div>
  <br />
  <input type="submit" value="Save" class="continueButton" name="Save"/>
  <input type="submit" value="Save and Continue" name="Continue" class="continueButton"/>
</form>