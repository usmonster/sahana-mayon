<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<h2>Facility Import Status</h2>
<p>Records imported into temporary table: <?php echo $numRecordsImported ?><br/>
  Error count: <?php echo count($events) ?></p>

<?php if (count($events) > 0): ?>
  <hr>
  <table>
  <?php foreach ($events as $event): ?>
    <tr>
    <?php switch ($event["type"]): 
        case "error": ?>
              <td><?php echo image_tag('error.png', array('alt' => 'Error', 'width' => '24', 'height' => '24')); ?></td>
              <td><?php echo "Error:"; ?></td>
              <?php break; ?>
        <?php case "success": ?>
              <td><?php echo image_tag('ok.png', array('alt' => 'Error', 'width' => '24', 'height' => '24')); ?></td>
              <td><?php echo "Success:"; ?></td>
              <?php break; ?>
        <?php default: ?>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <?php break; ?>
    <?php endswitch; ?>
    <td><?php echo $event["message"]; ?></td>
  </tr>
  <?php endforeach; ?>
  </table>
<?php endif; ?>




