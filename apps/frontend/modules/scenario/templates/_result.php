<?php
$data = $obj;
?>
<tr>
  <td>
    <a href="
       <?php echo url_for('search/show?id=' . $data['id']); ?>"
       title="View Sc... <?php echo $data['id']; ?>"
       class="linkButton"><?php print_r($data); ?></a>
  </td>
  <td>
    <?php echo $data['bar']; ?>
  </td>
</tr>