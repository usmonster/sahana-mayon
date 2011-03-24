<?php
/**
 * Extends the bulk record helper to provide contact-specific features.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
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
   * @todo Add the $keepHistory functionality 
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
}