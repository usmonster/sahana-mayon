<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$i = 1;
?>

<h2>Facility Import Status</h2>
<p>Records imported into temporary table: <?php echo $numRecordsImported ?><br/>

    <?php if (count($events) > 0): ?>
    <hr>
    <table>
        <tr><th>&nbsp;</th><th>Step</th><th>Type</th><th>Message</th></tr>
    <?php foreach ($events as $event): ?>
            <tr bgcolor="<?php echo($i % 2 == 0 ? '#F7F7F7' : '#F1F1F1'); ?>">
                
        <?php switch ($event["type"]):
            case "OK": ?>
            <td><?php echo image_tag('ok.png', array('alt' => 'OK', 'width' => '24', 'height' => '24')); ?></td>
            <?php break; ?>
        <?php case "INFO": ?>
            <td><?php echo image_tag('info.png', array('alt' => 'Information', 'width' => '24', 'height' => '24')); ?></td>
            <?php break; ?>
        <?php case "WARN": ?>
            <td><?php echo image_tag('warn.png', array('alt' => 'Warning', 'width' => '24', 'height' => '24')); ?></td>
            <?php break; ?>
        <?php case "ERROR": ?>
            <td><?php echo image_tag('error.png', array('alt' => 'Error', 'width' => '24', 'height' => '24')); ?></td>
            <?php break; ?>
        <?php default: ?>
            <td><?php echo image_tag('question.png', array('alt' => 'Unknown', 'width' => '24', 'height' => '24')); ?></td>
        <?php endswitch; ?>
            <td><?php echo $i++; ?></td>
            <td><?php echo $event["type"]; ?></td>
            <td><?php echo $event["message"]; ?></td>
        </tr>
    <?php endforeach; ?>
        </table>

    <?php endif; ?>

    <hr>
    <BR><BR>Total Number of Processed Records: <?php $summary['totalProcessedRecordCount']; ?>
    <BR><BR>Total New Facility Created: <?php $summary['totalNewFacilityCount']; ?>
    <BR><BR>Total New Facility Group Created: <?php $summary['totalNewFacilityGroupCount']; ?>
    <BR><BR>Total Number of Non-processed Records: <?php count($summary['nonprocessedRecords']); ?>
    <BR>Non-processed Records: <BR>
    <table>
      <?php $i = 0; ?>
      <?php foreach ($summary['nonprocessedRecords'] as $failedRecord): ?>
      <tr>
        <td><?php echo $i++; ?></td>
        <td><?php $failedRecord['message']; ?></td>
        <td><?php echo $failedRecord['record']['facility_name']; ?></td>
        <td><?php echo $failedRecord['record']['facility_resource_code']; ?></td>
      </tr>
      <?php endforeach ?>
    </table>

    <BR><BR>Warning Records:
    <table>
      <?php $i = 0; ?>
      <?php foreach($summary['warningMessages'] as $warning): ?>
      <tr>
        <td><?php echo $i++; ?></td>
        <td><?php $warning['message']; ?></td>
        <td><?php $warning['record']['facility_name']; ?></td>
        <td><?php $warning['record']['facility_resource_code']; ?></td>
      </tr>
      <?php endforeach ?>
    </table>




