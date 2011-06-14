<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$i = 1;
?>

<h2>Staff Import Status</h2>

<?php // include_partial('facility/infobar', array('timer' => $timer)); ?>

<h3>Import Statistics</h3>
<ul>
  <li><strong>Start:</strong> <?php echo date('F j, Y, g:i:s a',$startTime); ?></li>
  <li><strong>End:</strong> <?php echo date('F j, Y, g:i:s a',$endTime); ?></li>
  <li><strong>Time Elapsed:</strong> <?php echo $importTime; ?></li>
  <li><strong>Records Imported:</strong> <?php echo $importCount; ?></li>
  <li><strong>Peak Memory Usage: </strong> <?php echo $peakMemory; ?></li>
</ul>
