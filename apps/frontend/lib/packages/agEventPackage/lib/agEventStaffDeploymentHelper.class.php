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
  const QUERY_SHIFTS = 'query_shifts';


  protected $eventStaffDeployedStatusId,
            $eventFacilityGroups = array();

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

    // set our connections
    $this->setConnections();
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

  public function getEventFacilityGroups($eventId)
  {

  }

  protected static function _getEventFacilityGroups($eventId)
  {
    $q = agDoctrineQuery::create()
      ->select('efg.id')
        ->from('agEventFacilityGroup efg')
          ->innerJoin('efg.agEventFacilityGroupStatus efgs')
          ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
        ->where('efg.event_id = ?', $eventId)
          ->andWhere('fgas.active = ?', TRUE)
        ->orderBy('efg.activation_sequence ASC');

    // ensure that we only get the most recent group status
    $recentGroupStatus = 'EXISTS (' .
      'SELECT sefgs.id ' .
        'FROM agEventFacilityGroupStatus AS sefgs ' .
        'WHERE sefgs.time_stamp <= CURRENT_TIMESTAMP ' .
          'AND sefgs.event_facility_group_id = efgs.event_facility_group_id ' .
        'HAVING MAX(sefgs.time_stamp) = efgs.time_stamp' .
      ')';
    $q->andWhere($recentGroupStatus);

    $this->eventFacilityGroups = $q->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);
  }

  protected function queryShifts($eventId)
  {
    // @todo add min start to facility activation
    // @todo break apart into facgrps
    // @todo figure out how many total people of each type are needed at a fac simultaneously
    // @todo add sums of min/max and other shift-info
    // @todo add max prio address geo
    // build our basic query
    $q = agDoctrineQuery::create()
      ->select('efg.id')
          ->addSelect('efr.id')
          ->addSelect('es.id')
        ->from('agEventFacilityGroup efg')
          ->innerJoin('efg.agEventFacilityGroupStatus efgs')
          ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
          ->innerJoin('efg.agEventFacilityResource efr')
          ->innerJoin('efr.agEventFacilityResourceActivationTime')
          ->innerJoin('efr.agEventFacilityResourceStatus efrs')
          ->innerJoin('efrs.agFacilityResourceAllocationStatus fras')
          ->innerJoin('efr.agEventShift es')
          ->innerJoin('es.agShiftStatus ss')
        ->where('efg.event_id = ?')
          ->andWhere('fgas.active = ?')
          ->andWhere('fras.staffed = ?')
          ->andWhere('ss.disabled = ?')
        ->orderBy('efg.activation_sequence ASC')
          ->addOrderBy('efr.activation_sequence ASC')
          ->addOrderBy('es.staff_wave ASC');

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
    $recentFacRscStatus = 'EXISTS (' .
      'SELECT sefrs.id ' .
        'FROM agEventFacilityResourceStatus AS sefrs ' .
        'WHERE sefrs.time_stamp <= CURRENT_TIMESTAMP ' .
          'AND sefrs.event_facility_resource_id = efrs.event_facility_resource_id ' .
        'HAVING MAX(sefrs.time_stamp) = efrs.time_stamp' .
      ')';
    $q->andWhere($recentFacRscStatus);

    // set our indexed params
    $params = array(TRUE, FALSE);

    // grab a connection
    $conn = $this->getConnection(self::CONN_READ);

    // grab the sql query and pass it to pdo
    $sql = $q->getSqlQuery();
    $pdoStmt = $this->executePdoQuery($conn, $sql, $params, NULL, self::QUERY_SHIFTS);
    
    return $pdoStmt;
  }

  public function deployEventStaff()
  {

  }

  public function updateEventStaffStatus()
  {

  }

  public function test()
  {
    return $this->queryShifts(7);
  }
}
