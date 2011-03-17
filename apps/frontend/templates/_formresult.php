<?php
$data = $obj;
//this is used draw each result as a row, coming from _listform.php

//$columns are our desired columns.. keyed by the name of the value array to echo
//so we need
?>
<tr>

<?php foreach($columns as $display_column => $value){  //key => $value ?>
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
      $default = 0;
      if (isset($data['ess_staff_allocation_status_id'])){
        $default = $data['ess_staff_allocation_status_id'];
        //$widget->setDefault('status', $data['ess_staff_allocation_status_id']);
      }
      $widget->setDefault('status', $default);//ata['ess_staff_allocation_status_id']);
      echo $widget['status'];
      ////->render(array('name' =>  'status[' . $data['es_id'] . ']', 'selected' => $default)); //set the name of the checkbox to the id of the staff resource to be checked
    ?>
      <input type="hidden" name="event_status[event_staff_id]" value="<?php echo $data['es_id'] ?>">
      </td>
</tr>