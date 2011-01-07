<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $ag_foo->getId() ?></td>
    </tr>
    <tr>
      <th>Foo:</th>
      <td><?php echo $ag_foo->getFoo() ?></td>
    </tr>
    <tr>
      <th>Bar:</th>
      <td><?php echo $ag_foo->getBar() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('foo/edit?id='.$ag_foo->getId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('foo/index') ?>">List</a>
