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
<br> 
<table class="headerLess">
    <tr>
        <td>
            <span>Start:</span>
        </td>
        <td>
            <?php echo date('F j, Y, g:i:s a', $startTime); ?>
        </td>
    </tr>
    <tr>
        <td>
            <span>End:</span>
        </td>
        <td>
            <?php echo date('F j, Y, g:i:s a', $endTime); ?>
        </td>
    </tr>
    <tr>
        <td>
            <span>Time Elapsed:</span>
        </td>

        <td>
            <?php echo $importTime; ?>
        </td>
    </tr>
    <tr>
        <td>
            <span>Total Records:</span>
        </td>
        <td>
            <?php echo $totalRecords; ?>
        </td>
    </tr>
    <tr>
        <td>
            <span>Successful:</span>
        </td>
        <td>
            <?php echo $successful; ?>
        </td>
    </tr>
    <tr>
        <td>
            <span>Failed:</span>
        </td>
        <td>
            <?php echo $failed; ?>
        </td>
    </tr>
    <tr>
        <td>
            <span>Unprocessed:</span>
        </td>
        <td>
            <?php echo $unprocessed; ?>
        </td>
    </tr>
    <tr>
        <td>
            <span>Peak Memory Usage: </span>
        </td>
        <td>
            <?php echo $peakMemory; ?>
        </td>
    </tr>
</table>
<br>


<?php if ($unprocessedXLS !== FALSE) { ?>
<div class="formSmall">
<h3>Notice:</h3>
<p>All import records could not be processed. Please click the Export Failed Records button below to download an XLS with the failed rows. You are encouraged to correct these rows and re-submit the file. Do not re-submit the original file as it may cause errors and duplication.</p>
<?php echo link_to('Export Unprocessed Records', 'staff/import/exportunprocessed', array(
  'query_string' => 'file=' . $unprocessedXLS['path'])) ?>
</div>
<?php } ?>

<br>
<br>
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

   