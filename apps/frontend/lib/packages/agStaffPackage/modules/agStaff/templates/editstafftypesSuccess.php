<h2>Staff Resource Type List</h2>

<table>
  <thead>
    <tr>
      <th>Staff Resource Type</th>
      <th>Abbreviation</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_staff_types as $ag_staff_type): ?>
    <tr>
      <td><a href="<?php echo url_for('staff/editstafftypes?id='.$ag_staff_type->getId()) ?>" class="continueButton"><?php echo $ag_staff_type->getStaffResourceType() ?></a></td>
      <td><?php echo $ag_staff_type->getStaffResourceTypeAbbr() ?></td>
      <td><?php echo $ag_staff_type->getDescription() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include_partial('stafftypeForm', array('staffTypeForm' => $staffTypeForm)) ?>
