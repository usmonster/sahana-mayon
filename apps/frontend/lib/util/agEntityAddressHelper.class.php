<?php
/**
 * Provides person name helper functions and inherits several methods and properties from the
 * EntityContactHelper.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEntityAddressHelper extends agAddressHelper
{
  public    $agAddressHelper,
            $defaultIsPrimary = FALSE,
            $defaultIsStrType = FALSE;

  protected $_batchSizeModifier = 2,
            $_contactTableMetadata = array( 'table' => 'agEntityAddressContact',
                                            'method' => 'getEntityAddress',
                                            'type' => 'address_contact_type_id',
                                            'value' => 'address_id');

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
    if (is_null($strType)) { $strType = $this->defaultIsStrType ; }

    // the most basic version of this query
    $q = agDoctrineQuery::create()
      ->select('eac.entity_id')
          ->addSelect('eac.address_id')
          ->addSelect('eac.created_at')
          ->addSelect('eac.updated_at')
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
   * process the addresses. This should be an agAddressHelper::ADDR_GET_* constant. If left NULL,
   * only address ID's will be returned.
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
    $addressHelperArgs = array(array()) ;

    // if primary is not passed, get the default
    if (is_null($primary)) { $primary = $this->defaultIsPrimary ; }

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
      $entityAddresses[$row[0]][$row[4]][] = array($row[1], $row[2], $row[3]) ;

      // here we build the mono-dimensional addressId array, excluding dupes as we go; only useful
      // if we're actually going to use the address helper
      if (! is_null($addressHelperMethod) && ! in_array($row[1], $addressHelperArgs[0]))
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

    // we can release the address helper args, since we don't need them anymore
    unset($addressHelperArgs) ;

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
          // flatten the results
          $addresses = $addresses[0] ;
          $addresses[0] = $formattedAddresses[$addresses[0]] ;

          $entityAddresses[$entityId][$addressType][0] = $addresses ;
        }
        // if not primary, we have one more loop in our return for another array nesting
        else
        {
          foreach ($addresses as $index => $address)
          {
            $entityAddresses[$entityId][$addressType][$index][0] = $formattedAddresses[$address[0]] ;
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
   * process the addresses. This should be an agAddressHelper::ADDR_GET_* constant. If left NULL,
   * only address ID's will be returned.
   * @param array $addressArgs An array of arguments to pass forward to the address helper.
   * @return array A three dimensional array, by entityId, then indexed from highest priority
   * address to lowest, with a third dimension containing the address type as index[0], and the
   * address value as index[1].
   * <code>
   * array( [$entityId] => array( array($firstPriorityType, $firstPriorityValue),
   *     array($secondPriorityType, $secondPriorityValue),
   *     ... )
   *   ... )
   * </code>
   */
  public function getEntityAddress ($entityIds = NULL,
                                    $strType = NULL,
                                    $primary = NULL,
                                    $addressHelperMethod = NULL,
                                    $addressArgs = array())
  {
    // initial results declarations
    $entityAddresses = array() ;
    $addressHelperArgs = array(array()) ;

    // if primary is not passed, get the default
    if (is_null($primary)) { $primary = $this->defaultIsPrimary ; }

    // build our query object
    $q = $this->_getEntityAddressQuery($entityIds, $strType) ;

    // if this is a primary query we add the restrictor, note this one is different
    // from the one used in the by-type method
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
      $entityAddresses[$row[0]][]= array($row[4],$row[1], $row[2], $row[3]) ;

      // here we build the mono-dimensional addressId array, excluding dupes as we go; only useful
      // if we're actually going to use the address helper
      if (! is_null($addressHelperMethod) && ! in_array($row[1], $addressHelperArgs[0]))
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
        // flatten for just one return
        $addresses = $addresses[0] ;
        $addresses[1] = $formattedAddresses[$addresses[1]] ;

        $entityAddresses[$entityId] = $addresses ;
      }
      // if not primary, we have one more loop in our return for another array nesting
      else
      {
        foreach ($addresses as $index => $address) 
        {
          $entityAddresses[$entityId][$index][1] = $formattedAddresses[$address[1]] ;
        }
      }
    }
   
    return $entityAddresses ;
  }

  /**
   * Method to set entity addresses by passing address components, keyed by element id.
   *
   * @param array $entityContacts An array of entity contact information. This is similar to the
   * output of getEntityAddress if no arguments are passed.
   * <code>
   * array(
   *   $entityId => array(
   *     array($addressContactTypeId, array(array($elementId => $value, ...),
   *      $addressStandardId,
   *      array(array($latitude, $longitude), ...), $matchScoreId)
   *      )),
   *     ...
   *   ), ...
   * )
   * </code>
   * @param <type> $addressGeo
   * @param boolean $keepHistory An optional boolean value to determine whether old entity contacts
   * (eg, those stored in the database but not explicitly passed as parameters), will be retained
   * and reprioritized to the end of the list, or removed altogether.
   * @param boolean $enforceComplete An optional boolean to control whether or not only complete
   * addresses will be returned. Defaults to using the class property of the same name.
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an address 'optional'). Defaults to the class
   * property of the same name.
   * @param Doctrine_Connection $conn An optional Doctrine connection object.
   * @return array An associative array of operations performed including the number of upserted
   * records, removed records, an a positional array of failed inserts.
   * @todo Hook up the addressGeo bits
   */
  public function setEntityAddress( $entityContacts,
                                    $addressGeo = array(), 
                                    $keepHistory = NULL,
                                    $enforceComplete = NULL,
                                    $throwOnError = NULL,
                                    Doctrine_Connection $conn = NULL)
  {
    // some explicit declarations at the top
    $uniqContacts = array() ;
    $err = NULL ;
    $errMsg = 'This is a generic ERROR for setEntityAddress. You should never receive this ERROR.
      If you have received this ERROR, there is an error with your ERROR handling code.' ;

    // determine whether or not we'll explicitly throw exceptions on error
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ; }
    
    // loop through our contacts and pull our unique addresses from the fire
    foreach ($entityContacts as $entityId => $contacts)
    {
      foreach ($contacts as $index => $contact)
      {
        // Trim leading and trailing spaces from contact values.
        foreach($contact[1][0] as $elem => $val)
        {
          $contact[1][0][$elem] = trim($val);
        }

        // find the position of the element or return false
        $pos = array_search($contact[1], $uniqContacts, TRUE) ;

        // need to be really strict here because we don't want any [0] positions throwing us
        if ($pos === FALSE)
        {
          // add it to our unique contacts array
          $uniqContacts[] = $contact[1] ;

          // the the most recently inserted key
          $pos = max(array_keys($uniqContacts)) ;
        }

        // either way we'll have to point the entities back to their addresses
        $entityContacts[$entityId][$index][1] = $pos ;
      }
    }

    // whelp, if we haven't loaded it already, let's get our address helper
    $addressHelper = $this->getAgAddressHelper() ;

    // here we check our current transaction scope and create a transaction or savepoint
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }

    try
    {
      // process addresses, setting or returning, whichever is better with our s/getter
      $uniqContacts = $addressHelper->setAddresses($uniqContacts, $addressGeo,
        $enforceComplete, $throwOnError, $conn) ;
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('Could not set addresses %s. Rolling back!', json_encode($uniqContacts)) ;

      // hold onto this exception for later
      $err = $e ;
    }

    if (is_null($err))
    {
      // now loop through the contacts again and give them their real values
      foreach ($entityContacts as $entityId => $contacts)
      {
        foreach ($contacts as $index => $contact)
        {
          // check to see if this index found in our 'unsettable' return from setAddresses
          if (array_key_exists($contact[1], $uniqContacts[1]))
          {
            // purge this address
            unset($entityContacts[$entityId][$index]) ;
          }
          else
          {
            // otherwise, get our real addressId
            $entityContacts[$entityId][$index][1] = $uniqContacts[0][$contact[1]] ;
          }
        }
      }

      // we're done with uniqContacts now
      unset($uniqContacts) ;

      try
      {
        // just submit the entity addresses for setting
        $results = $this->setEntityContactById($entityContacts, $keepHistory, $throwOnError, $conn);
      }
      catch(Exception $e)
      {
        // log our error
        $errMsg = sprintf('Could not set entity addresses %s. Rolling Back!',
          json_encode($entityContacts)) ;

        // hold onto this exception for later
        $err = $e ;
      }
    }

    // check to see if we had any errors along the way
    if (! is_null($err))
    {
      // log our error
      sfContext::getInstance()->getLogger()->err($errMsg) ;

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $err ; }
    }

    // most excellent! no errors at all, so we commit... finally!
    if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }

    return $results ;
  }
}