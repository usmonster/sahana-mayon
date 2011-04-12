<?php

/**
 * Sahana Agasti 1.99999 , Mayon func.inc.php
 * this file houses installation specific functions, that may eventually be
 * used for other non-symfony specific purposes.
 *
 * primarily called from install.inc.php
 */
function get_request($name, $def=NULL)
{
  if (isset($_REQUEST[$name]))
    return $_REQUEST[$name];
  else
    return $def;
}

function get_cookie($name, $default_value=null)
{
  if (isset($_COOKIE[$name]))
    return $_COOKIE[$name];
  return $default_value;
}

function ag_setcookie($name, $value, $time=null)
{
  setcookie($name, $value, isset($time) ? $time : (0));
  $_COOKIE[$name] = $value;
}

function ag_unsetcookie($name)
{
  ag_setcookie($name, null, -99999);
  unset($_COOKIE[$name]);
}

function ag_set_post_cookie($name, $value, $time=null)
{
  global $AG_PAGE_COOKIES;

  $AG_PAGE_COOKIES[] = array($name, $value, isset($time) ? $time : 0);
}

function ag_is_callable($var)
{
  foreach ($var as $e)
    if (!is_callable($e))
      return false;

  return true;
}

function ag_flush_post_cookies($unset=false)
{
  global $AG_PAGE_COOKIES;

  if (isset($AG_PAGE_COOKIES)) {
    foreach ($AG_PAGE_COOKIES as $cookie) {
      if ($unset)
        ag_unsetcookie($cookie[0]);
      else
        ag_setcookie($cookie[0], $cookie[1], $cookie[2]);
    }
    unset($AG_PAGE_COOKIES);
  }
}

function redirect($url)
{
  ag_flush_post_cookies();

  header('Location: ' . $url);
  exit();
}

function str2mem($val)
{
  $val = trim($val);
  $last = strtolower(substr($val, -1, 1));

  switch ($last) {
    // The 'G' modifier is available since PHP 5.1.0
    case 'g':
      $val *= 1024;
    case 'm':
      $val *= 1024;
    case 'k':
      $val *= 1024;
  }

  return $val;
}

function mem2str($size)
{
  $prefix = S_B;
  if ($size > 1048576) {
    $size = $size / 1048576;
    $prefix = S_M;
  } elseif ($size > 1024) {
    $size = $size / 1024;
    $prefix = S_K;
  }
  return round($size, 6) . $prefix;
}

function parseDsn($dsnString)
{
  $dsnArray = explode(':', $dsnString, 2);

  $dsnObject['dbtype'] = $dsnArray[0];

  $dsnArray = explode(';', $dsnArray[1]);

  foreach ($dsnArray as $key => $value) {
    $dsnValuePair = explode('=', $value);
    $dsnObject[strtolower($dsnValuePair[0])] = $dsnValuePair[1];
  }

  $validValues = array('dbtype', 'host', 'dbname');

  foreach ($dsnObject as $key => $value) {
    if (!in_array($key, $validValues)) {
      unset($dsnObject[$key]);
    }
    if ($key == 'host') {
      $hostArray = explode(':', $value);
      $dsnObject['host'] = $hostArray[0];
      if (isset($hostArray[1]) && intval($hostArray[1]) > 0) {
        $dsnObject['port'] = intval($hostArray[1]);
      }
    }
  }

  return $dsnObject;
}

/**
 * buildDsnString builds a DSN string out of provided database connection attributes.
 * @todo Throw an exception if validation of required parameters fails.
 *
 * @param string $dbType Type of database engine
 * @param string $dbHost Hostname of the database server
 * @param string $dbName Name of the database
 * @param string $dbPort Network port for the database server
 * @return string
 */
function buildDsnString($dbType, $dbHost, $dbName, $dbPort = null)
{
  $dbType = trim($dbType);
  $dbHost = trim($dbHost);
  $dbName = trim($dbName);

  if ($dbType != '' && $dbHost != '' && $dbName != '') {
    $dsnString = "$dbType:";
    $dsnString .= "host=$dbHost";

    if (isset($dbPort) && $dbPort != '') {
      $dbPort = intval($dbPort);
      if ($dbPort) {
        $dsnString .= ":$dbPort";
      }
    }
    $dsnString .= ';';

    $dsnString .= "dbname=$dbName";

    return $dsnString;
  }

  return null;
}

