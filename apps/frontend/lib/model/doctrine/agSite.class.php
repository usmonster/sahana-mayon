<?php

/**
 * Extends BaseagSite
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

/**
 * @todo complete description on line 4
 */
class agSite extends BaseagSite
{

  /**
   * @todo add description and complete info below
   * @param Doctrine_Connection $conn
   * @return <type>
   */
  public function delete(Doctrine_Connection $conn = null)
  {
//  if ($agFacility = $this->getAgFacility()) {
//    $agFacility->delete();
//  }
    foreach ($this->getAgFacility() as $agF) {


      $agF->delete();
    }

    return parent::delete($conn);
  }

}

