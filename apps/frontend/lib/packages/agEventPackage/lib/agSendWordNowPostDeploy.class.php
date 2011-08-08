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

class agSendWordNowPostDeploy extends agSendWordNowExport
{
  protected $staffDistributionCenters;

  /**
   * Method to return an instance of this class
   * @param integer $eventId An event ID
   * @param string The export basename (as it would appear in an export)
   * @return agSendWordNowPostDeploy An instance of this class
   */
  public static function getInstance($eventId, $exportBaseName)
  {
    $self = new self();
    $self->_init($eventId, $exportBaseName);
    return $self;
  }

  /**
   * Method to set export components
   */
  protected function setExportComponents()
  {
    parent::setExportComponents();

    $this->exportComponents[] = array('method' => 'setDeploymentData');
  }

  /**
   * Scalar method to return a staff distribution center for a given facility group
   * @param integer $eventFacilityGroupId
   * @return array Staff distribution center data
   */
  protected function getStaffDistributionCenter($eventFacilityGroupId)
  {
    if (!isset($this->staffDistributionCenters[$eventFacilityGroupId])) {
      $staffDistributionCenter = agEventFacilityGroup::getStaffDistributionCenter($eventFacilityGroupId);
      $this->staffDistributionCenters[$eventFacilityGroupId] = $staffDistributionCenter;
    }
    return $this->staffDistributionCenters[$eventFacilityGroupId];
  }

  /**
   * Method to get the base doctrine query object used in export
   * @return agDoctrineQuery A doctrine query object
   */
  protected function getDoctrineQuery()
  {
    // get our basic contact query
    $q = $this->getEventStaffContactQuery();

    // add our post-deploy components
    $q->addSelect('evss.id')
      ->addSelect('evsh.id')
      ->addSelect('evsh.minutes_start_to_facility_activation')
      ->addSelect('evfr.id')
      ->addSelect('evfrat.id')
      ->addSelect('evfrat.activation_time')
      ->addSelect('evfg.id')
      ->innerJoin('evs.agEventStaffShift evss') // a10
      ->innerJoin('evss.agEventShift evsh') // a11
      ->innerJoin('evsh.agEventFacilityResource evfr') // a12
      ->innerJoin('evfr.agEventFacilityResourceActivationTime evfrat') // a13
      ->innerJoin('evfr.agEventFacilityGroup evfg') // a14
      ->andWhere('sas.committed = ?', TRUE);

    // filter for the first shift for each staffperson
    $qFirstShift = 'EXISTS (SELECT tevss.id, ' .
      '((60 * tevsh.minutes_start_to_facility_activation) + tevfrat.activation_time) as shift_start, ' .
      '((60 * evsh.minutes_start_to_facility_activation) + evfrat.activation_time) as parent_start ' .
      'FROM agEventStaffShift tevss ' .
        'INNER JOIN tevss.agEventShift tevsh ' .
        'INNER JOIN tevsh.agShiftStatus ss ' .
        'INNER JOIN tevsh.agEventFacilityResource tevfr ' .
        'INNER JOIN tevfr.agEventFacilityResourceActivationTime tevfrat ' .
      'WHERE tevss.event_staff_id = evss.event_staff_id ' .
        'AND ss.disabled = FALSE ' .
      'HAVING MIN(shift_start) = parent_start)';
    $q->andWhere($qFirstShift);

    return $q;
  }

  /**
   * Method to add deployment data to the mix
   */
  protected function setDeploymentData()
  {
    foreach ($this->exportRawData as $rowId => $rawData) {
      // format the data
      $gData = $this->getStaffDistributionCenter($rawData->a14__id);
      $timestamp = $rawData->a11__minutes_start_to_facility_activation + $rawData->a13__activation_time;
      $time = date('Y-m-d h:j', $timestamp);

      // concat value strings
      $this->exportData[$rowId]['CUSTOM LABEL'] = $gData['event_facility_group'] . '; ' .
        $gData['facility_name'] . ' ('.  $gData['facility_resource_type'] . '); ' .
        $gData['facility_resource_type'] . ' ' . $gData['facility_code'];
      $this->exportData[$rowId]['CUSTOM VALUE'] = $gData['facility_name'] . ', ' .
        $gData['facility_address'] . ' at ' . $time;
    }
  }
}