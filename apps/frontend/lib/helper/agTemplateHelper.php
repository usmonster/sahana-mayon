<?php
/**
* builds a table of address data.
*
* @param array of address
* @return string html formatted table of address for display in templates
**/
function buildAddressTable($addressArray)
{
  foreach ($addressArray as $type => $address) {
    $counts[$type] = count($address);
  }

  // Then determine the maximum rows we'll get.
  $maxRows = max($counts);
  // Get the headers for the table. And start building the HTML for the tables header.
  $headers = array_keys($addressArray);
  $tableHead = '<tr>' . PHP_EOL;
  // Set the iterator and build the table rows.
  $i = 0;
  while ($i < $maxRows) {
    // Begin to onstruct the table rows.
    $rows[$i] = '<tr>' . PHP_EOL;
    foreach ($headers as $header) {
      // On the first iteration through, finish creating the table headers.
      if ($i == 0) {
        $tableHead .= '<th class="head">' . ucwords($header) . '</th>' . PHP_EOL;
      }
      $rows[$i] .= '<td>' . (isset($addressArray[$header][$i][0]) ? ($addressArray[$header][$i][0] . '<hr class="ruleGray" /><span class="bold">Last Updated: </span><span class="italic">'  . substr($addressArray[$header][$i][1], 0, 10) . '</span>') : '') . '</td>' . PHP_EOL;
    }
    $rows[$i] .= '</tr>' . PHP_EOL;
    $i++;
  }
  // Close the table header row.
  $tableHead .= '</tr>' . PHP_EOL;
  // Spit it all out.
  $output = $tableHead;
  foreach ($rows as $row) {
    $output .= $row;
  }
  return $output;
}

/**
* Prepares and formats a checkbox table.
*
* @param array   $contents   A two dimensional array, the first level of which contains values that
*                            are associative arrays of object information values. The keys should be
*                            used as the values of $id, $html, and $title.
*
* @param string  $id         The key to the $contents array value that will be used to create the ID for a checkbox.
*
* @param string  $html       The key to the $contents array value that will be used to create the
*                            <label> html for a checkbox.
*
* @param string  $class      A CSS class for the table. Optional.
*
* @param int     $maxColumns An integer that determines the maximum number of columns for the table.
*                            Defaults to 5.
*
* @param string  $idPrepend  A value that will be used to prepend a string to the ID (in case the $con is not
*                            unique enough to serve as such, ie the ID is a numerical value from the DB). This
*                            argument is optional. Optional.
*
* @param string  $title      The key to the $contents array value that will be used to title a checkbox. Optional.
*
*
* @param boolean $toggle     True or false. Determines if the checkToggle classes are added to the checkboxes
*                            and the checkAll checkbox is added as a header. If true, they are. If false, they're not.
*                            Defaults to TRUE.
*
* @param boolean $checked    Determines if the checkboxes (all of them) will be checked or not on page load.
*                            Defaults to false.
*
* @return string             A table with a checkbox in each <td>.
* */
function buildCheckBoxTable(array $contents, $id, $html, $class = null, $maxColumns = 5, $idPrepend = null, $title = null, $toggle = true, $checked = false)
{
  // Check if this table will be making use of the checkToggle js in agMain. If it is, set up the
  // classes and elements that will be used to implement it. Create the header, if necessary, and
  // set it's colspan to $maxColumns.
  if($toggle == true) {
    $input = '<input type="checkbox" name="" id="" class="checkToggle"' . ($checked == true ? ' checked="checked"' : '') . '>';
    $header = '<tr>' . PHP_EOL .
              '<th colspan="' . $maxColumns . '">' . PHP_EOL .
              '<input type="checkbox" name="checkall" id="checkall" value="checkall"' . ($checked == true ? ' checked="checked"' : '') . '>' . PHP_EOL .
              '<label for="checkAll">Select All</label>' . PHP_EOL .
              '</th>' . PHP_EOL . '</tr>';
  } else {
    $input = '<input type="checkbox" name="" id="">';
    $header = null;
  }

  // Define the label opening tag.
  $label = '<label for="">';

  // Go through $contents and populate the inputs and labels with string replacement. Create arrays
  // of inputs and labels.
  foreach($contents as $content) {
    $searchInput = array('name=""', 'id=""');
    $replaceInput = array('name="' . $idPrepend . $content[$id] . '"', 'id="' . $idPrepend . $content[$id] . '"');

    if($title != null) {
      $searchLabel = array('for=""', '>');
      $replaceLabel = array('for="' . $idPrepend . $content[$id] . '"', ' title="' . $content[$title] . '">');
    } else {
      $searchLabel = 'for=""';
      $replaceLabel = 'for="' . $idPrepend . $content[$id] . '"';
    }
    $inputs[] = str_replace($searchInput, $replaceInput, $input);
    $labels[] = str_replace($searchLabel, $replaceLabel, $label) . $content[$html] . '</label>';
  }
  // Use chunk to make the $inputs and $labels arrays into multidimensional arrays, using $maxColumns
  // to determine the number of <td> elements that will be in each row.
  $inputs = array_chunk($inputs, $maxColumns);
  $labels = array_chunk($labels, $maxColumns);

  // Cycle through $inputs and $labels to build <tr>s and <td>s.
  foreach($inputs as $k1 => $inputRow) {
    $rows[$k1] = '<tr>' . PHP_EOL;
    foreach($inputRow as $k2 => $input) {
      $rows[$k1] .= '<td>'  . PHP_EOL . $input . PHP_EOL . $labels[$k1][$k2]  . PHP_EOL . '</td>' . PHP_EOL;
    }
    // Check if the last <tr> of the table has fewer <td>s than the others. If it does, add empty
    // <td>s to give it the same number.
    if(count($inputRow) < $maxColumns) {
      $empty = $maxColumns - count($inputRow);
      for($i = 1; $i <= $empty; $i++) {
        $rows[$k1] .= '<td></td>' . PHP_EOL;
      }
    }
    $rows[$k1] .= '</tr>' . PHP_EOL;
  }

  // Create the actual table.
  // If $class was passed in, add a class to the table. Then attach the header (it might be empty).
  $checkBoxTable = ($class == null ? '<table>' . PHP_EOL : '<table class="' . $class . '">' . PHP_EOL ) . ($header == null ? '' : $header);

  // Add the rows to the table.
  foreach($rows as $row) {
    $checkBoxTable .= $row;
  }
  // close the table and return.
  $checkBoxTable .= '</table>' . PHP_EOL;
  return $checkBoxTable;
}