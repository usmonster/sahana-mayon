<?php

/**
 * agStaff is an extension of base class agStaff
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agStaff extends BaseagStaff
{
//  /**
//   * staffCount() is a static method to return one of the following types of staff count:
//   *   (1) Total count of unique staff
//   *   (2) Total count of unique staff within a specified organization
//   *   (3) Total count of unique staff within a specified staff resource type
//   *   (4) Total count of unique staff within a specificed organization and staff resource type
//   *
//   * @param boolean $byStaffResourceType - If param passed in as true, set query condition to staff resource type specific.
//   * @param integer $staffResourceTypeId - Staff resource type id.  This is applied to the query condition if $byStaffResourceType is true.
//   * @param boolean $byOrganization - If param passed in as true, set query condition to organization specific.
//   * @param integer $organizationId - Organization id.  This is applied to the query condition if $byOrganization is true.
//   */
//  static public function staffCount($byStaffResourceType = false, $staffResourceTypeId = null, $byOrganization = false, $organizationId = null)
//  {
//    try
//    {
//      $staffCount = 0;
//      $staffResourceCount = Doctrine_Core::getTable('agStaffResource')
//        ->createQuery('sr')
//        ->select('count(*) as count')
//        ->execute();
//
//      /*
//       * Return 0 staff count if agStaffResource table is empty.
//       * Cannot associate staff resource to organization if there are no staff
//       * resource defined.
//       */
//      if ($staffResourceCount[0]['count'] == 0)
//      {
//        return $staffCount;
//      }
//
//      $staffResourceCountQuery = Doctrine_query::create()
//        ->select('count(distinct sr.staff_id)')
//        ->from('agStaffResource as sr')
//        ->leftJoin('sr.agStaffResourceOrganization as sro')
//        ->where('1=1');
//
//      if (is_bool($byStaffResourceType) and $byStaffResourceType)
//      {
//        $staffResourceCountQuery->addWhere('sr.staff_resource_type_id=?', ($staffResourceTypeId));
//      }
//
//      if (is_bool($byOrganization) and $byOrganization)
//      {
//        $staffResourceCountQuery->addWhere('sro.organization_id=?', ($organizationId));
//      }
//
//      $staffCountResults = $staffResourceCountQuery->execute(array(), Doctrine::HYDRATE_ARRAY);
//      $staffCount = $staffCountResults[0]['count'];
//
//      return $staffCount;
//    } catch (\Doctrine\ORM\ORMException $e) {
//      return NULL;
//    }
//  }

  /**
   * getUniqueStaffCount() is a static method that returns different result sets depending on the pass-in groupByMode:
   *   groupByMode 1 - returns an integer total count of unique staff.
   *   groupByMode 2 - returns an array of array( organization id => array( staff resource type id => unique staff count ) )
   *   groupByMode 3 - returns an array of array( organization id => unique staff count )
   *   groupByMode 4 - returns an array of array( staff resource type id => unique staff count )
   *
   * NOTE: Method returns 0 if no staff is defined in agStaff table.
   *
   * @param integer $groupByMode Accepts integer value ranging from 1-4.
   * @param array $organizationIds (Optional) Queries staff count for the specified organizations only.
   *   Note: This param is ignored in groupByMode 1 and 4.
   * @param array $staffResourceTypeIds (Optional) Queries staff count for the specified staff resource type only.
   *   Note: This param is ignored in groupByMode 1 and 3.
   */
  static public function getUniqueStaffCount($groupByMode, $organizationIds = array(), $staffResourceTypeIds = array())
  { // Need to combine staffcount and newStaffCount to one function.
    try
    {
      // Should check table agStaff is emtpy.  If empty, there is no staff.
//      $staffCount = 0;
//      $staff = Doctrine_Core::getTable('agStaff')
//        ->createQuery('s')
//        ->select('count(*) as count')
//        ->execute();
//
//      /*
//       * Return 0 staff count if agStaff table is empty.
//       */
//      if ($staff[0]['count'] == 0)
//      {
//        return $staffCount;
//      }

      switch ($groupByMode)
      {
        case 1:
          /**
           * Returns an integer of the total staff count.
           */
          $staffCount = Doctrine_Query::create()
            ->select('count(*) as count')
            ->from('agStaff as s')
            ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
          return $staffCount;

        case 2:
          $query = Doctrine_Query::create()
            ->select('sro.organization_id as orgId, sr.staff_resource_type_id as stfRsrcTypId, count(distinct sr.staff_id) as count')
            ->from('agStaff as s')
            ->innerJoin('s.agStaffResource as sr')
            ->innerJoin('sr.agStaffResourceOrganization as sro')
            ->where('1=1');

          /*
           * Append a where clause to query for specific organizations if an
           * array of organziation id is passed in as argument.
           */
          if (is_array($organizationIds) and count($organizationIds) > 0)
          {
            $query->whereIn('sro.organization_id', $organizationIds);
          }

          /*
           * Append a where clause to query for specific staff resource type if
           * an array of staff resource type id is passed in as argument.
           */
          if (is_array($staffResourceTypeIds) and count($staffResourceTypeIds) > 0)
          {
            $query->whereIn('sr.staff_resource_type_id', $staffResourceTypeIds);
          }

          $query->groupBy('orgId, stfRsrcTypId');

          break;

        case 3:
          $query = Doctrine_Query::create()
            ->select('o.id as orgId, count(distinct sr.staff_id) as count')
            ->from('agOrganization as o')
            ->leftJoin('o.agStaffResourceOrganization as sro')
            ->leftJoin('sro.agStaffResource as sr')
            ->where('1=1');

          /*
           * Append a where clause to query for specific organizations if an
           * array of organziation id is passed in as argument.
           */
          if (is_array($organizationIds) and count($organizationIds) > 0)
          {
            $query->whereIn('o.id', $organizationIds);
          }

          $query->groupBy('orgId');

          break;

        case 4:
          $query = Doctrine_Query::create()
            ->select('srt.id as stfRsrcTypId, count(distinct s.id) as count')
            ->from('agStaffResourceType as srt')
            ->leftJoin('srt.agStaff as s')
            ->where('1=1');

          /*
           * Append a where clause to query for specific staff resource type if
           * an array of staff resource type id is passed in as argument.
           */
          if (is_array($staffResourceTypeIds) and count($staffResourceTypeIds) > 0)
          {
            $query->whereIn('srt.id', $staffResourceTypeIds);
          }

          $query->groupBy('srt.id');

          break;

        default:
          // Returns a string of error message if an invalid groupByMode is passed-in.
          return 'Invalid mode.';
      }

      $resultSet = $query->execute(array(), Doctrine::HYDRATE_SCALAR);

      $staffCount = array();
      switch ($groupByMode)
      {
        // Populates $staffCount = array( organization id => array( staff resource type id => staff count by organization and staff resource type ) ).
        case 2:
          foreach($resultSet as $rslt)
          {
            $key1 = $rslt['sro_orgId'];
            $key2 = $rslt['sr_stfRsrcTypId'];
            $value = $rslt['sr_count'];
            $tempArray = array($key2 => $value);
            if (array_key_exists($key1, $staffCount)) {
              $oldArray = $staffCount[$key1];
              $newArray = $oldArray + $tempArray;
              $staffCount[$key1] = $newArray;
            } else {
              $staffCount[$key1] = array($key2 => $value);
            }
          }
          break;

        // Populates $staffCount = array( organization id => staff count by organization ).
        case 3:
          foreach($resultSet as $rslt)
          {
            $key = $rslt['o_orgId'];
            $value = $rslt['sr_count'];
            $staffCount[ $key ] = $value;
          }
          break;

        // Populates $staffCount = array( staff resource type id => staff count by staff resource type ).
        case 4:
          foreach($resultSet as $rslt)
          {
            $key = $rslt['srt_stfRsrcTypId'];
            $value = $rslt['s_count'];
            $staffCount[ $key ] = $value;
          }
          break;
      }

      return $staffCount;
    } catch (\Doctrine\ORM\ORMException $e) {
      return NULL;
    }
  }

}
