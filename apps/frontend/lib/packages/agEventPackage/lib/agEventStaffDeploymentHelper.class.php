<?php
/**
 * Provides methods related to event staff deployment
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

class agEventStaffDeploymentHelper extends agPdoHelper
{
  const CONN_READ = 'deploy_read';
  const CONN_WRITE = 'deploy_write';

  protected $eventStaffDeployedStatusId;

  /**
   * @var agEventHandler An instance of agEventHandler
   */
  private   $eh;

  public function __construct()
  {
    // instantiate our event handler
    $this->eh = new agEventHandler();

    // grab the new statusid we'll be applying to staff
    $this->eventStaffDeployedStatusId = agStaffAllocationStatus::getEventStaffDeployedStatusId();
  }

  /**
   * Method to return agEventHandler
   * @return agEventHandler This classes' instance of agEventHandler
   */
  public function getEventHandler()
  {
    return $this->eh;
  }

  /**
   * Method to set the import connection object property
   */
  protected function setConnections()
  {
    $this->_conn = array();

    $adapter = Doctrine_Manager::connection()->getDbh();
    $this->_conn[self::CONN_READ] = Doctrine_Manager::connection($adapter, self::CONN_READ);
    $this->_conn[self::CONN_WRITE] = Doctrine_Manager::connection($adapter, self::CONN_WRITE);
  }

  protected function queryShifts()
  {
    // build our basic query
    $q = agDoctrineQuery::create()
      ->select('efg.id')
          ->addSelect('efgs.id')
          ->addSelect('efr.id')
          ->addSelect('efrs.id')
          ->addSelect('es.id')
          ->addSelect('ss.id')
        ->from('agEventFacilityGroup efg')
          ->innerJoin('efg.agEventFacilityGroupStatus efgs')
          ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
          ->innerJoin('efg.agEventFacilityResource efr')
          ->innerJoin('efr.agEventFacilityResourceStatus efrs')
          ->innerJoin('efrs.agFacilityResourceAllocationStatus fras')
          ->innerJoin('efr.agEventShift es')
          ->innerJoin('es.agShiftStatus ss')
        ->where('fgas.active = ?', TRUE)
          ->andWhere('fras.staffed = ?', TRUE)
          ->andWhere('ss.disabled = ?', FALSE );

    // ensure that we only get the most recent group status
    $recentGroupStatus = 'EXISTS (' .
      'SELECT sefgs.id ' .
        'FROM agEventFacilityGroupStatus AS sefgs ' .
        'WHERE sefgs.time_stamp <= CURRENT_TIMESTAMP ' .
          'AND sefgs.event_facility_group_id = efgs.event_facility_group_id ' .
        'HAVING MAX(sefgs.time_stamp) = efgs.time_stamp' .
      ')';
    $q->andWhere($recentGroupStatus);

    // ensure that we only get the most recent facility resource status
    $recentGroupStatus = 'EXISTS (' .
      'SELECT sefrs.id ' .
        'FROM agEventFacilityGroupStatus AS sefrs ' .
        'WHERE sefrs.time_stamp <= CURRENT_TIMESTAMP ' .
          'AND sefrs.event_facility_resource_id = efrs.event_facility_resource_id ' .
        'HAVING MAX(sefrs.time_stamp) = efrs.time_stamp' .
      ')';
    $q->andWhere($recentGroupStatus);

  }

  public function deployEventStaff()
  {

  }

  public function updateEventStaffStatus()
  {

  }

  public function test()
  {

  }
}
