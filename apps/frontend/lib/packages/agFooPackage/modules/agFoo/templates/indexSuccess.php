<h1>Ag foos List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Foo</th>
      <th>Bar</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_foos as $ag_foo): ?>
    <tr>
      <td><a href="<?php echo url_for('foo/show?id='.$ag_foo->getId()) ?>"><?php echo $ag_foo->getId() ?></a></td>
      <td><?php echo $ag_foo->getFoo() ?></td>
      <td><?php echo $ag_foo->getBar() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('foo/new') ?>">New</a>
