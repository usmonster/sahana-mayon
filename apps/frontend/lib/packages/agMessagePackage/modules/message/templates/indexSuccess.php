<h1>Ag message templates List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Message template</th>
      <th>Message type</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_message_templates as $ag_message_template): ?>
    <tr>
      <td><a href="<?php echo url_for('message/edit?id='.$ag_message_template->getId()) ?>"><?php echo $ag_message_template->getId() ?></a></td>
      <td><?php echo $ag_message_template->getMessageTemplate() ?></td>
      <td><?php echo $ag_message_template->getMessageTypeId() ?></td>
      <td><?php echo $ag_message_template->getCreatedAt() ?></td>
      <td><?php echo $ag_message_template->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('message/new') ?>">New</a>
