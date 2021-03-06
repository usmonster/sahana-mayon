<?php
  // this file has some odd divs at the end, you have to pay attention to the loops for 2-columns
  // it really is correct, the IDE just can't tell
  $uniqStaffCounts = $sf_data->getRaw('uniqStaffCounts');
  if (!empty($uniqStaffCounts)):
?>

The following are staffing estimates at <?php echo date('Y-m-d H:i:s T', $reportTime); ?>.
<br /><br />

<div class="infoHolder width750Px">
  <h3>Staff Resource Estimates</h3>
  <br />
  <table class="blueTable fullWidthRightText">
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
    <div class="graphDisplay">
      <h5>Staff Resource Distribution By Status</h5>
      <img alt="Staff Status Pie Chart"  src="<?php echo $pCharts['staffStatusPie']; ?>">
    </div>
    <div class="graphDisplay padding10PxLeft">
      <h5>Staff Resource Projections</h5>
      <img alt="Staff Required Bar Chart"  src="<?php echo $pCharts['staffRequiredBar']; ?>">
    </div>
    <div class="clearBoth"></div>
  </div>
  <br />
<?php if ($uniqStaffCounts['non_geo'] > 0): ?>
*Of the <?php echo $uniqStaffCounts['unavailable']; ?> staff resources reported as unavailable, <?php echo $uniqStaffCounts['non_geo']; ?> (<?php echo $uniqStaffCounts['non_geo_pctg']; ?>%) are missing geo-data.
<?php endif; ?>
</div>

<?php $data = $sf_data->getRaw('staffTypeEstimates');
  if(!empty($data)): ?>
<div class="infoHolder width750Px">
  <h3>Staff Type Estimates</h3>
  <br />
  Staff without geo-data are non-deployable. The following estimates exclude staff without this information.
  <br /><br />
  <table class="blueTable fullWidthRightText">
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
        <td class="leftText"><a class="buttonText" title="<?php echo $se['resource_type']; ?>" href="#st<?php echo $typeId; ?>"><?php echo $se['resource_type']; ?></a></td>
        <td><?php echo $se['unknown']; ?></td>
        <td><?php echo $se['unavailable']; ?></td>
        <td><?php echo $se['available']; ?></td>
        <td><?php echo $se['committed']; ?></td>
        <td><?php echo $se['min_required']; ?></td>
      </tr>
      <?php endforeach; ?>

      <tr class="noborder"><td colspan="6"></td></tr>

      <tr>
        <td class="pad1Em">Totals:</td>
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
  <div class="alignMiddle">
    <img alt="Staff Type Required Bar Chart"  src="<?php echo $pCharts['staffTypeRequiredBar']; ?>">
  </div>
  <h4>Staff Resource Minimum Requirements By Type</h4>
  <br />

  <div class="pad1Em">
  <?php
    $i = 0;
    $ct = count($staffTypeEstimates);
    foreach ($staffTypeEstimates AS $typeId => $se):  ?>
      <?php $i++; ?>
      <?php if ($i % 2 != 0): ?>
        <div>
      <?php endif; ?>
      <div class="graphDisplay padding20Px">
        <a title="<?php echo $se['resource_type']; ?>" name="st<?php echo $typeId; ?>"><h5><?php echo $se['resource_type']; ?></h5></a>
        <img src="<?php echo $pCharts['staffTypeStatusPie'][$typeId] ?>" alt="<?php echo $se['resource_type']; ?> Status Pie Chart">
      </div>
      <?php if ($i % 2 == 0 || $i == $ct): ?>
        </div>
      <?php endif; ?>
  <?php endforeach; ?>
  <div class="clearBoth"></div>
  </div>
</div>
<?php endif; ?>

<?php endif; ?>
