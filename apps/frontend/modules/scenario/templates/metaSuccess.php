<h2><?php echo $metaAction ?> Scenario<?php
if(isset($scenarioName)) echo ': <span class="highlightedText">' . $scenarioName . "</span>"; ?>
</h2>

<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<p>Scenarios are plans for emergency responders. Using the Scenario Creation Wizard you'll add facilities,
  staff, and other resources to your plan.</p>
  
<h4> Name the Scenario and give it a brief description.<a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:scenario_name_describe&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Name and Describe">?</a> </h4>

<?php include_partial('form', array('form' => $form)) ?>

<p> Click "Save and Continue" to move to the next step.</p>
