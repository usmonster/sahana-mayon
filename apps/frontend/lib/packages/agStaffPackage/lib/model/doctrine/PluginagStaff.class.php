<?php

/**
 * PluginagStaff is an extension of base class agStaff
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
abstract class PluginagStaff extends BaseagStaff
{

  protected $isAutoIndexed;

  public function __construct($table = null, $isNewEntry = false, $isAutoIndexed = true)
  {
    parent::__construct($table, $isNewEntry);
    $this->isAutoIndexed = $isAutoIndexed;
  }

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
  static public function getUniqueStaffCount($groupByMode, $organizationIds = array(),
                                             $staffResourceTypeIds = array())
  { // Need to combine staffcount and newStaffCount to one function.
    try {
      switch ($groupByMode) {
        case 1:
          /**
           * Returns an integer of the total staff count.
           */
          $staffCount = agDoctrineQuery::create()
                  ->select('count(*) as count')
                  ->from('agStaff as s')
                  ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
          return $staffCount;

        case 2:
          $query = agDoctrineQuery::create()
                  ->select('sr.organization_id as orgId, sr.staff_resource_type_id as stfRsrcTypId, count(distinct sr.staff_id) as count')
                  ->from('agStaffResource as sr');
//                  ->innerJoin('sr.agStaffResourceOrganization as sro')
//                  ->where('1=1');

          /*
           * Append a where clause to query for specific organizations if an
           * array of organziation id is passed in as argument.
           */
          if (is_array($organizationIds) and count($organizationIds) > 0) {
            $query->whereIn('sr.organization_id', $organizationIds);
          }

          /*
           * Append a where clause to query for specific staff resource type if
           * an array of staff resource type id is passed in as argument.
           */
          if (is_array($staffResourceTypeIds) and count($staffResourceTypeIds) > 0) {
            $query->whereIn('sr.staff_resource_type_id', $staffResourceTypeIds);
          }

          $query->groupBy('orgId, stfRsrcTypId');

          break;

        case 3:
          $query = agDoctrineQuery::create()
                  ->select('o.id as orgId, count(distinct sr.staff_id) as count')
                  ->from('agOrganization as o')
//                  ->leftJoin('o.agStaffResourceOrganization as sro')
                  ->leftJoin('o.agStaffResource as sr');
//                  ->where('1=1');

          /*
           * Append a where clause to query for specific organizations if an
           * array of organziation id is passed in as argument.
           */
          if (is_array($organizationIds) and count($organizationIds) > 0) {
            $query->whereIn('o.id', $organizationIds);
          }

          $query->groupBy('orgId');

          break;

        case 4:
          $query = agDoctrineQuery::create()
                  ->select('srt.id as stfRsrcTypId, count(distinct s.id) as count')
                  ->from('agStaffResourceType as srt')
                  ->leftJoin('srt.agStaff as s');
          //                ->where('1=1');

          /*
           * Append a where clause to query for specific staff resource type if
           * an array of staff resource type id is passed in as argument.
           */
          if (is_array($staffResourceTypeIds) and count($staffResourceTypeIds) > 0) {
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
      switch ($groupByMode) {
        // Populates $staffCount = array( organization id => array( staff resource type id => staff count by organization and staff resource type ) ).
        case 2:
          foreach ($resultSet as $rslt) {
            $key1 = $rslt['sr_orgId'];
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
          foreach ($resultSet as $rslt) {
            $key = $rslt['o_orgId'];
            $value = $rslt['sr_count'];
            $staffCount[$key] = $value;
          }
          break;

        // Populates $staffCount = array( staff resource type id => staff count by staff resource type ).
        case 4:
          foreach ($resultSet as $rslt) {
            $key = $rslt['srt_stfRsrcTypId'];
            $value = $rslt['s_count'];
            $staffCount[$key] = $value;
          }
          break;
      }

      return $staffCount;
    } catch (\Doctrine\ORM\ORMException $e) {
      return NULL;
    }
  }

}