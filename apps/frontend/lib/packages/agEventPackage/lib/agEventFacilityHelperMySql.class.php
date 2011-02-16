<?php

/**
 * provides mysql-optimized event facility management functions
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

class agEventFacilityHelperMySql extends agEventFacilityHelper
{
  public static function agEventFacilityHelperMySqlInit()
  {
    agDatabaseHelper::displayPortabilityWarning('mysql') ;
  }

  /**
   * Return an array keyed by the shift id of any shifts that are currently in operation
   *
   * @param integer(4) $eventId The event being queried
   * @param string A optional value that adjusted the concept of 'current' from the application's
   * CURRENT_TIMESTAMP (or PHP time()) to the passed value (essentially enabling the user to ask
   * what shifts will be active at this point-in-$time
   * @return array An two-dimensional associative array, keyed by event_shift_id with a value
   * array of event_facility_resource_id
   */
  public static function returnCurrentFacilityResourceShifts($eventId, $time = NULL)
  {
    // convert our start time to unix timestamp or set default if null
    $timestamp = agDateTimeHelper::defaultTimestampFormat($time) ;
    $mysqlTime = agDateTimeHelper::timestampToMySql($timestamp) ;

    $query = new Doctrine_RawSql() ;
    $query->addComponent('es', 'agEventShift es')
      ->select('{es.id}')
        ->addSelect('{es.facility_resource_id}')
      ->from('ag_event_shift es')
        ->innerJoin('ag_event_facility_resource efr ON es.event_facility_resource_id = efr.id')
        ->innerJoin('ag_event_facility_group efg ON efr.event_facility_group_id = efg.id')
        ->leftJoin('ag_event_facility_resource_activation_time efrat ON efr.id = efrat.event_facility_resource_id')
      ->where('efg.event_id = ?', $eventId)
        ->andWhere('DATE_ADD(efrat.activation_time, INTERVAL es.minutes_start_to_facility_activation MINUTE) <= TIMESTAMP(?)', $mysqlTime)
        ->andWhere('DATE_ADD(efrat.activation_time, INTERVAL (es.minutes_start_to_facility_activation + es.task_length_minutes + es.break_length_minutes) MINUTE) >= TIMESTAMP(?)', $mysqlTime) ;

    $results = $query->execute(array(), 'key_value_array');
    return $results;
  }
} agEventFacilityHelperMySql::agEventFacilityHelperMySqlInit() ;