<h2><strong><span class="highlightedText"><?php echo $sf_data->getRaw('event_name'); ?></span></strong> Pre-Deployment</h2>
<p>The final steps in preparation are often the most critical.  
  In the final steps of pre-deployment, you'll ensure all geographic reference information for
  your staff and facility resources are up to date, and that the shifts for <span class="highlightedText"><?php echo $sf_data->getRaw('event_name'); ?></span> event are
  up to date.</p>

<?php
$checkResults = $sf_data->getRaw('checkResults');
if (isset($checkResults)) {
  foreach ($checkResults as $label => $checkResult)
  {
    echo '<em>' . $label . '</em>: ';
    if (is_array($checkResult))
    {
      echo implode(', ', $checkResult);
    } else {
      echo $checkResult;
    }
      echo '<br />';
  }
}
?>
<br />
<form action="<?php echo url_for('event/deploy?event=' .  urlencode($sf_data->getRaw('event_name'))) ?>" method="post">

<a href="<?php echo url_for('scenario/staffpool?id=' . $scenario_id); ?>" class="continueButton" title="Modify Scenario Staff Pool">Modify Scenario Staff Pool</a>

<a href="#" class="continueButton" title="Generate Shifts">Generate Shifts</a>

<?php
//We should have some warnings here in case you're missing something.
?>
<a href="#" class="continueButton" title="Save">Save</a>

<input type="submit" value="Save and Deploy" class="continueButton"/>

</form>