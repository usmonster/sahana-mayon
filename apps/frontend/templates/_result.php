<?php
$data = $obj;
?>
<tr>
  <td>
    <a href="
       <?php echo url_for('search/show?id=' . $data['id']); ?>"
       title="View Sc... <?php echo $data['id']; ?>"
       class="linkButton"></a>
  </td>
  <?php foreach($data as $column => $value){ ?>
  <td>
    <?php echo $value; //get columns for view from actions?>
  </td>
  <?php } ?>
</tr>