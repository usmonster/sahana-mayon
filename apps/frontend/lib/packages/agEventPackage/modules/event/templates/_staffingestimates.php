<?php
  $uniqStaffCounts = $sf_data->getRaw('uniqStaffCounts');
  if (!empty($uniqStaffCounts)):
?>

The following are staffing estimates at <?php echo date('Y-m-d H:m:s T', $reportTime); ?>.
<br /><br />

<div class="infoHolder" style="width:750px;">
  <h3>Unique Staff Estimates</h3>
  <br />
  <table class="blueTable">
    <thead>
      <tr class="head">
        <th>Unknown</th>
        <th>Available</th>
        <th>Committed</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php echo $uniqStaffCounts['unknown']; ?></td>
        <td><?php echo $uniqStaffCounts['available']; ?></td>
        <td><?php echo $uniqStaffCounts['committed']; ?></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="infoHolder" style="width:750px;">
  <h3>Staff Type Estimates</h3>
  <br />
  <table class="blueTable">
    <thead>
      <tr class="head">
        <th>Staff Type</th>
        <th>Unknown</th>
        <th>Available</th>
        <th>Committed</th>
        <th>Shift Requirements</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($staffTypeEstimates AS $typeId => $se): ?>
        <?php if ($typeId !== 'total'): ?>
      <tr>
        <td><?php echo $se['resource_type']; ?></td>
        <td><?php echo $se['unknown']; ?></td>
        <td><?php echo $se['available']; ?></td>
        <td><?php echo $se['committed']; ?></td>
        <td><?php echo $se['min_required'] . ' - ' . $se['max_required']; ?></td>
      </tr>
        <?php endif; ?>
      <?php endforeach; ?>

      <tr class="noborder"><td colspan="5"></td></tr>

      <tr>
        <td>Totals:</td>
        <td><?php echo $staffTypeEstimates['total']['unknown']; ?></td>
        <td><?php echo $staffTypeEstimates['total']['available']; ?></td>
        <td><?php echo $staffTypeEstimates['total']['committed']; ?></td>
        <td><?php echo $staffTypeEstimates['total']['min_required'] . ' - ' . $staffTypeEstimates['total']['max_required']; ?></td>
      </tr>
    </tbody>
  </table>
</div>

<?php
  endif;
?>