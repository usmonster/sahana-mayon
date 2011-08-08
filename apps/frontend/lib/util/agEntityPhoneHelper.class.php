<?php
/**
 * Provides entity phone helper functions and inherits several methods and properties from the
 * bulk record helper.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEntityPhoneHelper extends agEntityContactHelper
{
  public    $agPhoneHelper,
            $defaultIsPrimary = FALSE,
            $defaultIsStrType = FALSE;

  protected $_batchSizeModifier = 2, 
            $_contactTableMetadata = array( 'table' => 'agEntityPhoneContact',
                                            'method' => 'getEntityPhone',
                                            'type' => 'phone_contact_type_id',
                                            'value' => 'phone_contact_id');

  /**
   * Method to lazily load the $agPhoneHelper class property (an instance of agPhoneHelper)
   * @return object The instantiated agPhoneHelper object
   */
  public function getAgPhoneHelper()
  {
    if (! isset($this->agPhoneHelper)) { $this->agPhoneHelper = agPhoneHelper::init() ; }
    return $this->agPhoneHelper ;
  }

  /**
   * Method to return an agDocrineQuery object, preconfigured to collect entity phones.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the phone contact type will
   * be returned as an ID value or its string equivalent.
   * @return Doctrine_Query An agDoctrineQuery object.
   */
  private function _getEntityPhoneQuery($entityIds = NULL, $strType = NULL)
  {
    // if no (null) ID's are passed, get the entityIds from the class property
    $entityIds = $this->getRecordIds($entityIds);

    // if strType is not passed, get the default
    if (is_null($strType)) { $strType = $this->defaultIsStrType; }

    // the most basic version of this query
    $q = agDoctrineQuery::create()
       ->select('epc.entity_id')
         ->addSelect('epc.phone_contact_id')
         ->addSelect('epc.created_at')
         ->addSelect('epc.updated_at')
       ->from('agEntityPhoneContact epc')
       ->whereIn('epc.entity_id', $entityIds)
       ->orderBy('epc.priority');

    // here we determine whether to return the phone_contact_type_id or its string value
    if ($strType)
    {
      $q->addSelect('pct.phone_contact_type')
        ->innerJoin('epc.agPhoneContactType pct');
    } else {
      $q->addSelect('epc.phone_contact_type_id');
    }

    return $q;
  }

  /**
   * Method to return entity phones for a group of entity ids, sorted from highest priority to
   * lowest priority, and grouped by the phone contact type.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the phone contact type will
   * be returned as an ID value or its string equivalent.
   * @param boolean $primary Boolean that determines whether or not only the primary phone will
   * be returned (for that type).
   * @param boolean $formatPhone Boolean that determines whether or not to format the phone.
   * @param string $phoneHelperMethod The phone helper method that will be called to format or
   * process the phones. This should be an agPhoneHelper::PHN_GET_* constant. If left NULL,
   * only phone ID's will be returned.
   * @param array $phoneArgs An array of arguments to pass forward to the phone helper.
   * @return array A two or three dimensional array (depending on the setting of the $primary
   * parameter), by entityId, by phoneContactType.
   */
  public function getEntityPhoneByType ($entityIds = NULL,
                                        $strType = NULL,
                                        $primary = NULL,
                                        $phoneHelperMethod = NULL,
                                        $phoneArgs = array())
  {
    // initial results declarations
    $entityPhones = array();
    $phoneIds = array();

    // if primary is not passed, get the default
    if (is_null($primary)) { $primary = $this->defaultIsPrimary; }

    // build our query object
    $q = $this->_getEntityPhoneQuery($entityIds, $strType);

    // if this is a primary query we add the restrictor
    if ($primary)
    {
      $q->addWhere(' EXISTS (
        SELECT s.id
          FROM agEntityPhoneContact s
          WHERE s.entity_id = epc.entity_id
            AND s.phone_contact_type_id = epc.phone_contact_type_id
          HAVING MIN(s.priority) = epc.priority )') ;
    }

    // build this as custom hydration to 'double tap' the data
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE);
    $index = 0;
    $priorEntityId = '';
    $priorContactType = '';
    $phoneHelperArgs = array();
    foreach ($rows as $row)
    {
      $entityPhones[$row[0]][$row[4]][] = array($row[1], $row[2], $row[3]);

      // here we build the mono-dimensional phoneId array, excluding dupes as we go; only useful
      // if we're actually going to use the phone helper
      if (! is_null($phoneHelperMethod) && ! in_array($row[1], $phoneIds))
      {
        $phoneHelperArgs[0][] = $row[1];
      }
    }

    // if no phone helper method was passed, assume that all we need are the phone id's and
    // stop right here!
    if (is_null($phoneHelperMethod))
    {
      return $entityPhones;
    }

    // otherwise... we keep going and lazily load our phone helper, 'cause we'll need her
    $phoneHelper = $this->getAgPhoneHelper();

    // finish appending the rest of our phone helper args
    foreach ($phoneArgs as $arg)
    {
      $phoneHelperArgs[] = $arg;
    }

    // use the phone helper to format the phone results
    $userFunc = array($phoneHelper,$phoneHelperMethod) ;
    $formattedPhones = call_user_func_array($userFunc,$phoneHelperArgs);

    // we can release the phone helper args, since we don't need them anymore
    unset($phoneHelperArgs);

    // now loop through our entities and replace phone value with formatted phone.
    foreach ($entityPhones as $entityId => $phoneTypes)
    {
      foreach ($phoneTypes as $phoneType => $phones)
      {
        // if we're only returning the primary, change the third dimension from an array to a value
        // NOTE: because of the restricted query, we can trust there is only one component per type
        // in our output and safely make this assumption
        if ($primary)
        {
          $entityPhones[$entityId][$phoneType][0][0] = $formattedPhones[$phones[0][0]];
        }
        // if not primary, we have one more loop in our return for another array nesting
        else
        {
          foreach ($phones as $index => $phone)
          { 
            $entityPhones[$entityId][$phoneType][$index][0] = $formattedPhones[$phone[0]];
          }
        }
      }
    }

    return $entityPhones;

  }

  /**
   * Method to return entity phones for a group of entity ids, sorted from highest priority to
   * lowest priority.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the phone contact type will
   * be returned as an ID value or its string equivalent.
   * @param boolean $primary Boolean that determines whether or not only the primary phone will
   * be returned (for that type).
   * @param string $phoneHelperMethod The phone helper method that will be called to format or
   * process the phones. This should be an agPhoneHelper::PHN_GET_* constant. If left NULL,
   * only phone ID's will be returned.
   * @param array $phoneArgs An array of arguments to pass forward to the phone helper.
   * @return array A three dimensional array, by entityId, then indexed from highest priority
   * phone to lowest, with a third dimension containing the phone type as index[0], and the
   * phone value as index[1].
   */
  public function getEntityPhone (array $entityIds = NULL,
                                  $strType = NULL,
                                  $primary = NULL,
                                  $phoneHelperMethod = NULL,
                                  array $phoneArgs = array())
  {
    // initial results declarations
    $entityPhones = array();
    $phoneIds = array();

    // if primary is not passed, get the default
    if (is_null($primary)) { $primary = $this->defaultIsPrimary; }

    // build our query object
    $q = $this->_getEntityPhoneQuery($entityIds, $strType);

    // if this is a primary query we add the restrictor
    if ($primary)
    {
      $q->addWhere(' EXISTS (
        SELECT s.id
          FROM agEntityPhoneContact s
          WHERE s.entity_id = epc.entity_id
          HAVING MIN(s.priority) = epc.priority )');
    }
    // build this as custom hydration to 'double tap' the data
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE);

    $phoneHelperArgs = array();
    foreach ($rows as $row)
    {
      $entityPhones[$row[0]][]= array($row[4], $row[1], $row[2], $row[3]) ;

      // here we build the mono-dimensional phoneId array, excluding dupes as we go; only useful
      // if we're actually going to use the phone helper
      if (! is_null($phoneHelperMethod) && ! in_array($row[1], $phoneIds))
      {
        $phoneHelperArgs[0][] = $row[1];
      }
    }


    // if no phone helper method was passed, assume that all we need are the phone id's and
    // stop right here!
    if (is_null($phoneHelperMethod))
    {
      return $entityPhones;
    }

    // otherwise... we keep going and lazily load our phone helper, 'cause we'll need her
    $phoneHelper = $this->getAgPhoneHelper();

    // finish appending the rest of our phone helper args
    foreach ($phoneArgs as $arg)
    {
      $phoneHelperArgs[] = $arg;
    }

    // use the phone helper to format the phone results
    $userFunc = array($phoneHelper,$phoneHelperMethod) ;
    $formattedPhones = call_user_func_array($userFunc,$phoneHelperArgs);

    // we can release the phone helper args, since we don't need them anymore
    unset($phoneHelperArgs) ;

    // now loop through our entities and replace phone value with formatted phone.
    foreach ($entityPhones as $entityId => $phones)
    {
      // if we're only returning the primary, change the second dimension from an array to a value
      // NOTE: because of the restricted query, we can trust there is only one component per type
      // in our output and safely make this assumption
      if ($primary)
      {
        $phones = $phones[0];
        $phones[1] = $formattedPhones[$phones[1]];
        $entityPhones[$entityId] = $phones;
      }
      // if not primary, we have one more loop in our return for another array nesting
      else
      {
        foreach ($phones as $index => $phone)
        {
          $entityPhones[$entityId][$index][1] = $formattedPhones[$phone[1]];
        }
      }
    }

    return $entityPhones;
  }

  /**
   * Method to set entity phones by passing phones, keyed by phone id.
   *
   * @param array $entityContacts An array of entity contact information. This is similar to the
   * output of getEntityPhone if no arguments are passed.
   * <code>
   * array(
   *   $entityId => array(
   *     array($phoneContactTypeId, array($phone, $phoneFormatId)),
   *     ...
   *   ), ...
   * )
   * </code>
   * @param boolean $keepHistory An optional boolean value to determine whether old entity contacts
   * (eg, those stored in the database but not explicitly passed as parameters), will be retained
   * and reprioritized to the end of the list, or removed altogether.
   * @param boolean $enforceStrict A boolean to determine whether or not to check for data validation.
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an phone 'optional'). Defaults to the class
   * property of the same name.
   * @param Doctrine_Connection $conn An optional Doctrine connection object.
   * @return array An associative array of operations performed including the number of upserted
   * records, removed records, an a positional array of failed inserts.
   */
  public function setEntityPhone( $entityContacts,
                                  $keepHistory = NULL,
                                  $enforceStrict = NULL,
                                  $throwOnError = NULL,
                                  Doctrine_Connection $conn = NULL)
  {
    // some explicit declarations at the top
    $uniqContacts = array();
    $invalidData = array();
    $err = NULL;
    $errMsg = 'This is a generic ERROR for setEntityPhone. You should never receive this ERROR.
      If you have received this ERROR, there is an error with your ERROR handling code.';

    // determine whether or not we'll explicitly throw exceptions on error
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError; }

    // Lazily load our phone helper, 'cause we'll need her
    $phoneHelper = $this->getAgPhoneHelper();
    // Grab all phone validations from phone helper.
    $defaultValidationsByCountry = $phoneHelper->getPhoneFormatComponents();
    $phoneValidations = array();
    foreach ($defaultValidationsByCountry as $id => $format)
    {
      $phoneValidations[] = $format[0];
    }

    // loop through our contacts and pull our unique phone from the fire
    foreach ($entityContacts as $entityId => &$contacts)
    {
      foreach($contacts as $index => $contact)
      {
        $contact[1][0] = trim($contact[1][0]);

        $isValidPhone = FALSE;
        // If enforce data validation check and if data is invalid, unset from $entityContacts array
        // from performing contact updates.  Otherwise, continue to add record to uniqContacts for
        // further processing.
        if ($contact[1][0] != '' && $enforceStrict)
        {
          foreach ($phoneValidations as $pvIdx => $matchPattern)
          {
            if (preg_match($matchPattern, $contact[1][0]))
            {
              $isValidPhone = TRUE;
              break;
            }
          }
          if (!$isValidPhone)
          {
            $invalidData[$entityId][] = $contact;

            // if we're being strict with error throws, let's throw on a problem
            if ($throwOnError)
            {
              $errMsg = sprintf('Phone \'%s\' failed the phone formatting test.', $contact[1][0]);

              // log our error
              sfContext::getInstance()->getLogger()->err($errMsg);

              // throw the exception we promised in our boolean
              throw new Exception($errMsg);
            }

            unset($contacts[$index]);
            continue;
          }
        }

        // strip away all non-numeric values from phone with the exception of the extension marking.
        $contact[1][0] = preg_replace('/[^0-9x]/', '', $contact[1][0]);

        // find the position of the element or return false
        $pos = array_search($contact[1], $uniqContacts, TRUE);

        // need to be really strict here because we don't want any [0] positions throwing us
        if ($pos === FALSE)
        {
          // add it to our unique contacts array
          $uniqContacts[] = $contact[1];

          // the the most recently inserted key
          $pos = max(array_keys($uniqContacts));
        }

        // either way we'll have to point the entities back to their phones
        $contacts[$index][1] = $pos;
      }
    }
    unset($contacts);

    // here we check our current transaction scope and create a transaction or savepoint
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__);
    }
    else
    {
      $conn->beginTransaction();
    }

    try
    {
      // process phone numbers, setting or returning, whichever is better with our s/getter
      $uniqContacts = $phoneHelper->setPhones($uniqContacts, $throwOnError, $conn);
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('Could not set phone numbers %s. Rolling back!', json_encode($uniqContacts));

      // hold onto this exception for later
      $err = $e;
    }

    if (is_null($err))
    {
      // now loop through the contacts again and give them their real values
      foreach ($entityContacts as $entityId => &$contacts)
      {
        foreach($contacts as $index => $contact)
        {
          // check to see if this index found in our 'unsettable' return from setPhones
          if (array_key_exists($contact[1], $uniqContacts[1]))
          {
            // purge this phone
            unset($contacts[$index]);
          }
          else
          {
            // otherwise, get our real phoneId
            $contacts[$index][1] = $uniqContacts[0][$contact[1]];
          }
        }
      }
      unset($contacts);

      // we're done with uniqContacts now
      unset($uniqContacts);


      try
      {
        // just submit the entity phones for setting
        $results = $this->setEntityContactById($entityContacts, $keepHistory, $throwOnError, $conn);
      }
      catch(Exception $e)
      {
        // log our error
        $errMsg = sprintf('Could not set entity phones %s. Rolling Back!',
          json_encode($entityContacts));

        // hold onto this exception for later
        $err = $e;
      }
    }

    // check to see if we had any errors along the way
    if (! is_null($err))
    {
      // log our error
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $err; }
    }

    // most excellent! no errors at all, so we commit... finally!
    if ($useSavepoint) { $conn->commit(__FUNCTION__); } else { $conn->commit(); }

    $results['invalidData'] = $invalidData;
    return $results;
  }
}