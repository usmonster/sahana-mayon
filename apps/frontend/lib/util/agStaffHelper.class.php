<?php

/**
 *
 * Provides bulk-staff manipulation methods
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agStaffHelper
{

  public static function getStaffResourceTypes()
  {
    $staffResourceTypes = agDoctrineQuery::create()
            ->select('id, staff_resource_type')
            ->from('agStaffResourceType')
            ->execute(array(), 'key_value_pair');
    return $staffResourceTypes;
  }

}