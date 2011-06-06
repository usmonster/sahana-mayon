<form action="<?php echo url_for('scenario/staffresources?id=' . $scenario->id) ?>" method="post">
  <?php
    echo $facilityStaffResourceContainer;
  ?>
  <br />
  <br />
  <input class="continueButton" type="submit" value="Save" />
  <input class="continueButton" type="submit" value="Save and Continue" name="Continue"/>
</form>
