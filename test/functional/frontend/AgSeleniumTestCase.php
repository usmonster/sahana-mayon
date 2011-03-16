<?php

require_once 'SilverSelenide.php';
//require_once dirname(__FILE__).'/../../bootstrap/selenium.php';

/**
 * AgSeleniumTestCase is the base test class for all automated Selenium
 * tests in the Sahana Agasti Mayon testing framework.
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Usman Akeju, CUNY SPS
 *
 * @todo do{Action} verifies/asserts at the end, try{Action} does not
 * @todo all action methods should return $this to allow for chaining
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
abstract class AgSeleniumTestCase extends SilverSelenide
{

  /**
   * @var string
   */
  protected static $_appRoot = 'http://qa.example.com/agasti/';
  /**
   * @var string
   */
  protected static $_appUsername = 'default_username';
  /**
   * @var string
   */
  protected static $_appPassword = 'default_password';
  /**
   * @var integer
   */
  protected static $_sePort = 4444;
  /**
   * @var integer
   */
  protected static $_testTimeout = 30;        // test completion time limit (s)
  /**
   * @var integer
   */
  protected static $_pageLoadTimeout = 700;  // page load time limit (ms)
  /**
   * @var array
   * @see PHPUnit_Extensions_SeleniumTestCase::$browsers
   */
  public static $browsers = array(
    // FIREFOX for LOCAL TESTING
    array(
      'name' => 'Firefox [current] on LOCALHOST',
      'browser' => '*firefox',
      'host' => 'localhost'
    )/* ,

    // CURRENT browsers on Windows 7 (64-bit)
    array(
    'name' => 'Google Chrome Stable/7.0 on Windows 7 64-bit',
    'browser' => '*googlechrome',
    'host' => 'selenium-server1.example.com'
    ),
    /* <-- Change the type of comment (//* or /*) to toggle one/all browsers
    array(
    'name' => 'Firefox Current/3.6 on Windows 7 64-bit',
    'browser' => '*firefox',
    'host' => 'selenium-server1.example.com'
    ),
    array(
    'name' => 'IE Current/8.0 on Windows 7 64-bit',
    'browser' => '*iexploreproxy',
    'host' => 'selenium-server1.example.com'
    ),

    // CURRENT browsers on Windows XP Pro SP3 CURRENT
    array(
    'name' => 'IE Current/8.0 on Windows XP Pro SP3',
    'browser' => '*iexplore',
    'host' => 'selenium-server2.example.com'
    ),
    array(
    'name' => 'Firefox Current/3.6 on Windows XP Pro SP3',
    'browser' => '*firefox',
    'host' => 'selenium-server2.example.com'
    ),
    array(
    'name' => 'Google Chrome Stable/7.0 on Windows XP Pro SP3',
    'browser' => '*googlechrome',
    'host' => 'selenium-server2.example.com'
    ),

    // OLDER supported browsers on Windows XP Pro SP3
    array(
    'name' => 'IE 7.0 on Windows XP Pro SP3',
    'browser' => '*iexplore',
    'host' => 'selenium-server3.example.com'
    ),
    array(
    'name' => 'Firefox 3.0 on Windows XP Pro SP3',
    'browser' => '*firefox',
    'host' => 'selenium-server3.example.com'
    ),

    // OLDEST supported browsers on Windows XP Pro SP2
    array(
    'name' => 'IE 6.0 on Windows XP Pro SP2',
    'browser' => '*iexplore',
    'host' => 'selenium-server4.example.com'
    ),
    array(
    'name' => 'Firefox 2.0 on Windows XP Pro SP2',
    'browser' => '*firefox',
    'host' => 'selenium-server4.example.com'
    )
    // DO NOT REMOVE THIS COMMENT */
  );

  /**
   * Sets defaults for test, such as timeouts and browser target URL;
   * can be overridden for local or site-specific tests.
   */
  protected function setUp()
  {
    $this
        ->setTimeout(self::$_testTimeout)
        ->setHttpTimeout(self::$_pageLoadTimeout)
        ->setBrowserUrl(self::$_appRoot);
  }

  /**
   * Uses the navigation bar to click through to a page.
   *
   * @param string $navLinkName The text of the navigation link
   * @todo add navigation id to locator
   * @todo add asserts/verifies after click
   */
  protected function navigateTo($navLinkName)
  {
    return $this->clickAndWait('link=' . $navLinkName);
  }

  protected function navigateToHome()
  {
    return $this->navigateTo('Home');
  }

  protected function navigateToStaff()
  {
    return $this->navigateTo('Staff');
  }

  protected function navigateToGis()
  {
    return $this->navigateTo('GIS');
  }

  protected function navigateToFacility()
  {
    return $this->navigateTo('Facilities');
  }

  protected function navigateToScenario()
  {
    return $this->navigateTo('Scenarios');
  }

  protected function navigateToOrganization()
  {
    return $this->navigateTo('Organizations');
  }

  protected function navigateToEvent()
  {
    return $this->navigateTo('Manage Events');
  }

  protected function navigateToAdmin()
  {
    return $this->navigateTo('Administration');
  }

  protected function navigateToAbout()
  {
    return $this->navigateTo('About');
  }

  /**
   * Attempts to log into the application with the supplied credentials.
   *
   * @param string $username not null, falls back to default app username
   * @param string $password not null, falls back to default app password
   * @return mixed a reference to $this test.
   */
  protected function tryLogin($username = null, $password = null)
  {
    // if(empty($username)) {
    if (is_null($username)) {
      $username = self::$_appUsername;
    }
    // if(empty($password)) {
    if (is_null($password)) {
      $password = self::$_appPassword;
    }

    return $this
        ->type('signin_username', $username)
        ->type('signin_password', $password)
        ->clickAndWait("//input[@value='sign in']");
  }

  /**
   * Attempts to log into the application with the supplied credentials,
   * and asserts success.
   *
   * @param string $username optional
   * @param string $password optional
   * @return mixed a reference to $this test.
   */
  protected function doLogin($username = null, $password = null)
  {
    // TODO: extend assertElementPresent to accept/display an error message
    //$this->assertElementPresent("//input[@value='sign inBLORGLEFOOP']",
    // 'This message won't even show up!');
    return $this
        ->assertElementValueEquals("//input[@value='sign in']", 'sign in')
//    ->assertElementValueEquals("//input[@value='sign in']", 'sign in',
//     'Login form not found!')
        ->tryLogin($username, $password)
        ->assertTextPresent('Logged in as:', 'Is this where the error message goes?');
  }

  /**
   * Attempts to log into the application with the supplied credentials,
   * and asserts success.
   *
   * @param string $username optional
   * @param string $password optional
   * @return mixed a reference to $this test.
   */
  protected function doLogout()
  {
    // TODO: extend assertElementPresent to accept/display an error message
    //$this->assertElementPresent('logout', 'Logout button not found -- not logged in?');
    return $this
        ->assertElementValueEquals('logout', 'log out')
        ->clickAndWait('logout');
  }

}