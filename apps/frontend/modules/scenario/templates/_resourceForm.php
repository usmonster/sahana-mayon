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
  <br />
  <div>
    <a id="fileImportReplacer" class="generalButton" href="<?php echo url_for('scenario/facilityimport?id=' . $scenario_id) ?>" title="Import Facilities">Import Facilities</a>
    <p id="replaceMe"></p>
  </div>
  <br />
  <input type="submit" value="Save" class="continueButton" name="Save"/>
  <input type="submit" value="Save and Continue" name="Continue" class="continueButton"/>
</form>