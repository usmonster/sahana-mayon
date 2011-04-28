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
?>
<br>

<h3>Event Pre-Deployment</h3>
<?php include_partial('metaForm', array('metaForm' => $metaForm, 'scenario_id' => $scenario_id, 'event_name' => $event_name)) ?>
