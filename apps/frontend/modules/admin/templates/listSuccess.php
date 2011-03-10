<table class="staffTable">
  <caption>User Accounts
  </caption>
  <thead>
    <tr>
      <th>Username</th>
      <th>Email</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_accounts as $ag_account): ?>
    <tr>
      <td><a href="<?php echo url_for('admin/edit?id='.$ag_account->getId()) ?>"><?php echo $ag_account->getUserName() ?></a></td>
      <td><?php echo $ag_account->getEmailAddress() ?></td>
      <td><?php echo $ag_account->getCreatedAt() ?></td>
      <td><?php echo $ag_account->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<a href="<?php echo url_for('admin/new') ?>" class="linkButton" title="Create New User Account">Create New</a>

