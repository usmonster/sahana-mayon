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
* @return string
* */
function buildCheckBoxTable(array $contents, $id, $html, $class = NULL, $maxColumns = 5, $idPrepend = NULL, $title = NULL, $toggle = TRUE)
{
  $rows = count($contents);

  // Construct some rows.
  foreach($contents as $content) {
    $row[] = '<td>\n<input type="checkbox" name="'
               . ($idPrepend != NULL ? $idPrepend . $content[$id] : $content[$id])
               . '" id="'  . ($idPrepend != NULL ? $idPrepend . $content[$id] : $contend[$id])
               . '"'. ($toggle == TRUE ? ' class="checkToggle">\n' : '>\n')
               . '<label'. ($title != NULL ? ' title="'. $content[$title] . '" ' : ' ') . '>' . $content[$html] . '</label>\n</td>';
  }
  return $checkBoxTable;
}