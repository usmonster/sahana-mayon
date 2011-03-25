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
class agEntityAddressHelper extends agEntityContactHelper
{
  public    $agAddressHelper,
            $defaultIsPrimary = FALSE,
            $defaultIsStrType = FALSE;

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
   * Method to set entity address data using address ID 's instead of values.
   * 
   * @param array $entityContacts A multidimensional array of address contact information that
   * mimics the output of getEntityAddress($entityIds, FALSE, FALSE).
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return integer The number of operations performed.
   * @todo Add the $keepHistory functionality
   * @todo make results more meaningful (with errs)
   */
  public function setEntityAddressById( $entityContacts,
                                        $keepHistory = TRUE,
                                        Doctrine_Connection $conn = NULL)
  {
    $tableName = 'agEntityAddressContact' ;

    // explicit results declaration
    $results = array('upserted'=>0, 'removed'=>0, 'failures'=>array()) ;
    $currContacts = array() ;


    // set our connection object if not explicitly passed one
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    if ($keepHistory)
    {
      // if we're going to process existing addresses and keep them, then hold on
      $currContacts = $this->getEntityAddress(array_keys($entityContacts, FALSE, FALSE)) ;
    }
    else
    {
      // if we're not going to keep a history, let's build a delete query we'll execute on each
      // entity
      $q = agDoctrineQuery::create($conn)
        ->delete($tableName . ' ec') ;
    }

    // execute the reprioritization helper and pass it our current addresses as found in the db
    $entityContacts = $this->reprioritizeContacts($entityContacts, $currContacts ) ;


    // loop through our entityContacts
    foreach ($entityContacts as $entityId => $contacts)
    {
      // define our blank collection
      $coll = new Doctrine_Collection($tableName) ;

      foreach($contacts as $index => $contact)
      {
        // create a doctrine record with this info
        $newRec = new agEntityAddressContact() ;
        $newRec['entity_id'] = $entityId ;
        $newRec['priority'] = ($index + 1) ;
        $newRec['address_id'] = $contact[1] ;
        $newRec['address_contact_type_id'] = $contact[0] ;

        // add the record to our collection
        $coll->add($newRec) ;
      }

      // add our delete query to our where clause ;
      $q->where('ec.entity_id = ?', $entityId) ;

      // wowee, zowee, now that the hard stuff's done, let's just commit this sucker
      $conn->beginTransaction() ;
      try
      {
        // if we're not keeping our history, just blow them all out!
        if (! $keepHistory) { $results['removed'] = $results['removed'] + $q->execute() ; }

        // execute our commit and, while we're at it, add our successes to the bin
        $coll->replace() ;
        $conn->commit() ;
        $results['upserted'] = $results['upserted'] + count($coll) ;
      }
      catch(Exception $e)
      {
        // if we run into a problem, rollback and add the failed entity to the failures bin
        $conn->rollback() ;
        $results['failures'][] = $entityId ;
      }
    }

    return $results ;
  }

  /**
   *
   * @param <type> $entityContacts
   * @param <type> $keepHistory
   * @param <type> $enforceComplete
   * @param Doctrine_Connection $conn
   * @todo Figure out what our purge policy is going to be (failed inserts)
   */
  public function setEntityAddress( $entityContacts,
                                    $keepHistory = NULL,
                                    $enforceComplete = NULL,
                                    Doctrine_Connection $conn = NULL)
  {
    // some explicit declarations at the top
    $uniqContacts = array() ;
    $wontSet = array() ;

    // loop through our contacts and pull our unique addresses from the fire
    foreach ($entityContacts as $entityId => $contacts)
    {
      foreach ($contacts as $index => $contact)
      {
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

    // process addresses, setting or returning, whichever is better with our s/getter
    $uniqContacts = $addressHelper->setAddresses($uniqContacts, $enforceComplete, $conn) ;

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

    // just submit the entity addresses for setting
    $results = $this->setEntityAddressById($entityContacts, $keepHistory) ;

    return $results ;
  }

  public function exceptionTest()
  {
    // set our connection object if not explicitly passed one
   $conn = Doctrine_Manager::connection() ; 

    $q = agDoctrineQuery::create($conn)
      ->update('agGlobalParam')
        ->set('value', 20009)
        ->where('datapoint = ?', 'default_batch_size') ;

    $savepoint = __FUNCTION__  ;
    print_r($q->getConnection()->getTransactionLevel() . ', ') ;

    $conn->beginTransaction() ;
    $conn->beginTransaction($savepoint) ;
    $updates = $q->execute() ;
    $conn->rollback() ;
    $conn->commit() ;

    return $updates ;
  }
}