<?php
  use_javascript('agMain.js');
  use_javascript('jquery.ui.custom.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
  use_stylesheet('jquery/mayon.jquery.ui.css');
  use_javascript('agTooltip.js'); ?>
<h2><?php echo $metaAction ?> Scenario</h2>
<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<p>Scenarios are plans for emergency responders. Using the Scenario Creation Wizard you'll add facilities,
  staff, and other resources to your plan.  For now, name the Scenario and give it a brief
  description.<a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:scenario_name_describe&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Name and Describe">?</a> </p>

<?php include_partial('form', array('form' => $form)) ?>

<p> After you've completed the description, click "Save and Continue" to move to the next step in
  creating the Scenario.</p>
