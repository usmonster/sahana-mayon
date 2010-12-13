<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $ag_account->getId() ?></td>
    </tr>
    <tr>
      <th>Account name:</th>
      <td><?php echo $ag_account->getAccountName() ?></td>
    </tr>
    <tr>
      <th>Account status:</th>
      <td><?php echo $ag_account->getAccountStatusId() ?></td>
    </tr>
    <tr>
      <th>Description:</th>
      <td><?php echo $ag_account->getDescription() ?></td>
    </tr>
    <tr>
      <th>Created at:</th>
      <td><?php echo $ag_account->getCreatedAt() ?></td>
    </tr>
    <tr>
      <th>Updated at:</th>
      <td><?php echo $ag_account->getUpdatedAt() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('admin/edit?id='.$ag_account->getId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('admin/index') ?>">List</a>
