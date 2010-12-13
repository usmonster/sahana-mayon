<?php

/**
 *
 * extends the base ScenarioFacilityResource class for added
 * functionality
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Ilya Gulko, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agScenarioFacilityResource extends BaseagScenarioFacilityResource
{

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
