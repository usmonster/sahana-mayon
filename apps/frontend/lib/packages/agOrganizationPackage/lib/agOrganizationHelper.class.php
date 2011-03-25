<?php

/**
 * agOrganizationHelper this class extends the bulk record helper
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Shirley Chan, CUNY SPS
 * @author     Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agOrganizationHelper extends agBulkRecordHelper
{
  public    $showDisabledStaff = FALSE,
            $agStaffResourceHelper;

  /**
   * Method to construct our base organization staff query object.
   *
   * @param array $organizationIds An array of organization Ids. If left null, method will attempt
   * to collect ids from the $recordIds property.
   * @param boolean $showDisabledStaff Boolean to determine whether or not disabled staff will also
   * be returned. Defaults to the $showDisabledStaff class property.
   * @return Doctrine_Query A doctrine query object.
   */
  protected function _getOrganizationStaff($organizationIds = NULL, $showDisabledStaff = NULL)
  {
    // safe the values and return $recordIds if null
    $organizationIds = $this->getRecordIds($organizationIds) ;

    // pick up our default showDisabled status
    if (is_null($showDisabledStaff)) { $showDisabledStaff = $this->showDisabledStaff ; }

    // construct our query object
    $q = agDoctrineQuery::create()
      ->select('sro.organization_id')
          ->addSelect('sro.staff_resource_id')
          ->addSelect('sro.id')
        ->from('agStaffResourceOrganization sro')
        ->whereIn('sro.organization_id', $organizationIds) ;

    // join in the additional status information if show disabled is set false so that we exclude
    // the disabled, which is not the same as handicapable.
    if (! $showDisabledStaff)
    {
      $q->innerJoin('sro.agStaffResource sr')
        ->innerJoin('sr.agStaff s')
        ->innerJoin('s.agStaffStatus ss')
        ->andWhere('ss.is_available = ?', TRUE) ;
    }

    return $q ;
  }

  /**
   * Method to lazily load the $agStaffResourceHelper class property (an instance of
   * agStaffResourceHelper).
   * @return object The instantiated agStaffResourceHelper object
   */
  public function getAgStaffResourceHelper()
  {
    // lazily load our resource helper if it's not already set
    if (! isset($this->agStaffResourceHelper))
    {
      $this->agStaffResourceHelper = agStaffResourceHelper::init() ;
    }

    // also return the helper as an object
    return $this->agStaffResourceHelper ;
  }

  /**
   * Method to return organization staff by id.
   *
   * @param array $organizationIds An array of organization Ids. If left null, method will attempt
   * to collect ids from the $recordIds property.
   * @param boolean $showDisabledStaff Boolean to determine whether or not disabled staff will also
   * be returned. Defaults to the $showDisabledStaff class property.
   * @return array An associative array of organization staff keyed by organization id, keyed by
   * staff resource id, with a value as the staffResourceOrganizationId.
   */
  public function getOrganizationStaff($organizationIds = NULL, $showDisabledStaff = NULL)
  {
    // execute our query and get an associative results set
    $q = $this->_getOrganizationStaff($organizationIds, $showDisabledStaff) ;

    return $q->execute(array(), 'assoc_two_dim') ;
  }

  /**
   * Method to return several staff resource values, keyed by organizaton association.
   *
   * @param array $organizationIds An array of organization Ids. If left null, method will attempt
   * to collect ids from the $recordIds property.
   * @param boolean $showDisabledStaff Boolean to determine whether or not disabled staff will also
   * be returned. Defaults to the $showDisabledStaff class property.
   * @return array An associative array of organization staff keyed by organization id, keyed by
   * staff resource id, with a value as an array of staff resource values.
   */
  public function getOrganizationStaffInfo($organizationIds = NULL, $showDisabledStaff = NULL)
  {
    // get our original array of organization staff (only with the staff_resource_id)
    $organizationStaff = $this->getOrganizationStaff($organizationIds, $showDisabledStaff) ;

    // lazily load our staff resource helper
    $this->getAgStaffResourceHelper() ;

    // loop through each of the organizations
    foreach ($organizationStaff as $organizationId => $staffResources)
    {
      // grab just our staff resource Id's for the staff resource helper
      $staffResourceIds = array_keys($staffResources) ;

      // grab all of the staff info for those staff resource ids
      $staffInfo= $this->agStaffResourceHelper
        ->getStaffResourceComponents($staffResourceIds, TRUE, agPersonNameHelper::PRIMARY_AS_STRING) ;

      // loop through the staff info and add the staff resource organization id value from our
      // original call to getOrganizationStaff() to our value array
      foreach ($staffInfo as $staffResourceId => $components)
      {
        $staffInfo[$staffResourceId]['staff_resource_organization_id'] = $staffResources[$staffResourceId] ;
      }

      // overwrite all of this as our value for an organization
      $organizationStaff[$organizationId] = $staffInfo ;
    }

    return $organizationStaff ;
  }
}