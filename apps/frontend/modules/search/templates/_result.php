<?php
$data = $obj;
?>
<tr>
  <td>
    <a href="
    <?php
    // TODO fix the below to take in the module for show or edit action appendagism
    echo url_for($sf_request->getParameter('module') . '/show?id=' . $data['id']); ?>"
       title="View Sc... <?php echo $data['id']; ?>"
       class="linkButton"></a>
  </td>
  <?php foreach ($data as $column => $value) {
 ?>
         <td>
<?php echo $value; //get columns for view from actions ?>
       </td>
<?php } ?>
</tr>