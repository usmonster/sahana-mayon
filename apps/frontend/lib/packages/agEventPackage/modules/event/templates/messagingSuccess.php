<?php
use_javascript('agasti.js');
use_javascript('jQuery.fileinput.js');
use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
?>

<h2>Staff Messaging: <span class="highlightedText"><?php echo $event_name; ?> </span></h2>

<p>Use this page to send messages and receive responses to your staff for <span class="highlightedText"><?php echo $event_name; ?></span>.</p>
<?php
  $exportUrl = url_for('event/exportcontacts?event=' . urlencode($sf_data->getRaw('event_name'))) ;
  echo link_to('Export Staff Contact List', $exportUrl, array('class' => 'generalButton')); 
  //on click of this button, set the div content to 'exporting data, please wait'
if(isset($exportComplete)){
  echo $exportComplete . ' staff records exported, please send this file to your messaging vendor.';
}
  ?>
<span class="infoText" style="padding-left: 10px;">status of export here</span>
<br />
<br />
<span style="display: inline-block; margin: 0px; padding: 0px" >
<?php
  $importUrl = url_for('event/importreplies?event=' . urlencode($sf_data->getRaw('event_name'))) ;
  echo link_to('Import Staff Responses', $importUrl, 
      array('class' => 'generalButton', 'title' => 'Import Staff', 'id' => 'import'));
  
  $wikiUrl =  url_for('@wiki') . '/doku.php?id=tooltip:staff_import&do=export_xhtmlbody';
  echo link_to('?', $wikiUrl, 
      array('class' => 'tooltipTrigger', 'title' => 'Importing Staff Replies', 'id' => 'import'));
  $foo = new agStaff();
?>
  
  <form id="importForm" style="position: relative; display: inline-block" action="
  <?php echo $importUrl ?>" method="post" enctype="multipart/form-data">
    <div style="position: absolute; top: 0px; left: 0px; z-index: 1; width: 250px">
      <input  style="display: inline-block; color: #848484" class="inputGray" id="show" />
      <a class="continueButton" style="display: inline-block; padding: 3px">Browse</a>
    </div>
    <input type="file" name="import" id="fileUpload" />


    <input type="submit" name="submit" value="Submit" class="submitLinkButton" />
  </form>
</span>
<br />
<br />
<a href="#" class="generalButton">Preview Confirmed Staff Pool</a>
<br />
<br />
<?php
  $deployUrl = url_for('event/deploystaff?event=' . urlencode($sf_data->getRaw('event_name'))) ;
  echo link_to('Deploy Staff to Facilities', $deployUrl, array('class' => 'generalButton')); 
?>