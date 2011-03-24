<?php

/**
 * agFacilityGroupType extended class for creating scenario specific
 * facility groups
 *
 * @method agFacilityGroupType getObject() Returns the current form's model object
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agFacilityGroupType extends BaseagFacilityGroupType
{

  /**
   *
   * @return a string representation of the facility group type
   */
  public function __toString()
  {
    return $this->getFacilityGroupType();
    //->facility_name . " : " . $this->getAgFacilityResourceType()->facility_resource_type;
  }

}