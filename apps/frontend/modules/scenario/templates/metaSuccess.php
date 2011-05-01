<h2><?php echo $metaAction ?> Scenario</h2>
<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<p>Scenarios are plans for emergency responders. Using the Scenario Creation Wizard you'll add facilities,
  staff, and other resources to your plan.  For now, name the Scenario and give it a brief
  description.</p>

<?php include_partial('form', array('form' => $form)) ?>

<p> After you've completed the description, click "Save and Continue" to move to the next step in
  creating the Scenario.</p>
