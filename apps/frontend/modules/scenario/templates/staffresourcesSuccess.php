<?php
//  use_javascript('agMain.js');
  use_javascript('jquery.watermarkinput.js');
?>

<h2>Staff Resource Requirements: <span class="highlightedText"><?php echo $scenarioName ?> </span></h2>

<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<h4>
  <span>
    Assign minimum and maximum Staff Resource Requirements <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:staff_resource&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="What is a Staff Resource?">?</a> to Facility Groups for the
  </span>
  <span class="logName">
    <?php echo $scenario->scenario ?>
  </span>
  <span>
    Scenario:
  </span>
</h4><br />

<strong>Note:</strong> If a facility does not require any of a staff resource type leave the min and max
blank.  <strong>A facility resource must have at least one staff resource entered.</strong>

<?php //include_partial('staffresourceform', array('staffresourceform' => $staffresourceform, 'ag_staff_resources' => $ag_staff_resources, 'scenario' => $scenario, 'formsArray' => $formsArray)) ?>


<?php
    include_partial('staffresourceform', array(
      'facilityStaffResourceContainer' => $facilityStaffResourceContainer,
      'scenario' => $scenario,
      'pager' => $pager,
    ));

?>
    <br>
    
<p>Click "Save" to save any updates and continue editing on this page.
  Click "Save and Continue" to move to the next step.</p>