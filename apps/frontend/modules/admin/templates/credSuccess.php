<h3>Credential Management</h3>

<h4>Account Credentials</h4>
<table>
  <thead>
    <tr>
      <th>Credential</th>
      <th>Description</th>
      <th>Owner</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($sf_guard_permissions as $sf_guard_permission): ?>
    <tr>
      <td><a href="<?php echo url_for('admin/cred/?credid='.$sf_guard_user_permission->getId()) ?>" class="linkButton"><?php echo $sf_guard_user_permission->getPermission()->getName() ?></a></td>
      <td><?php echo $sf_guard_user_permission->getPermission()->getDescription() ?></td>
      <td><?php echo $sf_guard_user_permission->getPermission()->getUserId() //can i get account name from here, that would be good?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<h4>Group Credentials</h4>


<h3>Edit/Create Credential</h3>
<?php include_partial('credform', array('form' => $form))
    //our form should allow switching between the type of credential, group or individual
    ?>