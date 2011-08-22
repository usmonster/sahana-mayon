<?php
use_javascript('agasti.js');
use_javascript('agastiMessaging.js');
use_javascript('jQuery.fileinput.js');
use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
?>


<h2>Staff Deployment: <span class="highlightedText"><?php echo $event_name ?></span></h2>
<br/>
<h4>View staff deployment results for the <span class="highlightedText"><?php echo $event_name; ?></span> event.</h4>
<br />

<h3>Deployment Statistics</h3>
<br>
<table class="headerLess">
  <tr>
    <td>
      <span>Start:</span>
    </td>
    <td>
      <?php echo $strStart; ?>
    </td>
  </tr>
  <tr>
    <td>
      <span>End:</span>
    </td>
    <td>
      <?php echo $strEnd; ?>
    </td>
  </tr>
  <tr>
    <td>
      <span>Time Elapsed:</span>
    </td>

    <td>
      <?php echo $strDuration; ?>
    </td>
  </tr>
  <tr>
    <td>
      <span>Staff Persons:</span>
    </td>
    <td>
      <?php echo $batchResults['staff']; ?>
    </td>
  </tr>
  <tr>
    <td>
      <span>Staff Waves:</span>
    </td>
    <td>
      <?php echo $batchResults['waves']; ?>
    </td>
  </tr>
  <tr>
    <td>
      <span>Shifts:</span>
    </td>
    <td>
      <?php echo $batchResults['shifts']; ?>
    </td>
  </tr>
  <tr>
    <td>
      <span>Peak Memory Usage: </span>
    </td>
    <td>
      <?php echo $batchResults['profiler']['maxMem']; ?>
    </td>
  </tr>
</table>
<br />
<br />

<?php if($batchResults['err']): ?>
Staff deployment encountered an error and could not could not complete successfully. The last known error message was: <br />
<?php echo $batchResults['msg']; ?>

<?php else: ?>
Staff deployment successfully completed with the message: <?php echo $batchResults['msg']; ?>
<br />
<br />

<?php include_partial('staffingsummary', array('results' => $staffingSummary, 'reportTime' => $event_zero_hour)); ?>
<?php endif; ?>

<!-- <noscript>
  <p>You must have JavaScript enabled.</p>
</noscript>

<p>The list below shows the staff and facilities they have been assigned to. Review this and then use the export button to generate a spreadsheet to send to your messaging provider.</p>
<a class="generalButton" id="deploystaff" href="#">Deploy Staff</a>
<div id="result">
  <p><?php echo image_tag('indicator.gif') ?> please wait, calculation taking place</p>
</div> -->
