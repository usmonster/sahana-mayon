<?php

/**
 * PluginagStaffResourceType is an extension of base class agStaffResourceType
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
abstract class PluginagStaffResourceType extends BaseagStaffResourceType
{
  public function __toString() {
    return $this->getStaffResourceType();
  }
   /**
   * staffResourceTypeInArray() is a static method to return an array of staff resource type.
   *
   * @param boolean $app_display_true_only - If param passed in as true, only
   *   return an array of staff resource type where table field app_display is
   *   true.  Otherwise, return all staff resource type defined in the database
   *   regardless of the app_display field.
   */
  static public function staffResourceTypeInArray($app_display_true_only = false)
  {
    try
    {
      // Collect all staff resource types.
      $staffResourceTypeQuery = agDoctrineQuery::create()
        ->select('srt.*')
        ->from('agStaffResourceType as srt');

      if (is_bool($app_display_true_only) and $app_display_true_only)
      {
        $staffResourceTypeQuery->where('app_display = ?', ($app_display_true_only));
      }

      $staffResourceTypeQuery->orderBy('staff_resource_type');
      $results = $staffResourceTypeQuery->execute();

      $staffResourceTypeList = array();
      foreach ($results as $srt)
      {
        $staffResourceTypeList[$srt->getId()] = $srt->getStaffResourceType();
      }

      return $staffResourceTypeList;
    } catch (\Doctrine\ORM\ORMException $e) {
      return NULL;
    }
  }

}