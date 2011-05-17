<?php

/**
 * provides event management functions
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUNY SPS
 * @author Charles Wisniewski, CUNY SPS
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEventHelper
{

  /**
   * Method to return the current status of an event.
   * @return array An indexed array, keyed by agEventStatus.id, containing value members event_id,
   * time_stamp, and event_status_type_id.
   */
  public static function returnCurrentEventStatus()
  {
    $query = agDoctrineQuery::create()
        ->select('es.id')
        ->addSelect('es.event_id')
        ->addSelect('es.time_stamp')
        ->addSelect('es.event_status_type_id')
        ->addSelect('est.active')
        ->from('agEventStatus es')
        ->innerJoin('es.agEventStatusType est')
        ->where('EXISTS (
          SELECT s.id
            FROM agEventStatus s
            WHERE s.event_id = es.event_id
              AND s.time_stamp <= CURRENT_TIMESTAMP
            HAVING MAX(s.time_stamp) = es.time_stamp)');

    $results = $query->execute(array(), 'key_value_array');
    return $results;
  }

  public static function returnDefaultEventStatus()
  {
    $defaultEventStatus = agDoctrineQuery::create()
        ->select('a.id')
        ->from('agEventStatusType a')
        ->where('a.event_status_type = ?', agGlobal::getParam('default_event_status_type'))
        ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    return $defaultEventStatus;
  }

}
