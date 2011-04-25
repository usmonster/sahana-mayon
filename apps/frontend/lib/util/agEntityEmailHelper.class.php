<?php
/**
 * Provides entity email helper functions and inherits several methods and properties from the
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
class agEntityEmailHelper extends agEntityContactHelper
{
  public    $agEmailHelpder,
            $defaultIsPrimary = FALSE,
            $defaultIsStrType = FALSE;

  protected $_batchSizeModifier = 2,
            $_contactTableMetadata = array( 'table' => 'agEntityEmailContact',
                                            'method' => 'getEntityEmail',
                                            'type' => 'email_contact_type_id',
                                            'value' => 'email_contact_id');

  /**
   * Method to lazily load the $agEmailHelper class property (an instance of agEmailHelper)
   * @return object The instantiated agEmailHelper object
   */
  public function getAgEmailHelper()
  {
    if (! isset($this->agEmailHelper)) { $this->agEmailHelper = agEmailHelper::init(); }
    return $this->agEmailHelper;
  }

  /**
   * Method to return an agDocrineQuery object, preconfigured to collect entity emails.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the email contact type will
   * be returned as an ID value or its string equivalent.
   * @return Doctrine_Query An agDoctrineQuery object.
   */
  private function _getEntityEmailQuery($entityIds = NULL, $strType = NULL)
  {
    // if no (null) ID's are passed, get the entityIds from the class property
    $entityIds = $this->getRecordIds($entityIds);

    // if strType is not passed, get the default
    if (is_null($strType)) { $strType = $this->defaultIsStrType; }

    // the most basic version of this query
    $q = agDoctrineQuery::create()
       ->select('eec.entity_id')
         ->addSelect('eec.email_contact_id')
         ->addSelect('eec.created_at')
         ->addSelect('eec.updated_at')
       ->from('agEntityEmailContact eec')
            ->innerJoin('eec.agEmailContact ec')
       ->whereIn('eec.entity_id', $entityIds)
       ->orderBy('eec.priority');

    // here we determine whether to return the email_contact_type_id or its string value
    if ($strType)
    {
      $q->addSelect('ect.email_contact_type')
        ->innerJoin('eec.agEmailContactType ect');
    } else {
      $q->addSelect('eec.email_contact_type_id');
    }

    return $q;
  }

    /**
   * Method to return entity emails for a group of entity ids, sorted from highest priority to
   * lowest priority, and grouped by the email contact type.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the email contact type will
   * be returned as an ID value or its string equivalent.
   * @param boolean $primary Boolean that determines whether or not only the primary email will
   * be returned (for that type).
   * @param string $emailHelperMethod The email helper method that will be called to format or
   * process the emails. This should be an agEmailHelper::EML_GET_* constant. If left NULL,
   * only email ID's will be returned.
   * @param array $emailArgs An array of arguments to pass forward to the email helper.
   * @return array A two or three dimensional array (depending on the setting of the $primary
   * parameter), by entityId, by emailContactType.
   */
  public function getEntityEmailByType ($entityIds = NULL,
                                        $strType = NULL,
                                        $primary = NULL,
                                        $emailHelperMethod = NULL,
                                        $emailArgs = array())
  {
    // initial results declarations
    $entityEmails = array();
    $emailIds = array();
    $emailHelperArgs = array(array());

    // if primary is not passed, get the default
    if (is_null($primary)) { $primary = $this->defaultIsPrimary; }

    // build our query object
    $q = $this->_getEntityEmailQuery($entityIds, $strType);

    // if this is a primary query we add the restrictor
    if ($primary)
    {
      $q->addWhere(' EXISTS (
        SELECT s.id
          FROM agEntityEmailContact s
          WHERE s.entity_id = eec.entity_id
            AND s.email_contact_type_id = eec.email_contact_type_id
          HAVING MIN(s.priority) = eec.priority )');
    }

    // build this as custom hydration to 'double tap' the data
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE);
    foreach ($rows as $row)
    {
        $entityEmails[$row[0]][$row[4]][] = array($row[1], $row[2], $row[3]);

      // here we build the mono-dimensional emailId array, excluding dupes as we go; only useful
      // if we're actually going to use the email helper
      if (! is_null($emailHelperMethod) && ! in_array($row[1], $emailHelperArgs[0]))
      {
        $emailHelperArgs[0][] = $row[1];
      }
    }

    // if no email helper method was passed, assume that all we need are the email id's and
    // stop right here!
    if (is_null($emailHelperMethod))
    {
      return $entityEmails;
    }

    // otherwise... we keep going and lazily load our email helper, 'cause we'll need her
    $emailHelper = $this->getAgEmailHelper();

    // finish appending the rest of our email helper args
    foreach ($emailArgs as $arg)
    {
      $emailHelperArgs[] = $arg;
    }

    // use the email helper to format the email results
    $userFunc = array($emailHelper,$emailHelperMethod);
    $formattedEmails = call_user_func_array($userFunc,$emailHelperArgs);

    // we can release the email helper args, since we don't need them anymore
    unset($emailHelperArgs);

    // now loop through our entities and attach their emails
    foreach ($entityEmails as $entityId => $emailTypes)
    {
      foreach ($emailTypes as $emailType => $emails)
      {
        // if we're only returning the primary, change the third dimension from an array to a value
        // NOTE: because of the restricted query, we can trust there is only one component per type
        // in our output and safely make this assumption
        if ($primary)
        {
          // flatten the results
          $emails = $emails[0];
          $emails[0] = $formattedEmails[$emails[0]];

          $entityEmails[$entityId][$emailType][0] = $emails;
        }
        // if not primary, we have one more loop in our return for another array nesting
        else
        {
          foreach ($emails as $index => $email)
          {
            $entityEmails[$entityId][$emailType][$index][0] = $formattedEmails[$email[0]];
          }
        }
      }
    }

    return $entityEmails;
  }

  /**
   * Method to return entity emails for a group of entity ids, sorted from highest priority to
   * lowest priority.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the email contact type will
   * be returned as an ID value or its string equivalent.
   * @param boolean $primary Boolean that determines whether or not only the primary email will
   * be returned (for that type).
   * @param string $emailHelperMethod The email helper method that will be called to format or
   * process the emails. This should be an agEmailHelper::EML_GET_* constant. If left NULL,
   * only email ID's will be returned.
   * @param array $emailArgs An array of arguments to pass forward to the email helper.
   * @return array A three dimensional array, by entityId, then indexed from highest priority
   * email to lowest, with a third dimension containing the email type as index[0], and the
   * email value as index[1].
   */
  public function getEntityEmail ($entityIds = NULL,
                                  $strType = NULL,
                                  $primary = NULL,
                                  $emailHelperMethod = NULL,
                                  $emailArgs = array())
  {
    // initial results declarations
    $entityEmails = array();
    $emailIds = array();
    $emailHelperArgs = array(array());

    // if primary is not passed, get the default
    if (is_null($primary)) { $primary = $this->defaultIsPrimary; }

    // build our query object
    $q = $this->_getEntityEmailQuery($entityIds, $strType);

    // if this is a primary query we add the restrictor
    if ($primary)
    {
      $q->addWhere(' EXISTS (
        SELECT s.id
          FROM agEntityEmailContact s
          WHERE s.entity_id = eec.entity_id
          HAVING MIN(s.priority) = eec.priority )');
    }
    // build this as custom hydration to 'double tap' the data
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE);

    foreach ($rows as $row)
    {
      $entityEmails[$row[0]][] = array($row[4], $row[1], $row[2], $row[3]);

      // here we build the mono-dimensional emailId array, excluding dupes as we go; only useful
      // if we're actually going to use the phone helper
      if (! is_null($emailHelperMethod) && ! in_array($row[1], $emailIds))
      {
        $emailHelperArgs[0][] = $row[1];
      }
    }

    // if no email helper method was passed, assume that all we need are the email id's and
    // stop right here!
    if (is_null($emailHelperMethod))
    {
      return $entityEmails;
    }

    // otherwise... we keep going and lazily load our email helper, 'cause we'll need her
    $emailHelper = $this->getAgEmailHelper();

    // finish appending the rest of our phone helper args
    foreach ($emailArgs as $arg)
    {
      $emailHelperArgs[] = $arg;
    }

    // use the email helper to format the email results
    $userFunc = array($emailHelper,$emailHelperMethod);
    $formattedEmails = call_user_func_array($userFunc,$emailHelperArgs);

    // we can release the email helper args, since we don't need them anymore
    unset($emailHelperArgs);

    // now loop through our entities and replace email value with formatted email.
    foreach ($entityEmails as $entityId => $emails)
    {
      // if we're only returning the primary, change the second dimension from an array to a value
      // NOTE: because of the restricted query, we can trust there is only one component per type
      // in our output and safely make this assumption
      if ($primary)
      {
        // flatten for just one return
        $emails = $emails[0];
        $emails[1] = $formattedEmails[$emails[1]];

        $entityEmails[$entityId] = $emails;
      }
      // if not primary, we have one more loop in our return for another array nesting
      else
      {
        foreach ($emails as $index => $email)
        {
          $entityEmails[$entityId][$index][1] = $formattedEmails[$email[1]];
        }
      }
    }

    return $entityEmails;
  }

  /**
   * Method to set entity emails by passing emails, keyed by email id.
   *
   * @param array $entityContacts An array of entity contact information. This is similar to the
   * output of getEntityEmail if no arguments are passed.
   * <code>
   * array(
   *   $entityId => array(
   *     array($emailContactTypeId, $email),
   *     ...
   *   ), ...
   * )
   * </code>
   * @param boolean $keepHistory An optional boolean value to determine whether old entity contacts
   * (eg, those stored in the database but not explicitly passed as parameters), will be retained
   * and reprioritized to the end of the list, or removed altogether.
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an email 'optional'). Defaults to the class
   * property of the same name.
   * @param Doctrine_Connection $conn An optional Doctrine connection object.
   * @return array An associative array of operations performed including the number of upserted
   * records, removed records, an a positional array of failed inserts.
   */
  public function setEntityEmail( $entityContacts,
                                  $keepHistory = NULL,
                                  $throwOnError = NULL,
                                  $enforceStrict = NULL,
                                  Doctrine_Connection $conn = NULL)
  {
    // some explicit declarations at the top
    $uniqContacts = array();
    $invalidData = array();
    $err = NULL;
    $errMsg = 'This is a generic ERROR for setEntityEmail. You should never receive this ERROR.
      If you have received this ERROR, there is an error with your ERROR handling code.';

    // determine whether or not we'll explicitly throw exceptions on error
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError; }
    if (is_null($enforceStrict)) { $enforceStrict = $this->enforceStrict; }

    // loop through our contacts and pull our unique email from the fire
    foreach ($entityContacts as $entityId => $contacts)
    {
      foreach($contacts as $index => $contact)
      {
        $contact[1] = trim($contact[1]);

        // If enforce data validation check and if data is invalid, unset from $entityContacts array
        // from performing contact updates.  Otherwise, continue to add record to uniqContacts for
        // further processing.
        if ($contact[1] != '' && $enforceStrict)
        {
          if (!preg_match('/.+\@.+\..+/', $contact[1]))
          {
            if (array_key_exists($entityId, $invalidData))
            {
              $invalidData[$entityId][] = $contact;
            }
            else
            {
              $invalidData[] = $entityContacts[$entityId];
            }
            unset($entityContacts[$entityId][$index]);
            continue;
          }
        }

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

        // either way we'll have to point the entities back to their emails
        $entityContacts[$entityId][$index][1] = $pos;
      }
    }

    // whelp, if we haven't loaded it already, let's get our email helper
    $emailHelper = $this->getAgEmailHelper();

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
      // process emails, setting or returning, whichever is better with our s/getter
      $uniqContacts = $emailHelper->setEmails($uniqContacts, $throwOnError, $conn);
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('Could not set emails %s. Rolling back!', json_encode($uniqContacts));

      // hold onto this exception for later
      $err = $e;
    }

    if (is_null($err))
    {
      // now loop through the contacts again and give them their real values
      foreach ($entityContacts as $entityId => $contacts)
      {
        foreach($contacts as $index => $contact)
        {
          // check to see if this index found in our 'unsettable' return from setEmails
          if (array_key_exists($contact[1], $uniqContacts[1]))
          {
            // purge this email
            unset($entityContacts[$entityId][$index]);
          }
          else
          {
            // otherwise, get our real emailId
            $entityContacts[$entityId][$index][1] = $uniqContacts[0][$contact[1]];
          }
        }
      }

      // we're done with uniqContacts now
      unset($uniqContacts);


      try
      {
        // just submit the entity emails for setting
        $results = $this->setEntityContactById($entityContacts, $keepHistory, $throwOnError, $conn);
      }
      catch(Exception $e)
      {
        // log our error
        $errMsg = sprintf('Could not set entity emails %s. Rolling Back!',
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