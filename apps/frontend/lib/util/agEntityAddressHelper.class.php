<?php
/**
 * Provides person name helper functions and inherits several methods and properties from the
 * bulk record helper.
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEntityAddressHelper extends agBulkRecordHelper
{
  public    $agAddressHelper,
            $defaultFetchPrimary = FALSE,
            $defaultFetchStrType = FALSE;

  protected $_batchSizeModifier = 2 ;

  /**
   * Method to lazily load the $agAddressHelper class property (an instance of agAddressHelper)
   * @return object The instantiated agAddressHelper object
   */
  public function getAgAddressHelper()
  {
    if (! isset($this->agAddressHelper)) { $this->agAddressHelper = agAddressHelper::init() ; }
    return $this->agAddressHelper ;
  }

  /**
   * Method to return an agDocrineQuery object, preconfigured to collect entity addresses.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the address contact type will
   * be returned as an ID value or its string equivalent.
   * @return Doctrine_Query An agDoctrineQuery object.
   */
  private function _getEntityAddressQuery($entityIds = NULL, $strType = NULL)
  {
    // if no (null) ID's are passed, get the addressId's from the class property
    $entityIds = $this->getRecordIds($entityIds) ;

    // if strType is not passed, get the default
    if (is_null($strType)) { $strType = $this->defaultFetchStrType ; }

    // the most basic version of this query
    $q = agDoctrineQuery::create()
      ->select('eac.entity_id')
          ->addSelect('eac.address_id')
        ->from('agEntityAddressContact eac')
        ->whereIn('eac.entity_id', $entityIds)
        ->orderBy('eac.priority') ;

    // here we determine whether to return the address_contact_type_id or its string value
    if ($strType)
    {
      $q->addSelect('act.address_contact_type')
        ->innerJoin('eac.agAddressContactType act') ;
    }
    else
    {
      $q->addSelect('eac.address_contact_type_id') ;
    }

    return $q ;
  }

  /**
   * Method to return entity addresses for a group of entity ids, sorted from highest priority to
   * lowest priority, and grouped by the address contact type.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the address contact type will
   * be returned as an ID value or its string equivalent.
   * @param boolean $primary Boolean that determines whether or not only the primary address will
   * be returned (for that type).
   * @param string $addressHelperMethod The address helper method that will be called to format or
   * process the addresses. This should be an agAddressHelper::ADDR_GET_* constant.
   * @param array $addressArgs An array of arguments to pass forward to the address helper.
   * @return array A two or three dimensional array (depending up on the setting of the $primary
   * parameter), by entityId, by addressContactType.
   */
  public function getEntityAddressByType ($entityIds = NULL,
                                          $strType = NULL,
                                          $primary = NULL,
                                          $addressHelperMethod = NULL,
                                          $addressArgs = array())
  {
    // initial results declarations
    $entityAddresses = array() ;
    $addressIds = array() ;

    // if primary is not passed, get the default
    if (is_null($primary)) { $primary = $this->defaultFetchPrimary ; }

    // build our query object
    $q = $this->_getEntityAddressQuery($entityIds, $strType) ;

    // if this is a primary query we add the restrictor
    if ($primary)
    {
      $q->addWhere(' EXISTS (
        SELECT s.id
          FROM agEntityAddressContact s
          WHERE s.entity_id = eac.entity_id
            AND s.address_contact_type_id = eac.address_contact_type_id
          HAVING MIN(s.priority) = eac.priority )') ;
    }

    // build this as custom hydration to 'double tap' the data
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE) ;
    foreach ($rows as $row)
    {
      $entityAddresses[$row[0]][$row[2]][] = $row[1] ;

      // here we build the mono-dimensional addressId array, excluding dupes as we go; only useful
      // if we're actually going to use the address helper
      if (! is_null($addressHelperMethod) && ! in_array($row[2], $addressIds))
      {
        $addressHelperArgs[0][] = $row[1] ;
      }
    }

    // if no address helper method was passed, assume that all we need are the address id's and
    // stop right here!
    if (is_null($addressHelperMethod))
    {
      return $entityAddresses ;
    }

    // otherwise... we keep going and lazily load our address helper, 'cause we'll need her
    $addressHelper = $this->getAgAddressHelper() ;
    
    // finish appending the rest of our address helper args
    foreach ($addressArgs as $arg)
    {
      $addressHelperArgs[] = $arg ;
    }

    // use the address helper to format the address results
    $userFunc = array($addressHelper,$addressHelperMethod) ;
    $formattedAddresses = call_user_func_array($userFunc,$addressHelperArgs) ;

    // now loop through our entities and attach their addresses
    foreach ($entityAddresses as $entityId => $addressTypes)
    {
      foreach ($addressTypes as $addressType => $addresses)
      {
        // if we're only returning the primary, change the third dimension from an array to a value
        // NOTE: because of the restricted query, we can trust there is only one component per type
        // in our output and safely make this assumption
        if ($primary)
        {
          $entityAddresses[$entityId][$addressType] = $formattedAddresses[$addresses[0]] ;
        }
        // if not primary, we have one more loop in our return for another array nesting
        else
        {
          foreach ($addresses as $index => $address)
          {
            $entityAddresses[$entityId][$addressType][$index] = $formattedAddresses[$address] ;
          }
        }
      }
    }

    return $entityAddresses ;
  }

  /**
   * Method to return entity addresses for a group of entity ids, sorted from highest priority to
   * lowest priority.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the address contact type will
   * be returned as an ID value or its string equivalent.
   * @param boolean $primary Boolean that determines whether or not only the primary address will
   * be returned (for that type).
   * @param string $addressHelperMethod The address helper method that will be called to format or
   * process the addresses. This should be an agAddressHelper::ADDR_GET_* constant.
   * @param array $addressArgs An array of arguments to pass forward to the address helper.
   * @return array A three dimensional array, by entityId, then indexed from highest priority
   * address to lowest, with a third dimension containing the address type as index[0], and the
   * address value as index[1].
   */
  public function getEntityAddress ($entityIds = NULL,
                                    $strType = NULL,
                                    $primary = NULL,
                                    $addressHelperMethod = NULL,
                                    $addressArgs = array())
  {
    // initial results declarations
    $entityAddresses = array() ;
    $addressIds = array() ;

    // if primary is not passed, get the default
    if (is_null($primary)) { $primary = $this->defaultFetchPrimary ; }

    // build our query object
    $q = $this->_getEntityAddressQuery($entityIds, $strType) ;

    // if this is a primary query we add the restrictor
    if ($primary)
    {
      $q->addWhere(' EXISTS (
        SELECT s.id
          FROM agEntityAddressContact s
          WHERE s.entity_id = eac.entity_id
          HAVING MIN(s.priority) = eac.priority )') ;
    }
    // build this as custom hydration to 'double tap' the data
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE) ;
    foreach ($rows as $row)
    {
      $entityAddresses[$row[0]][]= array($row[2],$row[1]) ;

      // here we build the mono-dimensional addressId array, excluding dupes as we go; only useful
      // if we're actually going to use the address helper
      if (! is_null($addressHelperMethod) && ! in_array($row[2], $addressIds))
      {
        $addressHelperArgs[0][] = $row[1] ;
      }
    }

    // if no address helper method was passed, assume that all we need are the address id's and
    // stop right here!
    if (is_null($addressHelperMethod))
    {
      return $entityAddresses ;
    }

    // otherwise... we keep going and lazily load our address helper, 'cause we'll need her
    $addressHelper = $this->getAgAddressHelper() ;

    // finish appending the rest of our address helper args
    foreach ($addressArgs as $arg)
    {
      $addressHelperArgs[] = $arg ;
    }

    // use the address helper to format the address results
    $userFunc = array($addressHelper,$addressHelperMethod) ;
    $formattedAddresses = call_user_func_array($userFunc,$addressHelperArgs) ;

    // now loop through our entities and attach their addresses
    foreach ($entityAddresses as $entityId => $addresses)
    {
      // if we're only returning the primary, change the second dimension from an array to a value
      // NOTE: because of the restricted query, we can trust there is only one component per type
      // in our output and safely make this assumption
      if ($primary)
      {
        $entityAddresses[$entityId] = array($addresses[0][0],$formattedAddresses[$addresses[0][1]]) ;
      }
      // if not primary, we have one more loop in our return for another array nesting
      else
      {
        foreach ($addresses as $index => $address) 
        {
          $entityAddresses[$entityId][$index][1] = array($formattedAddresses[$address[1]]) ;
        }
      }
    }
   
    return $entityAddresses ;
  }
}