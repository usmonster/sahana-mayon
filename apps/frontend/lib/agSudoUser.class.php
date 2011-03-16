<?php

/**
 * Agasti Sudo User Class extends the basic functionality of sfGuardSecurity
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

/**
 * @todo add description of class in header
 */
class agSudoUser
{

  /**
   * @todo: log all activity of SUDO user to database
   * @param $username from the username form/entry
   * @param $password from the password form/entry
   * @return $user an autenticated/or unauthorized user object
   */
  public static function checkSudoPassword($username, $password)
  {
    //require_once dirname(__FILE__) . '/../../../lib/vendor/symfony/lib/yaml/sfYaml.php';
    // check config.yml existence
    if (file_exists(dirname(__FILE__) . '/../../../config/config.yml') == FALSE) {
      header("Location: install.php");
      return false;
    } else {
      $cfgArray = sfYaml::load(dirname(__FILE__) . '/../../../config/config.yml');
    }
    if ($cfgArray['sudo']['super_user'] == $username && $cfgArray['sudo']['super_pass'] == $password) {
      $user = self::getUser($username);
      $user->setPassword($password);
      $user->setEmailAddress($cfgArray['admin']['admin_email']);
      $user->setIsActive(true);
      // set other user aspects

      $user->save();
      return $user;
    } else {
      throw new sfException(sprintf('The username/password combination did not match'));
      return false;
    }
  }

  protected static function getUser($username)
  {
    if ($user = Doctrine::getTable('sfGuardUser')->retrieveByUsername($username)) {
      //user exists, return it
      return $user;
    } else {
      //none found, create it
      $user = new sfGuardUser();
      $user->setUsername($username);
      return $user;
      //if no DB connection, this will fail
    }
  }

}

