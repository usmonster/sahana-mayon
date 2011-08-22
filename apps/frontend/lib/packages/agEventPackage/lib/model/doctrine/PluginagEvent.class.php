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

    $q = agEventShift::getEventStaffShifts($timestamp, $timestamp);
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
    }

    foreach ($results as $efgID => $efg) {
      $facilityCt = 0;
      foreach ($efg['facilities'] as $facilityID => $facility) {
        $facilityCt++;
        $results[$efgID]['facilities'][$facilityID]['shift_count'] = count($facility['shifts']);
      }
      $results[$efgID]['facility_count'] = $facilityCt;
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
}