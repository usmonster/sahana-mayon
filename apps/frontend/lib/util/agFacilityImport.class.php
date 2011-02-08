<?php

class agFacilityImport
{

  public function processFacilityImport($importFile)
  {
    // Access Symfony...we'll only need these lines if we need to go the shell_exec route. Might need IReadFilter.php in any case though.
    require_once(dirname(__FILE__) . '/../../../../config/ProjectConfiguration.class.php');
    require_once(dirname(__FILE__) . '/../../../../plugins/sfPhpExcelPlugin/lib/PHPExcel/PHPExcel/Reader/IReadFilter.php');
    require_once(dirname(__FILE__) . '/agReadFilter.class.php');
    require_once(dirname(__FILE__) . '/excel_reader2.php');
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);

    // Same here
    $appConfig = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
    $dbManager = new sfDatabaseManager($appConfig);
    $db = $dbManager->getDatabase('doctrine');

    $data = new Spreadsheet_Excel_Reader($importFile);
    $numRows = $data->rowcount($sheet_index = 0);
    $numCols = $data->colcount($sheet_index = 0);

    $foo = "";

    for ($row = 1; $row <= $numRows; $row++) {

      for ($col = 1; $col <= $numCols; $col++) {
        $foo .= $data->val($row, $col) . ", ";
      }
      $foo .= "\n";
    }
    return $foo;
  }

}

$import = new agFacilityImport();
$output = $import->processFacilityImport($argv[1]);
print $output;
?>