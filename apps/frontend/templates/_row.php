<?php
$data = $obj; //->getRaw();
//$column_array = $displayColumns->toArray();
//this is used draw each result as a row, coming from _list.php
//$columns are our desired columns.. keyed by the name of the value array to echo
foreach ($displayColumns as $key => $value) {
  $display_columns[] = $key;
}
?>
<tr>

  <?php
  foreach ($data as $column => $value) {
//array_keys
    if (in_array($column, $display_columns)) {
  ?>
      <td><?php
      if ($column == 'id') {
        echo link_to('Edit', $target_module . '/edit?id=' . $data['id'], array('class' => 'linkButton', 'title' => 'Edit'));
        echo link_to('Show', $target_module . '/show?id=' . $data['id'], array('class' => 'linkButton', 'title' => 'Show'));
      } elseif($column == 'emails') {
        if($value != '---'){
          echo '<a href="mailto:' . $value . '">' . $value . '</a>';
        }
        else{
          echo $value;
        }
      }
      else{
        echo $value;
      }
  ?></td>
  <?php
    }
  }
  ?>
</tr>