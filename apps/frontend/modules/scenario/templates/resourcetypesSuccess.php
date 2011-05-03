<?php
use_javascript('agMain.js');
use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
use_javascript('agTooltip.js');
?>
<script language="Javascript" type="text/javascript"> 
  $(document).ready(function(){
    var maxHeight=0;

    $('div.inlineLists').each(function(){
      maxHeight = Math.max(maxHeight, $(this).height());
    });

    $('div.inlineLists').height(maxHeight);
  })

</script>
<h2>Select Resource Types: <span class="highlightedText"><?php echo $scenarioName ?> </span></h2> <br />
<?php
include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<p>Select the Staff and Facility Resources to be used in the <span class="highlightedText"><?php echo $scenarioName;
?> </span> scenario. <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:resource_definition&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="What is a Resource?">?</a>
<br>Click 'Save and Continue' when you are ready to move forward.</p>


<?php include_partial('resourceForm', array('resourceForm' => $resourceForm, 'scenario_id' => $scenario_id)) ?>
