<?php

/**
 * Class to provide message export management for messaging functionality
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
 *
 */

class agSendWordNowPreDeploy extends agSendWordNowExport
{
  /**
   * Method to return an instance of this class
   * @param integer $eventId An event ID
   * @param string The export basename (as it would appear in an export)
   * @return agSendWordNowPreDeploy An instance of this class
   */
  public static function getInstance($eventId, $exportBaseName)
  {
    $self = new self();
    $self->_init($eventId, $exportBaseName);
    return $self;
  }

  /**
   * Method to get the base doctrine query object used in export
   * @return agDoctrineQuery A doctrine query object
   */
  protected function getDoctrineQuery()
  {
    // get our basic contact query
    $q = $this->getEventStaffContactQuery();

    // add our pre-deploy components
    $q->andWhere('sas.allocatable = ?', TRUE)
      ->andWhere('sas.standby = ?', TRUE);
    return $q;
  }
}