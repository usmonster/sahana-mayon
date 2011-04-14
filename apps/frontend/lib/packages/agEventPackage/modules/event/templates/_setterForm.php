<form action="<?php echo url_for('event/eventfacilityresource?eventFacilityResourceId=' . $efrId); ?>" id="<?php echo $id; ?>" method="post">
  <?php echo $form; ?>
  <input class="buttonSmall submitTextToForm" type="submit" name="<?php echo $set?>" value="Set">
</form>

