<?php
$data = $obj;
?>
<tr>
  <td>
    <a href="
    <?php
    //$zoo = $sf_request->getParameterHolder();
    // TODO fix the below to take in the module for show or edit action appendagism
    echo url_for($target_module . '/show?id=' . $data['id']); ?>"
       title="View Sc... <?php echo $data['id']; ?>"
       class="continueButton"></a>
  </td>
  <?php foreach ($data as $column => $value) {
 ?>
         <td>
<?php echo $value; //get columns for view from actions ?>
       </td>
<?php } ?>
</tr>