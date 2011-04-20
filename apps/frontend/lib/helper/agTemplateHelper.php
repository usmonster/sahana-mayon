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