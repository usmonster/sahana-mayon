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
  public      $showDisabledStaff = FALSE;
  protected   $_globalDefaultOrganization = 'default_organization';

  /**
   * Method used to construct the base query object used by other objects in this class.
   *
   * @param string $organization A string value of an organization.
   * @return agDoctrineQuery An extended doctrine query object.
   */
  protected function _getOrganization($organization)
  {
    // construct our base query object
    $q = agDoctrineQuery::create()
      ->select('o.id')
        ->from('agOrganization o')
        ->where('o.organization = ?', $organization);

    return $q;
  }

  /**
   * Simple method to return the current phone format id.
   *
   * @return integer organization_id
   */
  public function getDefaultOrganizationId()
  {
    $defaultOrganization = agGlobal::getParam($this->_globalDefaultOrganization);
    $q = $this->_getOrganization($defaultOrganization);
    $organizationId = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    return $organizationId;
  }

}