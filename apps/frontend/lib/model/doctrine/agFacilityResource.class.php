<?php

/**
 * agFacilityResource
 *
 * extends the base FacilityResource class for added
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
class agFacilityResource extends BaseagFacilityResource
{

  /**
   *
   * @return a string representation of the facility resource in question, this
   * string is comprised of the facility's name and the facility's resource type
   */
  public function __toString()
  {
    return $this->getAgFacility()->facility_name . " : " . $this->getAgFacilityResourceType()->facility_resource_type;
  }

  /**
   * delete()
   *
   * Before deleting this agFacilityResource record, first delete
   * all agScenarioFacilityResource records that are joined to it
   */
  public function delete(Doctrine_Connection $conn = null)
  {
    /* @todo notify user that facility resources are used by scenario(s) */
    foreach ($this->getAgScenarioFacilityResource() as $agSFR) {
      $agSFR->delete();
    }

    return parent::delete($conn);
  }

}