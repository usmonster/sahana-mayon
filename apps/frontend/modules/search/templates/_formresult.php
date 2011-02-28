<?php
$data = $obj;
?>
<tr>
<!--  <td>

  </td>-->
  <?php foreach ($data as $column => $value) {
 ?>


    <td>

    <?php if ($column == 'id') { //this should be more verbose, both what's returned and what we check for
    ?>
    <?php
      //$widget->setDefault('add', $value); //$resource_allocation_status', $result['ras_id']);

      // OR ->render(array('class' => 'email')) with the name attribute inserted there.
      //$widget['add']->setAttribute('name', $value);
      echo $widget['add']->render(array('name' =>  'resultform[' . $value . ']')); //set the name of the checkbox to the id of the staff resource to be checked
    ?>
    <?php
    } else {
      echo $value; //get columns for view from actions
      //is column here the actual key? i.e. staff_id ?  it IS id:
    } ?>
  </td>
  <?php } ?>
</tr>