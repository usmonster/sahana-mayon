<?php
use_javascript('agMain.js');
use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
?>

<h2>Select Resource Types: <span class="highlightedText"><?php echo $scenarioName ?> </span></h2>
<?php
include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<h4>Select the Staff and Facility Resources to be used in the <span class="highlightedText"><?php echo $scenarioName;
?> </span> scenario. <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:resource_definition&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="What is a Resource?">?</a></h4>

<?php include_partial('resourceForm', array('resourceForm' => $resourceForm, 'scenario_id' => $scenario_id)) ?>

<p> Click "Save and Continue" to move to the next step.</p>
