<?php use_javascript('jquery.ui.custom.js'); ?>

<form action="<?php echo url_for('scenario/resourcetypes' . '?id=' .$scenario_id) ?>" method="post">
<?php //echo $resourceForm->renderHiddenFields(false) ?>

  <div class="inlineListWrapper">
    <?php echo $resourceForm ?>
  </div>
  <br />
  <br />
  <input type="submit" value="Save" class="linkButton" name="Save"/>
  <input type="submit" value="Save and Continue" name="Continue" class="linkButton"/>
</form>