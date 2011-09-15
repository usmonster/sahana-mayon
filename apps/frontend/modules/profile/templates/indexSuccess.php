<table class="staffTable">
	<caption>Web Service Cients</caption>
  <thead>
    <tr class="head">
      <th>Id</th>
      <th>User</th>
      <th>Token</th>
      <th>Is webservice client</th>
      <th>Is active</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($sf_guard_user_profiles as $sf_guard_user_profile): ?>
    <tr>
      <td><a href="<?php echo url_for('profile/edit?id='.$sf_guard_user_profile->getId()) ?>"><?php echo $sf_guard_user_profile->getId() ?></a></td>
      <td><?php echo $sf_guard_user_profile->getUserId() ?></td>
      <td><?php echo $sf_guard_user_profile->getToken() ?></td>
      <td><?php echo $sf_guard_user_profile->getIsWebserviceClient() ?></td>
      <td><?php echo $sf_guard_user_profile->getIsActive() ?></td>
      <td><?php echo $sf_guard_user_profile->getCreatedAt() ?></td>
      <td><?php echo $sf_guard_user_profile->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br>
<div>
  <a href="<?php echo url_for('profile/new') ?>" class="continueButton">New</a>
</div>