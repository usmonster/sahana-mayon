<?php

/**
 * PluginagEventStaff is an extension of base class agEventStaff
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
abstract class PluginagEventStaff extends BaseagEventStaff
{

  /**
   * Method to return a doctrine query that only selects active event staff
   * @param integer $eventId The event ID being queried
   * @return agDoctrineQuery An agDoctrineQuery object
   */
  public static function getActiveEventStaffQuery($eventId)
  {
    // start with our basic query object
    $q = agDoctrineQuery::create()
        ->from('agEventStaff evs')
          ->innerJoin('evs.agEventStaffStatus ess')
          ->innerJoin('ess.agStaffAllocationStatus sas')
          ->innerJoin('evs.agStaffResource sr')
          ->innerJoin('sr.agStaffResourceStatus srs')
        ->where('evs.event_id = ?', $eventId)
          ->andWhere('srs.is_available = ?', TRUE);

    // ensure that we only get the most recent staff status
    $recentStaffStatus = 'EXISTS (' .
      'SELECT sess.id ' .
        'FROM agEventStaffStatus AS sess ' .
        'WHERE sess.time_stamp <= CURRENT_TIMESTAMP ' .
          'AND sess.event_staff_id = ess.event_staff_id ' .
        'HAVING MAX(sess.time_stamp) = ess.time_stamp' .
      ')';
    $q->andWhere($recentStaffStatus);

    return $q;
  }

  public function setUp()
  {
    parent::setUp();

    $luceneable0 = new Luceneable(array());
    $this->actAs($luceneable0);
  }
}