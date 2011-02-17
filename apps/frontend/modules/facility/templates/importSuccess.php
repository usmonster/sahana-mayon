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




