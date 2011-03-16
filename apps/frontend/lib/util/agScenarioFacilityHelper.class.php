<?php

/**
 * provides scenario facility management functions
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

class agScenarioFacilityHelper
{
  /**
   * Method to mass-update scenario facility resource ids
   *
   * @param array $scenarioIds An array of scenario ID's.
   * @param array $facilityResourceIds An array of facility resource ID's.
   * @param integer(2) $allocationStatusId The status id being applied.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array An array containing the number of operations performed.
   */
  public static function setScenarioFacilityResourceAllocationStatus ($scenarioIds, $facilityResourceIds, $allocationStatusId, Doctrine_Connection $conn = NULL)
  {
    $updates = 0 ;

    // grab our facility resource groups
    $scenarioFacilityResourceQuery = agDoctrineQuery::create()
      ->select('sfr.id')
        ->from('agScenarioFacilityResource sfr')
          ->innerJoin('sfr.agScenarioFacilityGroup sfg')
        ->whereIn('sfg.scenario_id', $scenarioIds)
          ->andWhereIn('sfr.facility_resource_id', $facilityResourceIds) ;
    $scenarioFacilityResourceIds = $scenarioFacilityResourceQuery->execute(array(), 'single_value_array') ;

    // query construction
    $updateQuery = agDoctrineQuery::create($conn)
      ->update('agScenarioFacilityResource')
        ->set('facility_resource_allocation_status_id', '?', $allocationStatusId)
        ->whereIn('id', $scenarioFacilityResourceIds) ;

    // set our default connection if one is not passed and wrap it all in a transaction
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }
    $conn->beginTransaction() ;
    try
    {
      // update shifts
      $updates = $updateQuery->execute() ;

      // commit
      $conn->commit() ;
    }
    catch(Exception $e)
    {
      $conn->rollback(); // rollback if we must :(
    }

    return $updates ;
  }
}