/**
 * readInstallConfig reads from the configuration files in the config/ directory and
 * returns all of the settings as a multidimensional array.
 *
 * @return array Current configuration as a multidimensional array
 */
function readInstallConfig()
{
  $configArray = array();

  if (file_exists(dirname(__FILE__) . '/../config/databases.yml') == TRUE) {
    $dbArray = sfYaml::load(dirname(__FILE__) . '/../config/databases.yml');
    $adsn = parseDsn($dbArray['all']['doctrine']['param']['dsn']);

    $configArray['dbUser'] = $dbArray['all']['doctrine']['param']['username'];
    $configArray['dbPass'] = $dbArray['all']['doctrine']['param']['password'];
    $configArray['dbHost'] = $adsn['host'];
    $configArray['dbName'] = $adsn['dbname'];

    if ($configArray['dbUser'] && $configArray['dbHost'] && $configArray['dbName']) {
      $configArray['dbConfigValid'] = true;
    }
  }

  if (file_exists(dirname(__FILE__) . '/../config/config.yml') == TRUE) {
    $cfgArray = sfYaml::load(dirname(__FILE__) . '/../config/config.yml');

    $configArray['admin_name'] = $cfgArray['admin']['admin_name'];
    $configArray['admin_email'] = $cfgArray['admin']['admin_email'];

    if ($configArray['saUser'] && $configArray['saHost'] && $configArray['authMethod']) {
      $configArray['authConfigValid'] = true;
    }
  }

  return $configArray;
}

/**
 * saveInstallConfig will write a configuration array out to config files in the
 * config/ directory. The array uses the same format as the array returned by readInstallConfig()
 *
 * @todo Write this function and replace current separate save code with it.
 *
 * @param array $configArray
 */
function saveInstallConfig($configArray)
{

}

/**
 * Function used by installer and admin configuration for writing the app.yml file
 * @param $authMethod changes the app.yml to set the authentication method, if NULL, then superadmin
 * @return boolean success or failure (was app.yml written)
 */
