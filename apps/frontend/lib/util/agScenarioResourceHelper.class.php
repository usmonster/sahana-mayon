<?php

/**
 * Provides methods for Scenario Resources: Staff and Facility
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agScenarioResourceHelper
{

  public static function returnDefaultStaffResourceTypes($scenario_id)
  {
    $defaultStaffResourceTypes = agDoctrineQuery::create()
            ->select('srt.id')
            ->addSelect('srt.staff_resource_type')
            ->addSelect('dssrt.id')
            ->from('agStaffResourceType srt')
            ->innerJoin('srt.agDefaultScenarioStaffResourceType dssrt')
            ->where('dssrt.scenario_id = ?', $scenario_id)
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    return $defaultStaffResourceTypes;
  }
  public static function returnDefaulFacilityResourceTypes($scenario_id)
  {
    $defaultFacilityResourceTypes = agDoctrineQuery::create()
            ->select('frt.id')
            ->addSelect('frt.facility_resource_type')
            ->addSelect('dsfrt.id')
            ->from('agFacilityResourceType frt')
            ->innerJoin('frt.agDefaultScenarioFacilityResourceType dsfrt')
            ->where('dsfrt.scenario_id = ?', $scenario_id)
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    return $defaultFacilityResourceTypes;
  }

}