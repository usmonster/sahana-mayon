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
    $q = agDoctrineQuery::create()
      ->select('efg.id')
          ->addSelect('')
        ->from('agEventFacilityGroup efg')
          ->innerJoin('efg.agEventFacilityGroupStatus efgs'),
          ->innerJoin('efg.agEventFacilityResource efr'),
          ->innerJoin('efrs.agEventFacilityResourceStatus efrs'),
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
