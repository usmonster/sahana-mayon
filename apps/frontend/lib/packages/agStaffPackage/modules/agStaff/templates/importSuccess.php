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
  <li><strong>Total Records:</strong> <?php echo $totalRecords; ?></li>
  <ul>
    <li><strong>Successful:</strong> <?php echo $successful; ?></li>
    <li><strong>Failed:</strong> <?php echo $failed; ?></li>
    <li><strong>Unprocessed:</strong> <?php echo $unprocessed; ?></li>
  </ul>
  <li><strong>Peak Memory Usage: </strong> <?php echo $peakMemory; ?></li>
</ul>
<br>

<?php if ($unprocessedXLS !== FALSE) { ?>
<h3>Notice:</h3>
<p>All import records could not be processed. Please click the Export Failed Records button below to download an XLS with the failed rows. You are encouraged to correct these rows and re-submit the file. Do not re-submit the original file as it may cause errors and duplication.</p>
<a href="<?php echo $unprocessedXLS; ?>" class="generalButton" title="Export Failed Records">Export Failed Records</a>
<br>
<?php } ?>

<table class="blueTable" style="width:auto;" cellspacing="10" cellpadding="10">
    <tr class="head">
        <th>Time</th>
        <th>Type</th>
        <th>Message</th>
    </tr>

 <?php
 foreach ($importer->getImportEvents() as $value) {
        echo "<tr><td>";
        echo date("M d, Y H:i:s T", $value['ts']);
        echo "</td><td ";
        if ($value['lvl'] <= 8) {
            echo "class =\"redColorText boldText centerText\">";
        } elseif ($value['lvl'] == 16) {
            echo "class =\"orangeColorText boldText centerText\">";
        } else {
            echo "class =\"greenColorText boldText centerText\">";
        }
        echo $value['type'];
        echo "</td><td>";
        echo $value['msg'];
        echo "</td></tr>";
    }
    ?>
</table>

   