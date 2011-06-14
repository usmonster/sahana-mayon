<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<h2>Staff Export Status</h2>

<h3>Export Statistics</h3>
<ul>
  <li><strong>Start:</strong> <?php echo date('F j, Y, g:i:s a',$startTime); ?></li>
  <li><strong>End:</strong> <?php echo date('F j, Y, g:i:s a',$endTime); ?></li>
  <li><strong>Time Elapsed:</strong> <?php echo $importTime; ?></li>
  <li><strong>Max Memory Usage:</strong> <?php echo $peakMemory; ?></li>
</ul>