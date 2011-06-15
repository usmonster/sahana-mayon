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

<br><br>

<table class="blueTable" style="width:auto;" cellspacing="10" cellpadding="10">
    <tr class="head">
        <th>Time</th>
        <th>Type</th>
        <th>Message</th>
    </tr>

 <?php
 foreach ($multidimarray as $value1) {
        foreach ($value1 as $key2 => $value2) {
            echo "<tr><td>";
            echo date("M d, Y H:i:s.u T", $value1['ts']);
            echo "</td><td ";
            if ($value1['lvl'] <= 8) {
                echo "class =\"redColorText boldText\">";
            } elseif ($value1['lvl'] == 16) {
                echo "class =\"orangeColorText boldText\">";
            } else {
                echo "class =\"greenColorText boldText\">";
            }
            echo $value1['type'];
            echo "</td><td>";
            echo $value1['msg'];
            echo "</td></tr>";
        }
    }
    ?>
</table>

   