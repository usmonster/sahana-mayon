<table class="staffTable">
  <caption>User Accounts
  </caption>
  <thead>
    <tr class="head">
      <th>Username</th>
      <th>Email</th>
      <th>Created at</th>
      <th>Updated at</th>
      <th>Last Login</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_accounts as $ag_account): ?>
    <tr>
      <td><a href="<?php echo url_for('admin/edit?id='.$ag_account->getId()) ?>"><?php echo $ag_account->getUserName() ?></a></td>
      <td><?php echo $ag_account->getEmailAddress() ?></td>
      <td><?php echo $ag_account->getCreatedAt() ?></td>
      <td><?php echo $ag_account->getUpdatedAt() ?></td>
      <td><?php echo $ag_account->getLastLogin() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br>
<div>
<a href="<?php echo url_for('admin/new') ?>" class="continueButton" title="Create New User Account">Create New</a>
</div>
