<?php
$data = $obj;//->getRaw();
//$column_array = $displayColumns->toArray();

//this is used draw each result as a row, coming from _listform.php
//$columns are our desired columns.. keyed by the name of the value array to echo
//so we need
foreach($displayColumns as $key => $value)
{
  $display_columns[] = $key;
}
?>
<tr>

<?php foreach ($data as $column => $value) {

//array_keys
  ?>
    <?php
    if(in_array($column, $display_columns)){
      ?>
    <td>
<?php
      echo $value;
?>
      </td>
      <?php } ?>
<?php } ?>
  <td>
<?php
  //$widget->setDefault('add', $value); //$resource_allocation_status', $result['ras_id']);
  // OR ->render(array('class' => 'email')) with the name attribute inserted there.
  //$widget['add']->setAttribute('name', $value);
  $default = 0;
  if (isset($data['ess_staff_allocation_status_id'])) {
    $default = $data['ess_staff_allocation_status_id'];
    //$widget->setDefault('status', $data['ess_staff_allocation_status_id']);
  }
  $widget->setDefault('status', $default); //ata['ess_staff_allocation_status_id']);
  echo $widget['status'];
  ////->render(array('name' =>  'status[' . $data['es_id'] . ']', 'selected' => $default)); //set the name of the checkbox to the id of the staff resource to be checked
?>
    <input type="hidden" name="event_status[][event_staff_id]" value="<?php echo $data['es_id'] ?>">
  </td>
</tr>