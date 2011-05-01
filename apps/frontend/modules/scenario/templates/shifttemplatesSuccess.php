<h2>Scenario Shift Templates</h2>
<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<p>Shift Templates are templates for when responding staff will be scheduled to work.  The times 
  entered are relative, and when an event is created time will become specific to that event.</p>
<h3>Shift Templates for
  <span class="highlightedText"">
  <?php echo $scenarioName ?>
  </span>
  Scenario:
</h3>

<?php include_partial('shifttemplateholder', array('shifttemplateforms' => $shifttemplateforms, 'scenario_id' => $scenario_id)) ?>



<a href="#"><span class="info">+ Add Shift Template</span></a> <br />
    <input type="submit" class="linkButton" value="Save, Generate Shifts and Continue" name="Continue" />



