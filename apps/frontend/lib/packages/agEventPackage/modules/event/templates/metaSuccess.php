<h2>Event Management</h2> <br/>
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

<p>Below are checks that the scenario is correct and ready to be deployed.
  If the checks show "OK" and the staff pool meets your staffing needs, proceed with deployment.
  If not, return to the scenario and make corrections.  </p>

<?php
$checkResults = $sf_data->getRaw('checkResults');
if (isset($checkResults)) {
    echo '<table class="headerLess">';
    // echo '<tr class="head"><th class="row1">Warning</th><th>Count</th></tr>';
    foreach ($checkResults as $label => $checkResult) {
        echo '<tr>';
        echo '<td><span>' . $label . '</span></td><td class="highlightedText">';
        if (is_array($checkResult)) {
            echo implode(', ', $checkResult);
            if (empty($checkResult)) {
                echo '<span style="color:green; font-weight:bold">OK</span>';
            }
        } else {
            echo $checkResult;
        }
        echo '</td></tr>';
    }
    echo '</table>';
}

if (isset($errMsg)) {
    echo '<p>' . $errMsg . '</p>';
}
?>

<form action="<?php echo url_for('event/meta?event=' .  urlencode($event_name)) ?>" method="post">

<?php
//We should have some warnings here in case you're missing something.
?>
 
</form><br/>
<h3>Provide a name for the event and the Zero Hour:</h3>
<p><strong>Note:</strong> the name should be specific.  For example: a hurricane response to Hurricane Erica could
  be named "Erica".  The Zero Hour is the time the event occurred or is expected to occur.
<strong>Remember</strong> facility activation time is based on Zero Hour.</p>
<?php include_partial('metaForm', array('metaForm' => $metaForm, 'scenario_id' => $scenario_id, 'event_name' => $event_name, 'isPreDeploy' => $isPreDeploy)) ?>
<br />