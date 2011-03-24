<?php

/**
 * agOrganization extends the base organization object to capture additional organization related information.
 * 
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agOrganization extends BaseagOrganization
{
  /**
   * organizationInArray() is a static method to return an array of organization
   * where the organization id is set as the array's keys.
   *
   * @param none
   */
  static public function organizationInArray()
  {
    try
    {
      // Collect all organizations.
      $organizationQuery = agDoctrineQuery::create()
        ->select('o.*')
        ->from('agOrganization as o');

      $results = $organizationQuery->execute();

      $organizationList = array();
      foreach ($results as $rslt)
      {
        $organizationList[$rslt->getId()] = $rslt->getOrganization();
      }

      return $organizationList;
    } catch (\Doctrine\ORM\ORMException $e) {
      return NULL;
    }
  }

  /**
   * staffbyResource() is a static method to return an array of
   * (staff resource organization id, organization id, staff id, staff resource type id).
   *
   * @param array $organizationIds - Queries staffs for the specified organizations only.
   */
  static public function organizationStaffByResource($organizationIds)
  {
    try
    {
      // Should check if ag_staff_resource_organization table is empty.  If table is empty, staff resources are not associated to organization.

      $query = agDoctrineQuery::create()
        ->select('o.id, sro.id, sr.id, s.id, s.person_id, sr.staff_resource_type_id')
        ->from('agOrganization as o')
        ->leftJoin('o.agStaffResourceOrganization as sro')
        ->leftJoin('sro.agStaffResource as sr')
        ->leftJoin('sr.agStaff as s')
        ->where('1=1');

      /*
       * Append a where clause to query for specific organizations if an
       * array of organziation id is passed in as argument.
       */
      if (is_array($organizationIds) and count($organizationIds) > 0)
      {
        $query->whereIn('o.id', $organizationIds);
      }
      $query->orderBy('o.id, s.person_id, sr.staff_resource_type_id');
      $resultSet = $query->execute(array(), Doctrine::HYDRATE_SCALAR);
//      print_r($resultSet);

      $orgStfByRes = array();
      foreach($resultSet as $rslt)
      {
        $stfResOrgId = $rslt['sro_id'];
        $orgId = $rslt['o_id'];
        $staffId = $rslt['s_id'];
        $personId = $rslt['s_person_id'];
        $stfResTypeId = $rslt['sr_staff_resource_type_id'];
        $stfResId = $rslt['sr_id'];
        $orgStfByRes[] = array('staff_resource_organization_id' => $stfResOrgId,
                               'organization_id' => $orgId,
                               'staff_id' => $staffId,
                               'staff_resource_type_id' => $stfResTypeId,
                               'staff_resource_id' => $stfResId,
                               'person_id' => $personId
                              );
      }
      return $orgStfByRes;
    }catch (\Doctrine\ORM\ORMException $e) {
      return NULL;
    }
  }
}
