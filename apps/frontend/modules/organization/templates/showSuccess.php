<h2>Organization Details</h2>

<br
  <table class="singleTable">
  <thead>
  <h3>Organization <a href="'<?php url_for('@wiki'); ?>'/doku.php?id=tooltip:organization&do=export_xhtmlbody" class="tooltipTrigger" title="Organization">?</a></h3>
</thead>
<tbody>
  <tr>
    <th class="headLeft">Id:</th>
    <td><?php echo $ag_organization->getId() ?></td>
  </tr>
  <tr>
    <th class="headLeft">Organization:</th>
    <td><?php echo $ag_organization->getOrganization() ?></td>
  </tr>
  <tr>
    <th class="headLeft">Description:</th>
    <td><?php echo $ag_organization->getDescription() ?></td>
  </tr>
</tbody>
</table>

<br>

<table class="singleTable">
  <thead>
  <h3>Staff Count <a href="'<?php url_for('@wiki'); ?>'/doku.php?id=tooltip:organization_staff_count&do=export_xhtmlbody" class="tooltipTrigger" title="Staff Count">?</a></h3>
</thead>
<tbody>
  <?php foreach ($staffResourceTypes as $stfResTypeId => $stfResType): ?>
    <tr>
      <th class="headLeft"><?php echo $stfResType . ':'; ?></th>
      <td>
      <?php
      $uniqueStaffCountArray = $sf_data->getRaw('uniqueStaffCount');
      $orgId = $ag_organization->getId();

      if (array_key_exists($orgId, $uniqueStaffCountArray)) {
        if (array_key_exists($stfResTypeId, $uniqueStaffCountArray[$orgId])) {
          echo $uniqueStaffCountArray[$orgId][$stfResTypeId];
        } else {
          echo 0;
        }
      } else {
        echo 0;
      }
      ?>
    </td>
  </tr>
<?php endforeach; ?>
  <tr>
    <th class="headLeft">Total Unique Staff:</th>
    <td><?php echo $totalStaffCount; ?></td>
      </tr>
    </tbody>
    </table>

    <br>

  <a href="<?php echo url_for('organization/edit?id=' . $ag_organization->getId()) ?>" class="continueButton" >Edit</a>
  &nbsp;
  <a href="<?php echo url_for('organization/list') ?>" class="generalButton" >List</a>
