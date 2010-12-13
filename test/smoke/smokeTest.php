<?php

require_once dirname(__FILE__) . '/../functional/frontend/AgSeleniumTestCase.php';

/**
 * SmokeTest is a collection of the most basic functional tests, which
 * are executed only in a subset of all browsers as a QUICK sanity check.
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Usman Akeju, CUNY SPS
 *
 * @todo make this a test suite that points to other tests cases.
 *
 *  Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class SmokeTest extends AgSeleniumTestCase
{

  /**
   * Attempts a login with correct credentials, and logout.
   *
   * @todo verify expected text at each page/action
   */
  public function testLoginLogoutSucceeds()
  {
    $this
        // opens the main URI
        ->open()//;$this
        // clicks around to each menu item
        //TODO: verify expected text is there
        ->navigateToHome()
        ->navigateToFacilites()
        ->navigateToStaff()
        ->navigateToClients()
        ->navigateToScenario()
        ->navigateToAdmin()
        // tries to login with correct credentials
        ->doLogin()
        // clicks around to each menu item
        //TODO: verify expected text is there
        ->navigateToHome()
        ->navigateToFacilites()
        ->navigateToStaff()
        ->navigateToClients()
        ->navigateToScenario()
        ->navigateToAdmin()
        ->navigateToHome()
        // and logs out
        ->doLogout();
  }

  /**
   * Attempts a login attempt with bad credentials,
   * verifies that it fails.
   */
  public function testLoginWithBadCredentialsFails()
  {
    $this
        // opens the main URI
        ->open()//;$this
        // tries to login with bad password
        ->tryLogin(self::$_appUsername, self::$_appPassword . '...NOT!')
        // verifies login failed
        ->verifyTextNotPresent('Logged in as:');
  }

}