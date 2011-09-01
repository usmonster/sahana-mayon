<?php
  $uniqStaffCounts = $sf_data->getRaw('uniqStaffCounts');
  if (!empty($uniqStaffCounts)):
?>

The following are staffing estimates at <?php echo date('Y-m-d H:i:s T', $reportTime); ?>.
<br /><br />

<div class="infoHolder" style="width:750px;">
  <h3>Staff Resource Estimates</h3>
  <br />
  <table class="blueTable">
    <thead>
      <tr class="head">
        <th>Unknown</th>
        <th>Unavailable<?php if ($uniqStaffCounts['non_geo'] > 0): ?>*<?php endif; ?></th>
        <th>Available</th>
        <th>Committed</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php echo $uniqStaffCounts['unknown']; ?></td>
        <td><?php echo $uniqStaffCounts['unavailable']; ?></td>
        <td><?php echo $uniqStaffCounts['available']; ?></td>
        <td><?php echo $uniqStaffCounts['committed']; ?></td>
      </tr>
    </tbody>
  </table>

  <br />
  <div>
    <?php echo xspchart_image_tag($statusDistributionChart); ?>
    <?php echo xspchart_image_tag($staffRequiredChart); ?>
  </div>
  <br />
<?php if ($uniqStaffCounts['non_geo'] > 0): ?>
*Of the <?php echo $uniqStaffCounts['unavailable']; ?> staff resources reported as unavailable, <?php echo $uniqStaffCounts['non_geo']; ?> (<?php echo $uniqStaffCounts['non_geo_pctg']; ?>%) are missing geo-data.
</div>
<?php endif; ?>

<div class="infoHolder" style="width:750px;">
  <h3>Staff Type Estimates</h3>
  <br />
  Staff without geo-data are non-deployable. The following estimates exclude staff without this information.
  <br /><br />
  <table class="blueTable">
    <thead>
      <tr class="head">
        <th>Staff Type</th>
        <th>Unknown</th>
        <th>Unavailable</th>
        <th>Available</th>
        <th>Committed</th>
        <th>Minimum<br />Required</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($staffTypeEstimates AS $typeId => $se): ?>
      <tr>
        <td><a class="buttonText" title="<?php echo $se['resource_type']; ?>" href="#st<?php echo $typeId; ?>"><?php echo $se['resource_type']; ?></a></td>
        <td><?php echo $se['unknown']; ?></td>
        <td><?php echo $se['unavailable']; ?></td>
        <td><?php echo $se['available']; ?></td>
        <td><?php echo $se['committed']; ?></td>
        <td><?php echo $se['min_required']; ?></td>
      </tr>
      <?php endforeach; ?>

      <tr class="noborder"><td colspan="6"></td></tr>

      <tr>
        <td>Totals:</td>
        <td><?php echo $staffTypeEstimateTotals['unknown']; ?></td>
        <td><?php echo $staffTypeEstimateTotals['unavailable']; ?></td>
        <td><?php echo $staffTypeEstimateTotals['available']; ?></td>
        <td><?php echo $staffTypeEstimateTotals['committed']; ?></td>
        <td><?php echo $staffTypeEstimateTotals['min_required'] ; ?></td>
      </tr>
    </tbody>
  </table>
  <br />
  <h4>Staff Resource Requirements</h4>
  <br />
  <div align ="center">
  <?php echo xspchart_image_tag($staffTypeRequiredChart); ?>
  </div>
  <h4>Staff Resource Minimum Requirements By Type</h4>
  <br />
  <div>
  <?php
    $i = 0;
    foreach ($staffTypeEstimates AS $typeId => $se) {
    echo '<a title="' . $se['resource_type'] . '" name="st' . $typeId . '">';
    echo xspchart_image_tag($staffTypeStatusDistributionCharts[$typeId]);
    echo '</a>';
      $i++;
      if ($i % 2 == 0) {
        echo '<br />';
      }
    }
  ?>
  </div>
</div>

<?php
  endif;
?>