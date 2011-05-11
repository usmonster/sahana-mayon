<h3>Facility Group Type List</h3>

<table>
  <thead>
    <tr>
      <th>Facility Group Type</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_facility_group_types as $ag_facility_group_type): ?>
      <tr>
        <td><a href="<?php echo url_for('scenario/editgrouptype?id=' . $ag_facility_group_type->getId()) ?>" class="continueButton">
          <?php echo $ag_facility_group_type->getFacilityGroupType() ?></a></td>
      <td><?php echo $ag_facility_group_type->getDescription() ?></td>
    </tr>
    <?php endforeach; ?>
        </tbody>
      </table>

      <h3>Edit Facility Group Type</h3>
<?php include_partial('grouptypeform', array('grouptypeform' => $grouptypeform)) ?>