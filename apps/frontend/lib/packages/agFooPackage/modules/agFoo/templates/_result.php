<?php
$data = $obj;
?>
<tr>
  <td>
    <a href="
       <?php echo url_for('foo/show?id=' . $data['id']); ?>"
       title="View Foo <?php echo $data['id']; ?>"
       class="linkButton"><?php echo $data['foo']; ?></a>
  </td>
  <td>
    <?php echo $data['bar']; ?>
  </td>
</tr>