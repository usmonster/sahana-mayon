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
    <img alt="Staff Status Pie Chart"  src="<?php echo $pCharts['staffStatusPie']; ?>">
    <img alt="Staff Required Bar Chart"  src="<?php echo $pCharts['staffRequiredBar']; ?>">
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
    <img alt="Staff Type Required Bar Chart"  src="<?php echo $pCharts['staffTypeRequiredBar']; ?>">
  </div>
  <h4>Staff Resource Minimum Requirements By Type</h4>
  <br />

  <!-- @todo PAY PARTICULAR ATTENTION TO CLEANING UP THE INLINE CSS -->
  <div style="padding-left: 1em; padding-right: 4em;">
  <?php $i = 0; foreach ($staffTypeEstimates AS $typeId => $se): ?>
    <?php if ($i % 2 == 0): ?>
      <div>
      <?php $divStyle = 'float: left; padding-bottom: 1em;' ?>
    <?php else: ?>
      <?php $divStyle = 'float: right; padding-bottom: 1em;' ?>
    <?php endif; ?>
    <?php $i++; ?>
    <div style="<?php echo $divStyle; ?>">
      <a title="<?php echo $se['resource_type']; ?>" name="st<?php echo $typeId; ?>"><h5><?php echo $se['resource_type']; ?></h5></a>
      <img src="<?php echo $pCharts['staffTypeStatusPie'][$typeId] ?>" alt="<?php echo $se['resource_type']; ?> Status Pie Chart">
    </div>
    <?php if ($i % 2 == 0): ?>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
  <?php if ($i % 2 != 0): ?>
    </div>
  <?php endif; ?>
  </div>
  <div style="clear: both;"></div>
</div>
<!-- END TODO -->


<?php endif; ?>