function writeAppYml($authMethod = NULL)
{
  $appYmlFile = sfConfig::get('sf_app_config_dir') . '/app.yml';
  $appConfig = sfYaml::load($appYmlFile);
  if ($authMethod === NULL) {
    $appConfig['all']['sf_guard_plugin'] =
        array('check_password_callable'
          => array('agSudoAuth', 'authenticate'));
    $appConfig['all']['sf_guard_plugin_signin_form'] = 'agSudoSigninForm';
  } else {
    $appConfig['all'] = '';
  }
  $appConfig['all']['.array']['menu_top'] =
      array(
        'homepage' => array('label' => 'Home', 'route' => '@homepage'),
        'prepare' => array('label' => 'Prepare', 'route' => '@prepare'),
        'respond' => array('label' => 'Respond', 'route' => '@respond'),
        'recover' => array('label' => 'Recover', 'route' => '@homepage'),
        'mitigate' => array('label' => 'Mitigate', 'route' => '@homepage'),
        'foo' => array('label' => 'Foo', 'route' => '@foo'),
        'modules' => array('label' => 'Modules', 'route' => '@homepage'),
        'admin' => array('label' => 'Administration', 'route' => '@admin'),
        'help' => array('label' => 'Help', 'route' => '@wiki'));

  $appConfig['all']['.array']['menu_children'] =
      array(
        'facility' => array
          ('label' => 'Facilities', 'route' => '@facility', 'parent' => 'prepare'),
        'org' => array
          ('label' => 'Organizations', 'route' => '@org', 'parent' => 'prepare'),
        'scenario' => array
          ('label' => 'Scenarios', 'route' => '@scenario', 'parent' => 'prepare'),
        'activate' => array
          ('label' => 'Activate a Scenario', 'route' => '@scenario', 'parent' => 'respond'),
        'event' => array
          ('label' => 'Manage Events', 'route' => 'event', 'parent' => 'respond'),
        'event_active' => array
          ('label' => 'Manage [Active Event]', 'route' => '@homepage', 'parent' => 'respond'));

  $appConfig['all']['.array']['menu_grandchildren'] =
      array(
        'facility_new' => array
          ('label' => 'Add New Facility', 'route' => 'facility/new', 'parent' => 'facility'),
        'facility_list' => array
          ('label' => 'List Facilities', 'route' => 'facility/list', 'parent' => 'facility'),
        'facility_import' => array
          ('label' => 'Import Facilities', 'route' => '@facility', 'parent' => 'facility'),
        'facility_export' => array
          ('label' => 'Export Facilities', 'route' => '@facility', 'parent' => 'facility'),
        'facility_types' => array
          ('label' => 'Manage Facility Types', 'route' => '@facility', 'parent' => 'facility'),
        'org_new' => array
          ('label' => 'Add New Organization', 'route' => 'organization/new', 'parent' => 'org'),
        'org_list' => array
          ('label' => 'List Organizations', 'route' => 'organization/list', 'parent' => 'org'),
        'scenario_create' => array
          ('label' => 'Build New Scenario', 'route' => 'scenario/new', 'parent' => 'scenario'),
        'scenario_list' => array
          ('label' => 'List Scenarios', 'route' => 'scenario/list', 'parent' => 'scenario'),
        'scenario_facilitygrouptypes' => array
          ('label' => 'Edit Facility Group Types', 'route' => 'scenario/grouptype', 'parent' => 'scenario'),
        'scenario_active' => array
          ('label' => '[Active Scenario]', 'route' => 'event', 'parent' => 'scenario'),
        'event_active_staff' => array
          ('label' => 'Staff', 'route' => '@homepage', 'parent' => 'event_active'),
        'event_active_facilities' => array
          ('label' => 'Facilities', 'route' => '@homepage', 'parent' => 'event_active'),
        'event_active_clients' => array
          ('label' => 'Clients', 'route' => '@homepage', 'parent' => 'event_active'),
        'event_active_reporting' => array
          ('label' => 'Reporting', 'route' => '@homepage', 'parent' => 'event_active'));

  $appConfig['all']['.array']['title'] =
      array(
        'homepage' => array
          ('url' => '/', 'title' => 'Sahana Agasti Home'),
        'staff_page' => array
          ('url' => '/staff/index', 'title' => 'Sahana Agasti Staff'),
        'staff_list' => array
          ('url' => '/staff/list', 'title' => 'Sahana Agasti Staff List'),
        'staff_new' => array
          ('url' => '/staff/new', 'title' => 'Sahana Agasti Staff New'),
        'staff_import' => array
          ('url' => '/staff/import', 'title' => 'Sahana Agasti Staff Import'),
        'faciltiy_page' => array
          ('url' => '/facility/index', 'title' => 'Sahana Agasti Facilities'),
        'faciltiy_list' => array
          ('url' => '/facility/list', 'title' => 'Sahana Agasti Facility List'),
        'faciltiy_new' => array
          ('url' => '/facility/new', 'title' => 'Sahana Agasti Facility New'),
        'faciltiy_import' => array
          ('url' => '/facility/import', 'title' => 'Sahana Agasti Facility Import'),
        'organization_page' => array
          ('url' => '/organization/index', 'title' => 'Sahana Agasti Organization'),
        'organization_list' => array
          ('url' => '/organization/list', 'title' => 'Sahana Agasti Organization List'),
        'organization_new' => array
          ('url' => '/organization/new', 'title' => 'Sahana Agasti Organization New'),
        'scenario_page' => array
          ('url' => '/scenario/index', 'title' => 'Sahana Agasti Scenario'),
        'scenario_list' => array
          ('url' => '/scenario/list', 'title' => 'Sahana Agasti Scenario List'),
        'scenario_pre' => array
          ('url' => '/scenario/pre', 'title' => 'Sahana Agasti Scenario Pre-Creator'),
        'scenario_new' => array
          ('url' => '/scenario/new', 'title' => 'Sahana Agasti Scenario Creator'),
        'events_page' => array
          ('url' => '/event/index', 'title' => 'Sahana Agasti Events')
  );


// updates app.yml
  try {
    file_put_contents($appYmlFile, sfYaml::dump($appConfig, 4));
    return true;
  } catch (Exception $e) {
    //echo 'There was an error writing the app.yml file: ', $e->getMessage();
    return false;
  }
}

function sfClearCache($app, $env)
{
  $cacheDir = sfConfig::get('sf_cache_dir') . '/' . $app . '/' . $env . '/';
  /**
   * in order to clear the cache, we have to actually specify what to actually
   * clear
   */
  $cache = new sfFileCache(array('cache_dir' => $cacheDir));
  $cache->clean();
}

?>