<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$i = 1;
?>

<h2>Facility Import Status</h2>

<?php include_partial('infobar', array('form' => $form)); ?>

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

    <?php #if (isset($errMsg)) echo $errMsg; ?>
    <?php
      if (isset($errMsg))
      {
        echo '<br />app: ' . $errMsg['app'];
        echo '<br />env: ' . $errMsg['env'];
        echo '<br />debug: ' . $errMsg['debug'];
        echo '<br />no_script_name: ' . $errMsg['no_script_name'];
      }
    ?>

    <?php if (isset($summary)): ?>
      <br /><br /><b>Total Number of Processed Records:</b> <?php echo $summary['totalProcessedRecordCount']; ?>
      <br /><br /><b>Total New Facility Created:</b> <?php echo $summary['totalNewFacilityCount']; ?>
      <br /><br /><b>Total New Facility Group Created:</b> <?php echo $summary['totalNewFacilityGroupCount']; ?>
      <br /><br /><b>Total Number of Non-processed Records:</b> <?php echo count($summary['nonprocessedRecords']); ?>

      <br /><br />

      <?php if (count($summary['nonprocessedRecords']) > 0): ?>
        <table>
          <caption>Non-processed Records</caption>
          <tr><th>&nbsp;</th><th>Type</th><th>Record Info</th></tr>
          <?php $i = 1; ?>
          <?php foreach ($summary['nonprocessedRecords'] as $failedRecord): ?>
          <tr bgcolor="<?php echo($i % 2 == 0 ? '#F7F7F7' : '#F1F1F1'); ?>">
            <td><?php echo $i++; ?></td>
            <td><?php echo $failedRecord['message']; ?></td>
            <td>Facility Name: <?php echo $failedRecord['record']['facility_name']; ?>
              <br />Facility Code: <?php echo $failedRecord['record']['facility_code']; ?>
              <br />Facility Resource Type Abbr: <?php echo $failedRecord['record']['facility_resource_type_abbr']; ?>
          </tr>
          <?php endforeach ?>
        </table>
      <?php endif ?>

      <br /><br />

      <?php if (count($summary['warningMessages']) > 0): ?>
      <table>
        <caption>Warning Records</caption>
        <tr><th>&nbsp;</th><th>Type</th><th>Record Info</th></tr>
        <?php $i = 1; ?>
        <?php foreach($summary['warningMessages'] as $warning): ?>
        <tr bgcolor="<?php echo($i % 2 == 0 ? '#F7F7F7' : '#F1F1F1'); ?>">
          <td><?php echo $i++; ?></td>
          <td><?php echo $warning['message']; ?></td>
          <td>Facility Name: <?php echo $warning['record']['facility_name']; ?>
            <br />Facility Code: <?php echo $warning['record']['facility_code']; ?>
            <br />Facility Resource Type Abbr: <?php echo $warning['record']['facility_resource_type_abbr']; ?>
        </tr>
        <?php endforeach ?>

      </table>
      <?php endif ?>
    <?php else: ?>
      <br /><B>File not imported.</B>
    <?php endif ?>





