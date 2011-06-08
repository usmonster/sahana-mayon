<h2>Event Management</h2>
<p>Event deployment depends largely on when the event will begun/began.  To facilitate pre-deployment
  settings and the activation of the event's resources, please provide a name for the event and
  the Zero Hour.</p>
<p><strong>Note:</strong> the name should be specific.  For example: a hurricane response to Hurricane Erica could
  be named "Erica" in Agasti.</p>
<?php if(isset($scenarioName)){

?>
<h3>Event based on the <span class="highlightedText"><?php echo $scenarioName; ?></span> Scenario</h3>
<?php
}
else{
  $scenario_id = "";
}

if(!isset($event_name)){

  $event_name ="";
}
else{
  $event_name = $sf_data->getRaw('event_name');
}
?>
<br>

<h2><strong><span class="highlightedText"><?php echo $event_name; ?></span></strong> Pre-Deployment</h2>
<p>The final steps in preparation are often the most critical.  
  In the final steps of pre-deployment, you'll ensure all geographic reference information for
  your staff and facility resources are up to date, and that the shifts for <span class="highlightedText"><?php echo $event_name; ?></span> event are
  up to date.</p>

<?php
$checkResults = $sf_data->getRaw('checkResults');
if (isset($checkResults)) {

  echo '<table class="blueTable" style="width:auto; margin-bottom:10px">';
  echo '<tr class="head"><th class="row1">Steps</th><th>Count</th></tr>';

  foreach ($checkResults as $label => $checkResult)
  {
    echo '<tr>';
    echo '<td><em>' . $label . '</em>:</td><td>';

    if (is_array($checkResult))
    {
      echo implode(', ', $checkResult);
    } else {
      echo $checkResult;
    }
      echo '</td></tr>';
  }

  echo '</table>';

}
?>

<form action="<?php echo url_for('event/meta?event=' .  urlencode($event_name)) ?>" method="post">

<?php
//We should have some warnings here in case you're missing something.
?>
 
</form>

<?php include_partial('metaForm', array('metaForm' => $metaForm, 'scenario_id' => $scenario_id, 'event_name' => $event_name)) ?>
<br />