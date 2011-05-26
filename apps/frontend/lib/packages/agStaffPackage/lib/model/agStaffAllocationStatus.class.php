<?php
/**
 * An extended StaffAllocation class to provide additional extended staff resource methods
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
class agStaffAllocationStatus extends BaseagStaffAllocationStatus
{
  /**
   * Method to return the event staff post-deployed status
   */
  public static function getEventStaffDeployedStatusId()
  {
    $this->eventStaffDeployedStatusId = agDoctrineQuery::create()
      ->select('sas.id')
        ->from('agStaffAllocationStatus sas')
        ->where('sas.staff_allocation_status = ?', 'Committed')
        ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

}
