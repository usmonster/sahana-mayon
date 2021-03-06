<h2>Shift Templates: <span class="highlightedText"><?php echo $scenarioName ?></span></h2>
<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<p>Shift Templates are templates for when responding staff will be scheduled to work.  The times 
  entered are relative, and when an event is created time will become specific to that event.</p>
<form action="<?php echo url_for('scenario/shifttemplates?id=' . $scenario_id); ?>" method="post" name="shift_template">
  <?php include_partial('shifttemplateholder', array('shifttemplateforms' => $shifttemplateforms, 'scenario_id' => $scenario_id)) ?>
  <div id ="newshifttemplates">
  <!--<a href="<?php echo url_for('scenario/addshifttemplate?id=' . $scenario_id); ?>" class="smallLinkButton addShiftTemplate" id="adder">+ Add Shift Template</a>-->
  </div>
  <a href="<?php echo url_for('scenario/addshifttemplate?id=' . $scenario_id); ?>" class="smallLinkButton addShiftTemplate" id="adder">+ Add Shift Template</a>
  <br />
  <input type="hidden" name="deleteShiftTemplateId" id ="deleteShiftTemplateId"/>
<!--    <input type="submit" class="continueButton" value="Generate Resource Type Templates" name="Predefined" onclick="shiftTemplateIdVal('Hello Shirley');"/> -->
  <input type="submit" class="continueButton" value="Generate Resource Type Templates" name="Predefined" onclick="return confirm('Are you sure you want to generate templates for this scenario resources?  If you have already created templates, they will be lost.')"/>
  <input type="submit" class="continueButton" value="Save, Generate Shifts and Continue" name="Continue" />
</form>
