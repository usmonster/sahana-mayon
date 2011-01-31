<h2>Scenario Shift Templates</h2>
<p>Shift Templates are templates for when responding staff will be scheduled to work.  The times
  entered are relative, and when an event is created time will become specific to that event.</p>
<h3>Shift Templates for
  <span style="color: #ff8f00;">
  <?php echo $scenario_name ?>
  </span>
  Scenario:
</h3>

<?php include_partial('shifttemplateform', array('shifttemplateform' => $shifttemplateform, 'scenario_id' => $scenario_id)) ?>



