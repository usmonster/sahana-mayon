<?php
$data = $obj;//->getRaw();
//$column_array = $displayColumns->toArray();

//this is used draw each result as a row, coming from _list.php
//$columns are our desired columns.. keyed by the name of the value array to echo
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
<?php }
?>
</tr>