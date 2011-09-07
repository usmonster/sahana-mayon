<?php

/**
 * PluginagEvent
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginagEvent extends BaseagEvent
{

  /**
   * Method to get the event zero hour
   * @param integer $eventId The event ID being queried
   * @return integer A unix timestamp representing the event zero hour
   */
  public static function getEventZeroHour($eventId)
  {
    $q = agDoctrineQuery::create()
      ->select('e.zero_hour')
        ->from('agEvent e')
        ->where('e.id = ?', $eventId);

    return $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  /**
   * Function to return the event_status_id and event_status_type_id of the passed event_id
   *
   * @param  integer(4) $eventId The event id being queried
   * @return integer    An event_status_type_id.
   */
  public static function getCurrentEventStatus($eventId)
  {
    return agDoctrineQuery::create()
      ->select('es.event_status_type_id')
      ->from('agEventStatus es')
      ->where('es.event_id = ?', $eventId)
      ->andWhere(
              'EXISTS (
          SELECT s.id
            FROM agEventStatus s
            WHERE s.event_id = es.event_id
              AND s.time_stamp <= CURRENT_TIMESTAMP
            HAVING MAX(s.time_stamp) = es.time_stamp)')
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  /**
   * Function to return the event_status_id and event_status_type_id of the passed event_id
   * @return integer    An event_status_type_id.
   */
  public function getCurrentStatus()
  {
    return self::getCurrentEventStatus($this->id);
  }

  /**
   * Method to get event facility data (useful for exports)
   * @param integer $eventId An event ID
   * @return array An array of event facility data
   */
  public static function getEventFacilities($eventId)
  {
    $results = array();

    $rstatExists = 'EXISTS (SELECT subEfrs.id ' .
      'FROM agEventFacilityResourceStatus subEfrs ' .
      'WHERE subEfrs.time_stamp <= CURRENT_TIMESTAMP ' .
        'AND subEfrs.event_facility_resource_id = efrs.event_facility_resource_id ' .
      'HAVING MAX(subEfrs.time_stamp) = efrs.time_stamp)';

    $gstatExists = 'EXISTS (SELECT subEfgs.id ' .
      'FROM agEventFacilityGroupStatus subEfgs ' .
      'WHERE subEfgs.time_stamp <= CURRENT_TIMESTAMP ' .
        'AND subEfgs.event_facility_group_id = efgs.event_facility_group_id ' .
      'HAVING MAX(subEfgs.time_stamp) = efgs.time_stamp)';

    $eExists = '((EXISTS (' .
        'SELECT subE.id ' .
        'FROM agEntityEmailContact AS subE ' .
        'WHERE subE.entity_id = eec.entity_id ' .
        'HAVING MIN(subE.priority) = eec.priority' .
        ')) ' .
        'OR (eec.id IS NULL)' .
        ')';

    $pExists = '(' .
        '(EXISTS (' .
        'SELECT subP.id ' .
        'FROM agEntityPhoneContact AS subP ' .
        'WHERE subP.entity_id = epc.entity_id ' .
        'HAVING MIN(subP.priority) = epc.priority' .
        ')) ' .
        'OR (epc.id IS NULL)' .
        ')';

    $q = agDoctrineQuery::create()
      ->select('e.id')
          ->addSelect('eec.id')
          ->addSelect('ec.id')
          ->addSelect('ec.email_contact')
          ->addSelect('epc.id')
          ->addSelect('pc.phone_contact')
          ->addSelect('s.id')
          ->addSelect('f.id')
          ->addSelect('f.facility_name')
          ->addSelect('f.facility_code')
          ->addSelect('fr.id')
          ->addSelect('fr.capacity')
          ->addSelect('frt.id')
          ->addSelect('frt.facility_resource_type_abbr')
          ->addSelect('frs.id')
          ->addSelect('frs.facility_resource_status')
          ->addSelect('efr.id')
          ->addSelect('efr.activation_sequence')
          ->addSelect('efrs.id')
          ->addSelect('fras.id')
          ->addSelect('fras.facility_resource_allocation_status')
          ->addSelect('fgt.facility_group_type')
          ->addSelect('efg.id')
          ->addSelect('efg.activation_sequence')
          ->addSelect('efg.event_facility_group')
          ->addSelect('efgs.id')
          ->addSelect('fgas.id')
          ->addSelect('fgas.facility_group_allocation_status')
        ->from('agEntity e')
          ->innerJoin('e.agSite s')
          ->leftJoin('e.agEntityEmailContact eec')
          ->leftJoin('eec.agEmailContact ec')
          ->leftJoin('e.agEntityPhoneContact epc')
          ->leftJoin('epc.agPhoneContact pc')
          ->innerJoin('s.agFacility f')
          ->innerJoin('f.agFacilityResource fr')
          ->innerJoin('fr.agFacilityResourceStatus frs')
          ->innerJoin('fr.agFacilityResourceType frt')
          ->innerJoin('fr.agEventFacilityResource efr')
          ->innerJoin('efr.agEventFacilityGroup efg')
          ->innerJoin('efg.agFacilityGroupType fgt')
          ->innerJoin('efg.agEventFacilityGroupStatus efgs')
          ->innerJoin('efr.agEventFacilityResourceStatus efrs')
          ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
          ->innerJoin('efrs.agFacilityResourceAllocationStatus fras')
          ->where('efg.event_id = ?', $eventId)
            ->andWhere($gstatExists)
            ->andWhere($rstatExists)
            ->andWhere($pExists)
            ->andWhere($eExists);

    $sql = $q->getSqlQuery(array(36, TRUE));

    // execute our query and get the entity id
    $data = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $eah = new agEntityAddressHelper();

    foreach ($data as $datum) {
      $subResults = array();
      $subResults['eventFacilityResourceID'] = $datum['efr_id'];
      $subResults['facility_name'] = $datum['f_facility_name'];
      $subResults['facility_code'] = $datum['f_facility_code'];
      $subResults['facility_resource_type_abbr'] = $datum['frt_facility_resource_type_abbr'];
      $subResults['facility_resource_status'] = $datum['frs_facility_resource_status'];
      $subResults['capacity'] = $datum['fr_capacity'];
      $subResults['facility_resource_activation_sequence'] = $datum['efr_activation_sequence'];
      $subResults['facility_resource_allocation_status'] = $datum['fras_facility_resource_allocation_status'];
      $subResults['facility_group'] = $datum['efg_event_facility_group'];
      $subResults['facility_group_type'] = $datum['fgt_facility_group_type'];
      $subResults['facility_group_allocation_status'] = $datum['fgas_facility_group_allocation_status'];
      $subResults['facility_group_activation_sequence'] = $datum['efg_activation_sequence'];
      $subResults['email'] = $datum['ec_email_contact'];
      $subResults['phone'] = $datum['pc_phone_contact'];

      $entityId = $datum['e_id'];
      $addr = $eah->getEntityAddress(array($entityId), FALSE, TRUE,
        agAddressHelper::ADDR_GET_TYPE, array(TRUE));

      foreach ($addr[$entityId][1] as $type => $val) {
        $subResults[str_replace(' ','_',strtolower($type))] = $val;
      }

      $results[$datum['efr_id']] = $subResults;
    }

    return $results;
  }

  /**
   * Method to return a summary of shifts and their staffing levels for a specific event
   * @param integer $eventId An eventId
   * @param integer $timestamp A php timestamp
   * @return array A multidimensional array of shift summary data
   */
  public static function getShiftsSummary($eventId, $timestamp)
  {
    $results = array();

    $rStatusExists = 'EXISTS (SELECT tefrs.id ' .
      'FROM agEventFacilityResourceStatus AS tefrs ' .
      'WHERE tefrs.time_stamp <= ? ' .
        'AND tefrs.event_facility_resource_id = efrs.event_facility_resource_id ' .
      'HAVING MAX(tefrs.time_stamp) = efrs.time_stamp)';

    $gStatusExists = 'EXISTS (SELECT tefgs.id ' .
      'FROM agEventFacilityGroupStatus AS tefgs ' .
      'WHERE tefgs.time_stamp <= ? ' .
        'AND tefgs.event_facility_group_id = efgs.event_facility_group_id ' .
      'HAVING MAX(tefgs.time_stamp) = efgs.time_stamp)';

    $q = agEventShift::getEventStaffShifts($timestamp);
    $origCols = $q->getDqlPart('select');

    // we only really do it this way because we're going to have an annoyingly long group-by later
    $cols[] = 'efrs.id';
    $cols[] = 'fras.id';
    $cols[] = 'fr.id';
    $cols[] = 'frt.id';
    $cols[] = 'f.id';
    $cols[] = 'efg.id';
    $cols[] = 'efgs.id';
    $cols[] = 'fgas.id';
    $cols[] = 'frt.facility_resource_type_abbr';
    $cols[] = 'f.facility_name';
    $cols[] = 'f.facility_code';
    $cols[] = 'efg.event_facility_group';
    $cols[] = 'fras.facility_resource_allocation_status';
    $cols[] = 'fgas.facility_group_allocation_status';

    foreach ($cols as $column) {
      $q->addSelect($column);
    }
    $q->addSelect('COUNT(ess.id) as staff_count')
      ->addSelect('ss.standby')
      ->addSelect('ss.disabled')
      ->innerJoin('efr.agEventFacilityResourceStatus efrs')
      ->innerJoin('efrs.agFacilityResourceAllocationStatus fras')
      ->innerJoin('efr.agFacilityResource fr')
      ->innerJoin('fr.agFacilityResourceType frt')
      ->innerJoin('fr.agFacility f')
      ->innerJoin('efr.agEventFacilityGroup efg')
      ->innerJoin('efg.agEventFacilityGroupStatus efgs')
      ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
      ->leftJoin('es.agEventStaffShift ess')
      ->andWhere('efg.event_id = ?', $eventId)
      ->andWhere($rStatusExists, date('Y-m-d H:i:s', $timestamp))
      ->andWhere($gStatusExists, date('Y-m-d H:i:s', $timestamp));

    // if only everything could be as easy as an orderby
    $orderBy = 'efg.event_facility_group, frt.facility_resource_type_abbr, f.facility_code, ' .
      'shift_start, shift_end, srt.staff_resource_type';
    $q->orderBy($orderBy);

    // this grabs pesky aliased columns
    foreach ($origCols as $column) {
      $aliasPos = strpos($column, ' AS ');
      if ($aliasPos !== FALSE) {
        $cols[] = substr($column, ($aliasPos + 4));
      } else {
        $cols[] = $column;
      }
    }
    unset($origCols);

    $q->groupBy(implode(', ', $cols));


    foreach($q->execute(array(), Doctrine_Core::HYDRATE_SCALAR) as $r) {
      $results[$r['efg_id']]['group_name'] = $r['efg_event_facility_group'];
      $results[$r['efg_id']]['group_status'] = $r['fgas_facility_group_allocation_status'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['facility_type'] = $r['frt_facility_resource_type_abbr'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['facility_code'] = $r['f_facility_code'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['facility_name'] = $r['f_facility_name'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['facility_status'] = $r['fras_facility_resource_allocation_status'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['staff_type'] = $r['srt_staff_resource_type_abbr'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['minimum_staff'] = $r['es_minimum_staff'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['maximum_staff'] = $r['es_maximum_staff'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['staff_count'] = $r['ess_staff_count'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['shift_status'] = $r['ss_shift_status'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['shift_standby'] = $r['ss_standby'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['shift_disabled'] = $r['ss_disabled'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['staff_wave'] = $r['es_staff_wave'];
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['shift_start'] = date('Y-m-d H:i:s', $r['es_shift_start']);
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['break_start'] = date('Y-m-d H:i:s', $r['es_break_start']);
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['shift_end'] = date('Y-m-d H:i:s', $r['es_shift_end']);
      $results[$r['efg_id']]['facilities'][$r['efr_id']]['shifts'][$r['es_id']]['timezone'] = date('T');

      if (isset($results[$r['efg_id']]['staff_totals']['resource_types'][$r['srt_staff_resource_type_abbr']])) {
        $results[$r['efg_id']]['staff_totals']['resource_types'][$r['srt_staff_resource_type_abbr']]['staff_count'] += $r['ess_staff_count'];
        $results[$r['efg_id']]['staff_totals']['resource_types'][$r['srt_staff_resource_type_abbr']]['minimum_staff'] += $r['es_minimum_staff'];
        $results[$r['efg_id']]['staff_totals']['resource_types'][$r['srt_staff_resource_type_abbr']]['maximum_staff'] += $r['es_maximum_staff'];
      } else {
        $results[$r['efg_id']]['staff_totals']['resource_types'][$r['srt_staff_resource_type_abbr']]['staff_count'] = $r['ess_staff_count'];
        $results[$r['efg_id']]['staff_totals']['resource_types'][$r['srt_staff_resource_type_abbr']]['minimum_staff'] = $r['es_minimum_staff'];
        $results[$r['efg_id']]['staff_totals']['resource_types'][$r['srt_staff_resource_type_abbr']]['maximum_staff'] = $r['es_maximum_staff'];
      }
    }

    foreach ($results as $efgID => $efg) {
      $facilityCt = 0;
      $results[$efgID]['staff_totals']['staff_count'] = 0;
      $results[$efgID]['staff_totals']['minimum_staff'] = 0;
      $results[$efgID]['staff_totals']['maximum_staff'] = 0;

      foreach ($efg['facilities'] as $facilityID => $facility) {
        $facilityCt++;
        $results[$efgID]['facilities'][$facilityID]['shift_count'] = count($facility['shifts']);
      }
      $results[$efgID]['facility_count'] = $facilityCt;

      foreach ($efg['staff_totals']['resource_types'] as $totals) {
        $results[$efgID]['staff_totals']['staff_count'] += $totals['staff_count'];
        $results[$efgID]['staff_totals']['minimum_staff'] += $totals['minimum_staff'];
        $results[$efgID]['staff_totals']['maximum_staff'] += $totals['maximum_staff'];
      }
    }

    return $results;
  }

  /**
   * Simple method to return the staff shift count
   * @param integer $eventId An event ID
   * @return integer An int value of the number of staff who are deployed in this event
   */
  public static function getEventShiftStaffCount($eventId)
  {
    return agDoctrineQuery::create()
      ->select('COUNT(ess.id) as count_staff_shift')
        ->from('agEventStaffShift ess')
        ->innerJoin('ess.agEventShift es')
        ->innerJoin('es.agEventFacilityResource efr')
        ->innerJoin('efr.agEventFacilityGroup efg')
        ->where('efg.event_id = ?', $eventId)
        ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  /**
   * Simple method to return the staff shift count
   * @return integer An int value of the number of staff who are deployed in this event
   */
  public function getShiftStaffCount()
  {
    return self::getEventShiftStaffCount($this->id);
  }

  /**
   * Simple method to return a  staff count
   * @param integer $evnetId An event ID
   * @param timestamp $timestamp
   * @return agDoctrineQuery A doctrine query.
   */
  protected static function getStaffCount($eventId, $timestamp)
  {
    $subQuery = 'EXISTS ( ' .
      'SELECT ess2.id '.
        'FROM agEventStaffStatus ess2 '.
          'WHERE ess2.event_staff_id = ess.event_staff_id ' .
            'AND ess2.time_stamp <= ? '.
          'HAVING (MAX(ess2.time_stamp) = ess.time_stamp) ' .
        ')';

    $query = agDoctrineQuery::create()
      ->select('count(s.id) staffCount')
        ->from('agStaff s')
          ->innerJoin('s.agStaffResource sr')
          ->innerJoin('sr.agStaffResourceStatus srs')
          ->innerJoin('sr.agEventStaff es')
          ->innerJoin('es.agEventStaffStatus ess')
          ->innerJoin('ess.agStaffAllocationStatus sas')
          ->innerJoin('s.agPerson p')
          ->innerJoin('p.agEntity e')
        ->where('es.event_id = ?', $eventId)
        ->andWhere($subQuery, date('Y-m-d H:i:s', $timestamp));

    return $query;
  }

  /**
   * Simple method to return a  staff count who meet the qualified criterion
   * @param integer $evnetId An event ID
   * @param timestamp $timestamp
   * @return integer An integer of the staff count
   */
  public static function getUnknownEventStaffCount($eventId, $timestamp)
  {
    return self::getStaffCount($eventId, $timestamp)
      ->andWhere('srs.is_available = ?', TRUE)
      ->andWhere('(sas.allocatable = ? AND sas.standby = ?)', array(TRUE, TRUE))
      ->andWhere(agEntity::QUERY_HAS_GEO)
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  /**
   * Simple method to return a  staff count who meet the qualified criterion
   * @param integer $evnetId An event ID
   * @param timestamp $timestamp
   * @return integer An integer of the staff count
   */
  public static function getUnavailableEventStaffCount($eventId, $timestamp)
  {
    return self::getStaffCount($eventId, $timestamp)
      ->andWhere('(sas.allocatable = ? AND sas.committed = ?) OR (srs.is_available = ?) OR ' .
        '(NOT ' . agEntity::QUERY_HAS_GEO . ')', array(FALSE, FALSE, FALSE))
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  /**
   * Simple method to return a  staff count who meet the qualified criterion
   * @param integer $evnetId An event ID
   * @param timestamp $timestamp
   * @return integer An integer of the staff count
   */
  public static function getAvailableEventStaffCount($eventId, $timestamp)
  {
    return self::getStaffCount($eventId, $timestamp)
      ->andWhere('srs.is_available = ?', TRUE)
      ->andWhere('(sas.allocatable = ? AND sas.standby = ?)', array(TRUE, FALSE))
      ->andWhere(agEntity::QUERY_HAS_GEO)
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  /**
   * Simple method to return a  staff count who meet the qualified criterion
   * @param integer $evnetId An event ID
   * @param timestamp $timestamp
   * @return integer An integer of the staff count
   */
  public static function getCommittedEventStaffCount($eventId, $timestamp)
  {
    return self::getStaffCount($eventId, $timestamp)
      ->andWhere('srs.is_available = ?', TRUE)
      ->andWhere('sas.committed = ?', TRUE, FALSE)
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  /**
   * Method to return a count of event staff who meet the qualified criterion
   * @param integer $timestamp A unix timestamp
   * @return integer An integer of the staff count
   */
  public function getUnknownStaffCount($timestamp)
  {
    return self::getUnknownEventStaffCount($this->id, $timestamp);
  }
  
  /**
   * Method to return a count of event staff who meet the qualified criterion
   * @param integer $timestamp A unix timestamp
   * @return integer An integer of the staff count
   */
  public function getAvailableStaffCount($timestamp)
  {
    return self::getAvailableEventStaffCount($this->id, $timestamp);
  }

  /**
   * Method to return a count of event staff who meet the qualified criterion
   * @param integer $timestamp A unix timestamp
   * @return integer An integer of the staff count
   */
  public function getCommittedStaffCount($timestamp)
  {
    return self::getCommittedEventStaffCount($this->id, $timestamp);
  }

  /**
   * Simple method to return a  staff count who meet the qualified criterion
   * @param integer $evnetId An event ID
   * @param timestamp $timestamp
   * @return integer An integer of the staff count
   */
  public static function getMissingGeoEventStaffCount($eventId, $timestamp)
  {
    return self::getStaffCount($eventId, $timestamp)
      ->andWhere('srs.is_available = ?', TRUE)
      ->andWhere('(sas.allocatable = ? OR sas.committed = ?)', array(TRUE, TRUE))
      ->andWhere('NOT ' . agEntity::QUERY_HAS_GEO)
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  /**
   * Method to return a count of event staff who meet the qualified criterion
   * @param integer $timestamp A unix timestamp
   * @return integer An integer of the staff count
   */
  public function getMissingGeoStaffCount($timestamp)
  {
    return self::getMissingGeoEventStaffCount($this->event_id, $timestamp);
  }

  /**
   * Method to return an event status type query
   * @param integer $eventId An eventId
   * @param integer $timestamp A unix timestamp
   * @return agDoctrineQuery An agDoctrineQuery object
   */
  protected static function getEventStaffTypeCount($eventId, $timestamp)
  {
    $statusExists = 'EXISTS ( ' .
      'SELECT subEss.id ' .
        'FROM agEventStaffStatus subEss ' .
        'WHERE subEss.time_stamp <= ? ' .
          'AND subEss.event_staff_id = ess.event_staff_id ' .
        'HAVING (MAX(subEss.time_stamp) = ess.time_stamp) ' .
      ')';

    $q = agDoctrineQuery::create()
      ->select('sr.staff_resource_type_id')
          ->addSelect('COUNT(sr.id) as staff_count')
        ->from('agStaffResource sr')
          ->innerJoin('sr.agStaffResourceStatus srs')
          ->innerJoin('sr.agStaffResourceType srt')
          ->innerJoin('sr.agEventStaff es')
          ->innerJoin('es.agEventStaffStatus ess')
          ->innerJoin('ess.agStaffAllocationStatus sas')
          ->innerJoin('sr.agStaff s')
          ->innerJoin('s.agPerson p')
          ->innerJoin('p.agEntity e')
        ->where('es.event_id = ?', $eventId)
            ->addWhere($statusExists, date('Y-m-d H:i:s', $timestamp))
            ->andWhere(agEntity::QUERY_HAS_GEO)
        ->groupBy('sr.staff_resource_type_id')
        ->orderBy('srt.staff_resource_type ASC');

    return $q;
  }

  /**
   * Method to return a count of event staff who meet the qualified criterion
   * @param integer $eventId An eventId
   * @param integer $timestamp A unix timestamp
   * @return array A single-dimension associative array
   */
  public static function getUnknownEventStaffTypeCount($eventId, $timestamp)
  {
    return self::getEventStaffTypeCount($eventId, $timestamp)
      ->addWhere('srs.is_available = ?', TRUE)
      ->andWhere('(sas.allocatable = ? AND sas.standby = ?)', array(TRUE, TRUE))
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to return a count of event staff who meet the qualified criterion
   * @param integer $eventId An eventId
   * @param integer $timestamp A unix timestamp
   * @return array A single-dimension associative array
   */
  public static function getUnavailableEventStaffTypeCount($eventId, $timestamp)
  {
    return self::getEventStaffTypeCount($eventId, $timestamp)
      ->andWhere('(srs.is_available = ?) OR (sas.allocatable = ? AND sas.standby = ?)', array(FALSE, FALSE, FALSE))
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to return a count of event staff who meet the qualified criterion
   * @param integer $eventId An eventId
   * @param integer $timestamp A unix timestamp
   * @return array A single-dimension associative array
   */
  public static function getAvailableEventStaffTypeCount($eventId, $timestamp)
  {
    return self::getEventStaffTypeCount($eventId, $timestamp)
      ->addWhere('srs.is_available = ?', TRUE)
      ->andWhere('(sas.allocatable = ? AND sas.standby = ?)', array(TRUE, FALSE))
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to return a count of event staff who meet the qualified criterion
   * @param integer $eventId An eventId
   * @param integer $timestamp A unix timestamp
   * @return array A single-dimension associative array
   */
  public static function getCommittedEventStaffTypeCount($eventId, $timestamp)
  {
    return self::getEventStaffTypeCount($eventId, $timestamp)
      ->addWhere('srs.is_available = ?', TRUE)
      ->andWhere('(sas.committed = ?)', TRUE)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to return a count of event staff who meet the qualified criterion
   * @param integer $timestamp A unix timestamp
   * @return array A single-dimension associative array
   */
  public function getUnknownStaffTypeCount($timestamp)
  {
    return self::getUnknownEventStaffTypeCount($this->id, $timestamp);
  }

  /**
   * Method to return a count of event staff who meet the qualified criterion
   * @param integer $timestamp A unix timestamp
   * @return array A single-dimension associative array
   */
  public function getAvailableStaffTypeCount($timestamp)
  {
    return self::getAvailableEventStaffTypeCount($this->id, $timestamp);
  }

  /**
   * Method to return a count of event staff who meet the qualified criterion
   * @param integer $timestamp A unix timestamp
   * @return array A single-dimension associative array
   */
  public function getCommittedStaffTypeCount($timestamp)
  {
    return self::getCommittedEventStaffTypeCount($this->id, $timestamp);
  }

  /**
   * Returns a sum of required min / max staff for a specified point in time
   * @param integer $eventId An eventId
   * @param integer $timestamp A PHP / UNIX timestamp
   * @return array A multidimensional array keyed by staff resource type
   */
  public static function getEventShiftStaffTypeSum($eventId, $timestamp)
  {
    $strTimestamp = date('Y-m-d H:i:s', $timestamp);

    $frStatusExists = 'EXISTS ( ' .
      'SELECT subEfrs.id ' .
        'FROM agEventFacilityResourceStatus subEfrs ' .
        'WHERE subEfrs.time_stamp <= ? ' .
          'AND subEfrs.event_facility_resource_id = efrs.event_facility_resource_id ' .
        'HAVING (MAX(subEfrs.time_stamp) = efrs.time_stamp) ' .
      ')';

    $fgStatusExists = 'EXISTS ( ' .
      'SELECT subEfgs.id ' .
        'FROM agEventFacilityGroupStatus subEfgs ' .
        'WHERE subEfgs.time_stamp <= ? ' .
          'AND subEfgs.event_facility_group_id = efgs.event_facility_group_id ' .
        'HAVING (MAX(subEfgs.time_stamp) = efgs.time_stamp) ' .
      ')';

    $timeBoundWhere = '((( 60 * es.minutes_start_to_facility_activation ) + efrat.activation_time ) ' .
      '<= ?) AND ((( 60 * ( es.minutes_start_to_facility_activation + es.task_length_minutes + ' .
      'es.break_length_minutes )) + efrat.activation_time ) >= ? )';

    $q = agDoctrineQuery::create()
      ->select('es.staff_resource_type_id')
        ->addSelect('SUM(es.minimum_staff) as minSum')
        ->addSelect('SUM(es.minimum_staff) as maxSum')
      ->from('agEventShift es')
        ->innerJoin('es.agStaffResourceType srt')
        ->innerJoin('es.agEventFacilityResource efr')
        ->innerJoin('efr.agEventFacilityResourceActivationTime efrat')
        ->innerJoin('efr.agEventFacilityResourceStatus efrs')
        ->innerJoin('efrs.agFacilityResourceAllocationStatus fras')
        ->innerJoin('efr.agFacilityResource fr')
        ->innerJoin('fr.agFacilityResourceStatus frs')
        ->innerJoin('efr.agEventFacilityGroup efg')
        ->innerJoin('efg.agEventFacilityGroupStatus efgs')
        ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
      ->where('frs.is_available = ?', TRUE)
        ->andWhere('fras.staffed = ?', TRUE)
        ->andWhere('fgas.active = ?', TRUE)
        ->andwhere($frStatusExists, $strTimestamp)
        ->andwhere($fgStatusExists, $strTimestamp)
        ->andWhere($timeBoundWhere, array($timestamp, $timestamp))
      ->groupBy('es.staff_resource_type_id')
      ->orderBy('srt.staff_resource_type ASC');

    return $q->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_ARRAY);
  }

  /**
   * Returns a sum of required min / max staff for a specified point in time
   * @param integer $timestamp A PHP / UNIX timestamp
   * @return array A multidimensional array keyed by staff resource type
   */
  public function getShiftStaffTypeSum($timestamp)
  {
    return self::getEventShiftStaffTypeSum($this->id, $timestamp);
  }

  /**
   * Returns a multidimensional array of staffing estimates
   * @param integer $eventId An eventId
   * @param integer $timestamp A PHP / UNIX timestamp
   * @return array A multidimensional array keyed by staff resource type
   */
  public static function getEventShiftEstimates($eventId, $timestamp)
  {
    $results = array();

    $staffResourceTypes = agDoctrineQuery::create()
      ->select('srt.id')
        ->addSelect('srt.staff_resource_type')
      ->from('agStaffResourceType srt')
      ->useResultCache(TRUE, 3600)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    $shiftResources = self::getEventShiftStaffTypeSum($eventId, $timestamp);
    $unknownStaff = self::getUnknownEventStaffTypeCount($eventId, $timestamp);
    $availableStaff = self::getAvailableEventStaffTypeCount($eventId, $timestamp);
    $committedStaff = self::getCommittedEventStaffTypeCount($eventId, $timestamp);
    $unavailableStaff = self::getUnavailableEventStaffTypeCount($eventId, $timestamp);

    $results['total'] = array('min_required' => 0, 'max_required' => 0, 'unknown' => 0,
      'available' => 0, 'committed' => 0, 'unavailable' => 0);

    foreach ($shiftResources as $srtId => $sums) {

      $results[$srtId]['resource_type'] = $staffResourceTypes[$srtId];
      $results[$srtId]['min_required'] = $sums[0];
      $results[$srtId]['max_required'] = $sums[1];

      $results['total']['min_required'] += $sums[0];
      $results['total']['max_required'] += $sums[1];

      if (isset($unknownStaff[$srtId])) {
        $results[$srtId]['unknown'] = $unknownStaff[$srtId];
        $results['total']['unknown'] += $unknownStaff[$srtId];
      } else {
        $results[$srtId]['unknown'] = 0;
      }

      if (isset($unavailableStaff[$srtId])) {
        $results[$srtId]['unavailable'] = $unavailableStaff[$srtId];
        $results['total']['unavailable'] += $unavailableStaff[$srtId];
      } else {
        $results[$srtId]['unavailable'] = 0;
      }

      if (isset($availableStaff[$srtId])) {
        $results[$srtId]['available'] = $availableStaff[$srtId];
        $results['total']['available'] += $availableStaff[$srtId];
      } else {
        $results[$srtId]['available'] = 0;
      }

      if (isset($committedStaff[$srtId])) {
        $results[$srtId]['committed'] = $committedStaff[$srtId];
        $results['total']['committed'] += $committedStaff[$srtId];
      } else {
        $results[$srtId]['committed'] = 0;
      }
    }

    return $results;
  }

  /**
   * Returns a multidimensional array of staffing estimates
   * @param integer $timestamp A PHP / UNIX timestamp
   * @return array A multidimensional array keyed by staff resource type
   */
  public function getShiftEstimates($timestamp)
  {
    return self::getEventShiftEstimates($this->id, $timestamp);
  }
}