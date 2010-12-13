<?php

/**
 *
 * extends the base Facility class for added functionality
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
class agFacility extends BaseagFacility
{

  /**
   * delete()
   *
   * extends the base class's delete() function to delete related
   * agFacilityResource records before deleting the agFacility
   * record
   *
   * @param $conn Doctrine_Connection object that gets passed
   *              through into the base class's function
   *
   * @return passthrough from base class
   *
   */
  public function delete(Doctrine_Connection $conn = null)
  {
    foreach ($this->getAgFacilityResource() as $agFR) {
      $agFR->delete();
    }

    return parent::delete($conn);
  }

  /**
   * getBorough()
   *
   * Get this agFacility record's Borough, based on its first work
   * address
   */
  public function getBorough() {
    $addresses = $this->getAgSite()->getAgEntity()->getAgEntityAddressContact();

    foreach ($addresses as $address) {
      if ($address->getAgAddressContactType() == 'work') {
        foreach ($address->getAgAddress()->getAgAddressValue() as $addressValue) {
          /**
           * @todo check for correct agAddressValue type and then return
           *       its value
           */
        }
      }
    }

  }

}

