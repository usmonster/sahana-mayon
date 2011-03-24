<?php

/**
 *
 * extends the base ScenarioFacilityResource class for added
 * functionality
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Ilya Gulko, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agScenarioFacilityResource extends BaseagScenarioFacilityResource
{
  /**
   *
   * @return a string representation of the scenario facility resource
   */
  public function __toString()
  {
    return $this->getAgFacilityResource()->getAgFacility()->facility_name . " : " . $this->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type;
  }

  /**
   *
   * extends the base delete() function to delete related agScenarioShift and
   * agFacilityStaffResource records before deleting the record
   *
   * @param $conn Doctrine_Connection that gets passed into the
   *              base function
   *
   * @return passthrough for base function
   */
  public function delete(Doctrine_Connection $conn = null)
  {
    foreach ($this->getAgScenarioShift() as $agSS) {
      $agSS->delete();
    }

    foreach ($this->getAgFacilityStaffResource() as $agFSR) {
      $agFSR->delete();
    }

    return parent::delete($conn);
  }

}
