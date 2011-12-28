<?php

/**
 * Provides Scenario Facility Group info
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agScenarioFacilityGroup extends BaseagScenarioFacilityGroup
{

  public function __toString()
  {
    return $this->getScenarioFacilityGroup();
  }

  /**
   * Method to return the default group allocation status id
   * @return integer Default group allocation status id
   */
  public static function getDefaultAllocationStatus()
  {
    return agDoctrineQuery::create()
      ->select('fgas.id')
        ->from('agFacilityGroupAllocationStatus fgas')
        ->where('fgas.facility_group_allocation_status = ?',
          agGlobal::getParam('default_facility_group_allocation_status'))
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }
}
