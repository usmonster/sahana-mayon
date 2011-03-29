<?php
/**
* agTemplateHelper class
*
* PHP Version 5.3
*
* LICENSE: This source file is subject to LGPLv2.1 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/licenses/lgpl-2.1.html
*
* @author     Nils Stolpe, CUNY SPS
* @author     Pradeep Vijayagiri, CUNY SPS
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
**/
class agTemplateHelper
{
  /**
   *
   * @param array of address
   * @return string html formatted table of address for display in templates
   */
  public static function buildAddressTable($addressArray)
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
  //start p-code
  public static function include_customTitle()
  {
    $title = sfContext::getInstance()->getResponse()->getTitle();
    $titleinfo = sfConfig::get('app_title');
    $uri= str_replace("/frontend_dev.php/", "/", $_SERVER['REQUEST_URI']);
    foreach ($titleinfo as $titlename){
      if($titlename['url'] == $uri){
       $title = $titlename['title'];
         }
    }
    echo content_tag('title', $title)."\n";
  }
    //end p-code
}

?>
