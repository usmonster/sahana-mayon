<?php
  $data = $sf_data->getRaw('data');
  $displayColumns = $sf_data->getRaw('displayColumns');
  $display_columns = array_keys($displayColumns);

  foreach ($data as $row)
  {
    echo '<tr>';

    foreach ($display_columns AS $key => $column)
    {
      echo '<td>';
      $value = $row[$displayColumns[$column]['index']];
      if ($column == 'id') {
        echo link_to('Edit', $targetModule . '/edit?id=' . $value, array('class' => 'continueButton', 'title' => 'Edit'));
        echo link_to('Show', $targetModule . '/show?id=' . $value, array('class' => 'generalButton', 'title' => 'Show'));
      } elseif($column == 'emails') {
        if($value != '---'){
          echo '<a class="linkText" href="mailto:' . $value . '">' . $value . '</a>';
        }
        else{
          echo $value;
        }
      }
      else{
        echo $value;
      }

      echo '</td>';
    }

    echo '</tr>';
  }
  ?>