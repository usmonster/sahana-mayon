<?php

/**
 * provides scenario facility management functions
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
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

  public static function deleteScenarioFacilityResource( $scenarioFacilityResourceId, Doctrine_Connection $conn = NULL)
  {
    $results = 0;

    // define our three querys
    $q1 = agDoctrineQuery::create()
      ->delete('agScenarioShift')
        ->where('scenario_facility_resource_id = ?', $scenarioFacilityResourceId);

    $q2 = agDoctrineQuery::create()
      ->delete('agFacilityStaffResource')
        ->where('scenario_facility_resource_id = ?', $scenarioFacilityResourceId);

    $q3 = agDoctrineQuery::create()
      ->delete('agScenarioFacilityResource')
        ->where('id = ?', $scenarioFacilityResourceId);

    // get our connection if not passed one and start a transaction or savepoint
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }

    try
    {
      $results = $q1->execute();
      $results = ($q2->execute() + $results);
      $results = ($q3->execute() + $results);
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = ('Could not set DELETE scenario facility resource ' . $scenarioFacilityResourceId);

      // log our error
      sfContext::getInstance()->getLogger()->err($errMsg) ;

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

      throw $e;
    }

    return $results;
  }
}