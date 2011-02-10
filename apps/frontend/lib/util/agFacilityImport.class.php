<?php

class agFacilityImport
{

  public function dumpImportFile($importFile)
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

    $xlsObj = new Spreadsheet_Excel_Reader($importFile);
    $numRows = $xlsObj->rowcount($sheet_index = 0);
    $numCols = $xlsObj->colcount($sheet_index = 0);

    $foo = "";

    for ($row = 1; $row <= $numRows; $row++) {

      for ($col = 1; $col <= $numCols; $col++) {
        $foo .= $xlsObj->val($row, $col) . ", ";
      }
      $foo .= "\n";
    }
    return $foo;
  }

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

    $xlsObj = new Spreadsheet_Excel_Reader($importFile);
    $numRows = $xlsObj->rowcount($sheet_index = 0);
    $numCols = $xlsObj->colcount($sheet_index = 0);

    // Create a simplified array from
    for ($row = 1; $row <= $numRows; $row++) {

      for ($col = 1; $col <= $numCols; $col++) {
        $importData[$row][$xlsObj->val(1, $col)] = $xlsObj->val($row, $col);
      }
    }

    $this->saveFacilityImport($importData);
  }

  public function saveFacilityImport($importFacility)
  {

    foreach ($importFacility as $import) {

      //echo $import["Facility Code"] ."-" . strlen($import["Facility Code"]) . "\n";
      $facilityCode = $import["Facility Code"];
        
      // Basic/Primary
      $entity = new agEntity();
      $entity->save();
      $site = new agSite();
      $site->entity_id = $entity->id;
      $site->save();
      $facility = new agFacility();
      $facility->site_id = $site->id;
      $facility->facility_name = $import["Facility Name"];
      $facility->facility_code = substr($import["Facility Code"],7);
      $facility->save();
      
    }
  }

  function dumpFacilities()
  {
    require_once(dirname(__FILE__) . '/../../../../config/ProjectConfiguration.class.php');
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
    $appConfig = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
    $dbManager = new sfDatabaseManager($appConfig);
    $db = $dbManager->getDatabase('doctrine');

    $facRes = Doctrine_Query::create()
            ->select('f.facility_name, f.facility_code, frt.facility_resource_type_abbr, frs.facility_resource_status, fr.capacity')
            ->addSelect('sfr.activation_sequence, fras.facility_resource_allocation_status')
            ->addSelect('sfg.scenario_facility_group, fgt.facility_group_type, fgas.facility_group_allocation_status, sfg.activation_sequence')
            ->addSelect('gc.longitude, gc.latitude')
            ->from('agFacility f')
            ->innerJoin('f.agSite s')
            ->innerJoin('s.agEntity e')
            ->innerJoin('f.agFacilityResource fr')
            ->innerJoin('fr.agFacilityResourceType frt')
            ->innerJoin('fr.agFacilityResourceStatus frs')
            ->innerJoin('fr.agScenarioFacilityResource sfr')
            ->innerJoin('sfr.agFacilityResourceAllocationStatus fras')
            ->innerJoin('sfr.agScenarioFacilityGroup sfg')
            ->innerJoin('sfg.agFacilityGroupType fgt')
            ->innerJoin('sfg.agFacilityGroupAllocationStatus fgas')
            ->innerJoin('e.agEntityAddressContact eac')
            ->innerJoin('eac.agAddressContactType act ON act.address_contact_type=?', 'work')
            ->innerJoin('eac.agAddress a')
            ->innerJoin('a.agAddressGeo ag')
            ->innerJoin('ag.agGeo g')
            ->innerJoin('g.agGeoSource gs')
            ->innerJoin('g.agGeoFeature gf')
            ->innerJoin('gf.agGeoCoordinate gc')
            ->where('sfg.scenario_id=?', '1')
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $foo = "";
    foreach ($facRes as $row) {
      foreach ($row as $val) {
        print("$val,");
      }
      print("\n");
    }
  }

  // importName() *******************
  private function importFacilityName($facility, $point)
  {
    $nameCheck =
            Doctrine_core::getTable('agFacility')
            ->findByDql('facility_name = ?', $point)
            ->getFirst();
    if (!($nameCheck instanceof agFacility)) {
      $nameCheck = new agFacility();
      $nameCheck->facility_name = $point;
      $nameCheck->save();
    }
  }

}

$import = new agFacilityImport();
//$output = $import->dumpImportFile($argv[1]);
//print("$output\n");
//print("---------------------------\n");

$test = new agFacilityImport();
$output = $test->processFacilityImport($argv[1]);
$output = $test->dumpFacilities();
?>
