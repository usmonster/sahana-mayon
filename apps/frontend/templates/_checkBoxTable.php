<table style="border: solid 1px #dadada">
  <tr>
    <th style="text-align: left; background: #A1E2FF" colspan="<?php echo $maxColumns; ?>">
      <input type="checkbox" name="checkall" id="checkall" value="check all">
      <label for="checkAll" class="bold" style="color: white">Select All</label>
    </th>
  </tr>
  <tr>
  <?php $i = 1; ?>
  <?php foreach($contents as $key => $content): ?>
    <td style="text-align: left">
      <input type="checkbox" name="<?php echo $idPrepend . $content[$id]; ?>" id="<?php echo $idPrepend . $content[$id]; ?>" class="checkToggle">
      <label title="<?php echo $content[$title]; ?>"><?php echo $content[$html]; ?></label>
    </td>
    <?php $i++; ?>
    <?php if(($key + 1) % $maxColumns == 0): ?>
      </tr>
      <tr>
    <?php endif; ?>
  <?php endforeach; ?>
  </tr>
</table>
