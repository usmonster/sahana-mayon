<?php use_javascript('jquery.ui.custom.js'); ?>
<form action="<?php echo url_for('scenario/resourcetypes' . '?id=' .$scenario_id) ?>" method="post">
  <div class="inlineListWrapper">
    <?php echo $resourceForm ?>
  </div>
  <br />
  <br />
  <input type="submit" value="Save" class="continueButton" name="Save"/>
  <input type="submit" value="Save and Continue" name="Continue" class="continueButton"/>
</form>