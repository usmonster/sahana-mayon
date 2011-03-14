<?php
$data = $obj;
//this is used draw each result as a row, coming from _listform.php
?>
<tr>
<!--  <td>

  </td>-->
  <?php foreach ($data as $column => $value) {
 ?>


    <td>
    <?php
      echo $value; //get columns for view from actions
      //is column here the actual key? i.e. staff_id ?  it IS id:
    ?>
      </td>
  <?php } ?>
      <td>
      <?php
      //$widget->setDefault('add', $value); //$resource_allocation_status', $result['ras_id']);

      // OR ->render(array('class' => 'email')) with the name attribute inserted there.
      //$widget['add']->setAttribute('name', $value);
      echo $widget['add']->render(array('name' =>  'resultform[' . $value . ']')); //set the name of the checkbox to the id of the staff resource to be checked
    ?>

      </td>
</tr>