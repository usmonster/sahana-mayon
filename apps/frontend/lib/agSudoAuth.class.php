<?php

/**
 * Extends sfGuardSecurityUser class 
 *
 * Agasti Sudo User Class extends the basic sfGuardSecurityUser class to allow
 * for a first-step injection (i.e. not to check against the database tables
 * first) of a username/password combination specified in a file by the admin.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agSudoAuth extends sfGuardSecurityUser
{

  /**
   *
   * @param $username username coming from the login form
   * @param $password password coming from the login form
   * @return a call to cleanUser that checks for the username/password
   */
  public static function authenticate($username, $password)
  {
    if (agSudoUser::checkSudoPassword($username, $password)) {
      return self::cleanUser($username, $password);
    }
  }

  /** create and or update
   * gets a user/password from  autenticate, adds a password if the user exists
   * @todo update tags below
   * @param <type> $username
   * @param <type> $password
   * @return <type>
   */
  protected static function cleanUser($username, $password)
  {
    $user = self::getUser($username);
    $user->setPassword($password);
    $user->setEmailAddress('super@admin.com');
    $user->setIsActive(true);
    //set other user aspects
    $user->save();
    return $user;
  }

  protected static function getUser($username)
  {
    if ($user = Doctrine::getTable('sfGuardUser')->
            retrieveByUsername($username)) {
      /** when user exists,
       * @return it
       */
      return $user;
    } else {
      //none found, create the user
      $user = new sfGuardUser();
      $user->setUsername($username);
      return $user;
      /** if no DB connection, this will fail
       */
    }
  }

}
