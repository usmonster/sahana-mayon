<?php
/**
 *
 * Provides bulk-email manipulation methods
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
class agEmailHelper extends agBulkRecordHelper
{
  // these constants map to the email get types that are supported in other function calls
  const     EML_GET_VALUE = 'getEmailValues';

  public function getEmailValues($emailIds)
  {
    $q = agDoctrineQuery::create()
      ->select('e.id')
            ->addSelect('e.email_contact')
          ->from('agEmailContact e')
          ->whereIn('e.id', $emailIds);
    return $q->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * A quick helper method to take in an array emails and return an array of email ids.
   * @param array $emails A monodimensional array of emails.
   * @return array An associative array, keyed by email, with a value of email_id.
   */
  public function getEmailIds($emails)
  {
    $q = agDoctrineQuery::create()
      ->select('e.email_contact')
          ->addSelect('e.id')
        ->from('agEmailContact e')
        ->whereIn('e.email_contact',$emails);

    return $q->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  protected function setNewEmails($emails, $throwOnError, $conn)
  {
    // declare our results array
    $results = array();

    // pick up the default connection and error throw prerogative if one is not passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError; }

    foreach ($emails as $index => $email)
    {
      // we do this so we only have to call rollback / unset once, plus it's nice to have a bool to
      // check on our own
      $err = NULL;
      $errMsg = 'This is a generic error message for setNewEmails. You should never receive this
        error. If you are recieving this error, there is an ERROR in your error-handling code.';

      // here we check our current transaction scope and create a transaction or savepoint
      $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
      if ($useSavepoint)
      {
        $conn->beginTransaction(__FUNCTION__);
      }
      else
      {
        $conn->beginTransaction();
      }

      $newEmail = new agEmailContact();
      $newEmail['email_contact'] = $email;

      try
      {
        $newEmail->save($conn);
        $emailId = $newEmail->getId();
        if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit(); }
      }
      catch (Exception $e)
      {
        // log our error
        $errMsg = sprintf('Couldn\'t insert email contact %s!  Rolled back changes!', $email);

        // log our error
        sfContext::getInstance()->getLogger()->err($errMsg);

        // rollback
        if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

        // ALWAYS throw an error, it's like stepping on a crack if you don't
        if ($throwOnError) { throw $e; }
        continue;
      }

      // commit our results to our final results array
      $results[$index] = $emailId;

      // release the value on our input array
      unset($emails[$index]);
    }
    return array($results, array_keys($emails));
  }

  protected function _setEmails( $emails, $throwOnError = NULL, Doctrine_Connection $conn = NULL)
  {
    // declare our results array
    $results = array();

    // return any found emails
    $dbEmails = $this->getEmailIds(array_unique(array_values($emails)));

    // loop through the pass-in emails and build a couple of arrays
    foreach ($emails as $emailIndex => $email)
    {
      // Check if email already exists in db.
      if (array_key_exists($email, $dbEmails))
      {
        // for each of the emails with that ID, build our results set and
        // unset the value from the stuff left to be processed (we're going to use that later!)
        $results[$emailIndex] = $dbEmails[$email];
        unset($emails[$emailIndex]);
      }
    }

    // just 'cause this is going to be a very memory-hungry method, we'll unset the dbEmails too
    unset($dbEmails);

    // pick up the default connection if one is not passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    // now that we have all of the 'existing' emails, let's build the new ones
    $newEmails = $this->setNewEmails($emails, $throwOnError, $conn);
    $successes = array_shift($newEmails);

    // we don't need this anymore!
    unset($newEmails);

    foreach ($successes as $index => $emailId)
    {
      // add our successes to the final results set
      $results[$index] = $emailId;

      // release the address from our initial input array
      unset($emails[$index]);

      // release it from the successes array while we're at it
      unset($successes[$index]);
    }

    // and finally we return our results, both the successes and the failures
    return array($results, array_keys($emails));
  }

  /**
   *
   * @param <type> $emails
   * @param <type> $throwOnError
   * @param <type> $conn
   * @todo Fill in method.  Currently, empty shell.
   */
  public function setEmails($emails, $throwOnError = NULL, $conn = NULL)
  {
    // either way, we eventually pass the 'cleared' emails to our setter
    $results = $this->_setEmails($emails, $throwOnError, $conn);

    return $results;
  }

}
