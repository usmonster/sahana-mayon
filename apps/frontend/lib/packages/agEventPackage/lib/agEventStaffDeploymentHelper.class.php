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


  protected $eventId,
            $eventStaffDeployedStatusId,
            $eventFacilityGroups = array(),
            $addrGeoTypeId,
            $shiftOffset;

  /**
   * @var agEventHandler An instance of agEventHandler
   */
  private   $eh;

  /**
   * Method to return an instance of agEventStaffDeploymentHelper
   * @param integer $eventId An event ID
   * @return self An instance of agEventStaffDeploymentHelper
   */
  public static function getInstance($eventId)
  {
    $esdh = new self();
    $esdh->__init($eventId);
    return $esdh;
  }

  /**
   * A method to act like this classes' own constructor
   * @param integer $eventId An event ID
   */
  protected function __init($eventId)
  {
    // instantiate our event handler
    $this->eh = new agEventHandler();

    // set our connections
    $this->setConnections();

    // grab some global defaults
    $this->addrGeoTypeId = agGeoType::getAddressGeoTypeId();
    $this->eventStaffDeployedStatusId = agStaffAllocationStatus::getEventStaffDeployedStatusId();
    $this->shiftOffset = agGlobal::getParam('shift_change_restriction');

    // get our event facility groups loaded and ready to process
    $this->getEventFacilityGroups();
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

  public function processBatch($continue = TRUE)
  {

  }

  /**
   * Method to query the database for event facility groups and set the class property.
   */
  protected function getEventFacilityGroups()
  {
    $q = agDoctrineQuery::create()
      ->select('efg.id')
        ->from('agEventFacilityGroup efg')
          ->innerJoin('efg.agEventFacilityGroupStatus efgs')
          ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
        ->where('efg.event_id = ?', $this->eventId)
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

  protected function getFacGrpShifts($eventFacilityGroupId)
  {
    // build our basic query
    $q = agDoctrineQuery::create()
      ->select('es.id')
          ->addSelect('es.event_facility_resource_id')
          ->addSelect('es.minimum_staff')
          ->addSelect('es.maximum_staff')
          ->addSelect('gc.latitude')
          ->addSelect('gc.longitude')
        ->from('agEventShift es')
          ->innerJoin('es.agShiftStatus ss')
          ->innerJoin('es.agEventFacilityResource efr')
          ->innerJoin('efr.agEventFacilityResourceActivationTime')
          ->innerJoin('efr.agEventFacilityResourceStatus efrs')
          ->innerJoin('efrs.agFacilityResourceAllocationStatus fras')
          ->innerJoin('efr.agFacilityResource fr')
          ->innerJoin('fr.agFacilityResourceStatus frs')
          ->innerJoin('fr.agFacility f')
          ->innerJoin('f.agSite s')
          ->innerJoin('s.agEntity e')
          ->innerJoin('e.agEntityAddressContact eac')
          ->innerJoin('eac.agAddress a')
          ->innerJoin('a.agAddressGeo ag')
          ->innerJoin('ag.agGeo g')
          ->innerJoin('g.agGeoFeature gf')
          ->innerJoin('gf.agGeoCoordinate gc')
        ->where('fras.staffed = ?', TRUE)
          ->andWhere('frs.is_available = ?', TRUE)
          ->andWhere('ss.disabled = ?', FALSE)
          ->andWhere('efr.event_facility_group_id = ?', $eventFacilityGroupId)
          ->andWhere('g.geo_type_id = ?', $this->addrGeoTypeId)
        ->orderBy('es.staff_wave ASC')
          ->addOrderBy('efr.activation_sequence ASC');
 
    // restrict ourselves only to allocatable shifts
    $allocatableShifts = '(es.minutes_start_to_facility_activation - ' . $this->shiftOffset .
      ') <= CURRENT_TIMESTAMP';
    $q->andWhere($allocatableShifts);


    // ensure that we only get the most recent facility resource status
    $recentFacRscStatus = 'EXISTS (' .
      'SELECT sefrs.id ' .
        'FROM agEventFacilityResourceStatus AS sefrs ' .
        'WHERE sefrs.time_stamp <= CURRENT_TIMESTAMP ' .
          'AND sefrs.event_facility_resource_id = efrs.event_facility_resource_id ' .
        'HAVING MAX(sefrs.time_stamp) = efrs.time_stamp' .
      ')';
    $q->andWhere($recentFacRscStatus);

    // just pick up the lowest priority facility address
    $minFacAddr = 'EXISTS (' .
      'SELECT seac.id ' .
        'FROM agEntityAddressContact AS seac ' .
        'WHERE seac.id = eac.id ' .
        'HAVING MIN(seac.priority) = eac.priority' .
      ')';
    $q->andWhere($minFacAddr);

    // grab a connection
    $conn = $this->getConnection(self::CONN_READ);

    // grab the query components and pass it to pdo
    $sql = $q->getSqlQuery();
    $params = $q->getParams();
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
    return $this->eventFacilityGroups;
  }
}
