<?php
/**
 * Extends the bulk record helper to provide contact-specific features.
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
abstract class agEntityContactHelper extends agBulkRecordHelper
{
  /**
   * Helper function to execute the reprioritization of contact info.
   *
   * @param array $entityContacts The array of new entity contacts to apply.
   * @param array $currContacts The array of current contacts as found in the database
   * @return array A reprioritized $entityContacts with additions from $currContacts as appropriate.
   */
  protected function reprioritizeContacts($entityContacts, $currContacts)
  {
    // first we need do just do a little cleanup on $currContacts to strip date/times
    foreach ($currContacts as $entityId => $currContact)
    {
      foreach ($currContact as $index => $oldContact)
      {
        $currContacts[$entityId][$index] = array($oldContact[0], $oldContact[1]) ;
      }
    }

    // now we loop through and do an inner array diff
    foreach($entityContacts as $entityId => $contacts)
    {
      //explicit declarations are good
      $newContacts = array() ;
      $currContact = array() ;

      // check to see if this is brand-spankin' new entity or old
      if (array_key_exists($entityId, $currContacts))
      {
        // we'll reuse this at the end so lets grab it once
        $currContact = $currContacts[$entityId] ;

        // here we do a little something crazy; we add ALL of the old contact info to the array
        foreach ($currContact as $oldContact)
        {
          $contacts[] = $oldContact ;
        }
      }

      // now that we've got all contact info on our $contacts array, let's reshape our array and exclude dupes
      // we intentionally don't use array_unique() here because types might differ and it is strict
      foreach ($contacts as $contact)
      {
        if (! in_array($contact, $newContacts))
        {
          $newContacts[] = $contact ;
        }
      }

      // now that we're out of the contact de-dupe loop we exclude the ones that haven't changed
      foreach($newContacts as $ncKey => $ncValue)
      {
        if (array_key_exists($ncKey, $currContact) && $ncValue == $currContact[$ncKey])
        {
          unset($newContacts[$ncKey]) ;
        }
      }

      // add our results to our final results array
      $entityContacts[$entityId] = $newContacts ;

      // might as well re-claim our memory here too
      unset($currContacts[$entityId]) ;
    }

    return $entityContacts ;
  }

  /**
   * Method to set entity contact data using contact id 's instead of values. Depends on the
   * $_contactTableMetadata property of its child class.
   *
   * @param array $entityContacts A multidimensional array of address contact information that
   * mimics the output of getEntityAddress($entityIds, FALSE, FALSE).
   * @param boolean $keepHistory An optional boolean value to determine whether old entity contacts
   * (eg, those stored in the database but not explicitly passed as parameters), will be retained
   * and reprioritized to the end of the list, or removed altogether.
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an address 'optional'.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return integer The number of operations performed.
   */
  public function setEntityContactById( $entityContacts,
                                        $keepHistory = NULL,
                                        $throwOnError = NULL,
                                        Doctrine_Connection $conn = NULL)
  {
    $tableMetadata = $this->_getContactTableMetadata() ;

    // explicit results declaration
    $results = array('upserted'=>0, 'removed'=>0, 'failures'=>array()) ;
    $currContacts = array() ;

    // get some defaults if not explicitly passed
    if (is_null($keepHistory)) { $keepHistory = $this->keepHistory ; }
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    if ($keepHistory)
    {
      // if we're going to process existing addresses and keep them, then hold on
      $currContacts = $this->$tableMetadata['method'](array_keys($entityContacts), FALSE, FALSE) ;
    }
    else
    {
      // if we're not going to keep a history, let's build a delete query we'll execute on each
      // entity
      $q = agDoctrineQuery::create($conn)
        ->delete($tableMetadata['table'])
        ->whereIn('entity_id', array_keys($entityContacts));
    }

    // execute the reprioritization helper and pass it our current addresses as found in the db
    $entityContacts = $this->reprioritizeContacts($entityContacts, $currContacts ) ;

    // define our blank collection
    $coll = new Doctrine_Collection($tableMetadata['table']) ;

    // loop through our entityContacts
    foreach ($entityContacts as $entityId => $contacts)
    {
      foreach($contacts as $index => $contact)
      {
        // create a doctrine record with this info
        $newRec = new $tableMetadata['table']() ;
        $newRec['entity_id'] = $entityId ;
        $newRec['priority'] = ($index + 1) ;
        $newRec[$tableMetadata['value']] = $contact[1] ;
        $newRec[$tableMetadata['type']] = $contact[0] ;

        // add the record to our collection
        $coll->add($newRec) ;

        // recover a little resource
        unset($entityContacts[$entityId][$index]) ;
      }
    }

   // here we check our current transaction scope and create a transaction or savepoint
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
      // if we're not keeping our history, just blow them all out!
      if (! $keepHistory) { $results['removed'] = $q->execute() ; }

      // execute our commit and, while we're at it, add our successes to the bin
      $coll->replace($conn) ;

      // commit, being sensitive to our nesting
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }

      // append to our results array
      $results['upserted'] = $results['upserted'] + count($coll) ;
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('%s failed at: %s', __FUNCTION__, $e->getMessage()) ;
      sfContext::getInstance()->getLogger()->err($errMsg) ;

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $e ; }

      $results['failures'] = array_keys($entityContacts) ;
    }

    return $results ;
  }

  /**
   * Returns the _contactTableMetadata array from the agEntityContactHelper child class.
   *
   * @return array The $_contactTableMetadata array of the child class.
   */
  protected function _getContactTableMetadata()
  {
    return $this->_contactTableMetadata ;
  }
}