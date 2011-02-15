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

  public static function returnCurrentFacilityResourceShifts($eventId, $time = NULL)
  {
    // convert our start time to unix timestamp or set default if null
    $timestamp = agDateTimeHelper::defaultTimestampFormat($time) ;
    $mysqlTime = agDateTimeHelper::timestampToMySql($timestamp) ;

    $query = new Doctrine_RawSql() ;
    $query->addComponent('es', 'agEventShift es')
      ->select('{es.facility_resource_id}')
        ->addSelect('{es.id}')
      ->from('ag_event_shift es')
        ->innerJoin('ag_event_facility_resource efr ON es.event_facility_resource_id = efr.id')
        ->innerJoin('ag_event_facility_group efg ON efr.event_facility_group_id = efg.id')
        ->leftJoin('ag_event_facility_resource_activation_time efrat ON efr.id = efrat.event_facility_resource_id')
      ->where('efg.event_id = ?', $eventId)
        ->andWhere('DATE_ADD(efrat.activation_time, INTERVAL es.minutes_start_to_facility_activation MINUTE) <= TIMESTAMP(?)', $time)
        ->andWhere('DATE_ADD(efrat.activation_time, INTERVAL (es.minutes_start_to_facility_activation + es.task_length_minutes + es.break_length_minutes) MINUTE) >= TIMESTAMP(?)', $time) ;

    $results = $query->execute(array(), 'key_value_array') ;
    return $results ;
  }
} agEventFacilityHelperMySql::agEventFacilityHelperMySqlInit() ;