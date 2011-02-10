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
    for ($row = 2; $row <= $numRows; $row++) {

      for ($col = 1; $col <= $numCols; $col++) {
        $importData[$row][$xlsObj->val(1, $col)] = $xlsObj->val($row, $col);
      }
    }

    $this->saveFacilityImport($importData);
  }

  public function saveFacilityImport($importFacility)
  {
    require_once(dirname(__FILE__) . '/../../../../config/ProjectConfiguration.class.php');
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
    $appConfig = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
    $dbManager = new sfDatabaseManager($appConfig);
    $db = $dbManager->getDatabase('doctrine');

    $conn = Doctrine_Manager::connection();
    
    $foo = Doctrine_Query::create()
            ->select('t.*')
            ->from('temp_facilityImport t')
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    
    foreach ($importFacility as $import) {

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

  // incrementFacilityName() *******************
  private function incrementFacilityName($facilityName)
  {
    $nameCheck =
            Doctrine_core::getTable('agFacility')
            ->findByDql('facility_name = ?', $facilityName)
            ->getFirst();
    if (!($nameCheck instanceof agFacility)) {
      $name = $facilityName;
    } else {
      $dupNameCount = $nameCheck->count() + 1;
      $name = $facilityName . " ($dupNameCount)";
    }
    return $name;
  }

  public function createTempTable()
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

    $conn = Doctrine_Manager::connection();

    // Drop the table
    try {
      $conn->export->dropTable('temp_facilityImport');
    } catch (Doctrine_Exception $e) {

    }

    $columns = array(
      'id' => array(
        'type' => 'integer',
        'autoincrement' => true,
        'primary' => true
      ),
      'facility_name' => array(
        'type' => 'string',
        'length' => 64
      ),
      'facility_code' => array(
        'type' => 'string',
        'length' => 10
      ),
      'facility_resource_type_abbr' => array(
        'type' => 'string',
        'length' => 10
      ),
      'facility_resource_status' => array(
        'type' => 'string',
        'length' => 40
      ),
      'capacity' => array(
        'type' => 'integer'
      ),
      'activation_sequence' => array(
        'type' => 'integer'
      ),
      'facility_resource_allocation_status' => array(
        'type' => 'string',
        'length' => 30
      ),
      'scenario_facility_group' => array(
        'type' => 'string',
        'length' => 64
      ),
      'facility_group_type' => array(
        'type' => 'string',
        'length' => 30
      ),
      'facility_group_allocation_status' => array(
        'type' => 'string',
        'length' => 30
      ),
      'email_contact' => array(
        'type' => 'string',
        'length' => 255
      ),
      'phone_contact' => array(
        'type' => 'string',
        'length' => 16
      ),
      'street_1' => array(
        'type' => 'string',
        'length' => 255
      ),
      'street_2' => array(
        'type' => 'string',
        'length' => 255
      ),
      'city' => array(
        'type' => 'string',
        'length' => 255
      ),
      'state' => array(
        'type' => 'string',
        'length' => 2
      ),
      'zip_code' => array(
        'type' => 'string',
        'length' => 255
      ),
      'borough' => array(
        'type' => 'string',
        'length' => 255
      ),
      'country' => array(
        'type' => 'string',
        'length' => 128
      ),
      'latitude' => array(
        'type' => 'decimal'
      ),
      'longitude' => array(
        'type' => 'decimal'
      )
    );

    $options = array(
      'type' => 'MYISAM',
      'charset' => 'utf8'
    );

    $conn->export->createTable('temp_facilityImport', $columns, $options);
  }

}

//$import = new agFacilityImport();
//$output = $import->dumpImportFile($argv[1]);
//print("$output\n");
//print("---------------------------\n");

$test = new agFacilityImport();
//$output = $test->processFacilityImport($argv[1]);
//$output = $test->dumpFacilities();
$output = $test->createTempTable();
$output = $test->processFacilityImport($argv[1]);

?>
