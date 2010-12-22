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
  if(isset($_REQUEST[$name]))
    return $_REQUEST[$name];
  else
    return $def;
}

function get_cookie($name, $default_value=null){
	if(isset($_COOKIE[$name]))	return $_COOKIE[$name];
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

function ag_set_post_cookie($name, $value, $time=null){
	global $AG_PAGE_COOKIES;

	$AG_PAGE_COOKIES[] = array($name, $value, isset($time)?$time:0);
}

function ag_is_callable($var)
{
  foreach($var as $e)
    if(!is_callable($e)) return false;

  return true;
}
function ag_flush_post_cookies($unset=false){
	global $AG_PAGE_COOKIES;

	if(isset($AG_PAGE_COOKIES)){
		foreach($AG_PAGE_COOKIES as $cookie){
			if($unset)
				ag_unsetcookie($cookie[0]);
			else
				ag_setcookie($cookie[0], $cookie[1], $cookie[2]);
		}
		unset($AG_PAGE_COOKIES);
	}
}
function redirect($url){
	ag_flush_post_cookies();

	header('Location: '.$url);
	exit();
}
function str2mem($val){
        $val = trim($val);
        $last = strtolower(substr($val, -1, 1));

        switch($last){
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

function mem2str($size){
        $prefix = S_B;
        if($size > 1048576) {   $size = $size/1048576;  $prefix = S_M; }
        elseif($size > 1024) {  $size = $size/1024;     $prefix = S_K; }
        return round($size, 6).$prefix;
}

function agSaveSetup($config)
{
  /** remember to set the superadmin config.yml up!!!  */
  sfClearCache('frontend', 'all');
  sfClearCache('frontend', 'dev');
  sfClearCache('frontend', 'prod');
  require_once sfConfig::get('sf_root_dir') . '/lib/vendor/symfony/lib/yaml/sfYaml.php';
  $file = sfConfig::get('sf_config_dir') . '/config.yml';
  // update config.yml
  try {
    file_put_contents($file, sfYaml::dump($config, 4));
  } catch (Exception $e) {
    echo "hey, something went wrong:" . $e->getMessage();
  }

  $file = sfConfig::get('sf_app_dir') . '/frontend/config/app.yml';
  touch($file);
  //if app.yml doesn't exist
  $appConfig = sfYaml::load($file);
  $appConfig['all']['sf_guard_plugin'] =
      array('check_password_callable'
        => array('agSudoAuth', 'authenticate'));
  $appConfig['all']['sf_guard_plugin_signin_form'] = 'agSudoSigninForm';

  $appConfig['all']['.array']['navpages'] =
      array(
        'homepage' => array('name' => 'Home', 'route' => '@homepage'),
        'facility' => array('name' => 'Facility', 'route' => '@facility'),
        'staff' => array('name' => 'Staff', 'route' => '@staff'),
        'client' => array('name' => 'Client', 'route' => '@client'),
        'scenario' => array('name' => 'Scenario', 'route' => '@scenario'),
        'gis' => array('name' => 'GIS', 'route' => '@gis'),
        'org' => array('name' => 'Organization', 'route' => '@org'),
        'admin' => array('name' => 'Admin', 'route' => '@admin'),
        'about' => array('name' => 'About', 'route' => '@about'));
  // update config.yml
  try {
    file_put_contents($file, sfYaml::dump($appConfig, 4));
  } catch (Exception $e) {
    echo "hey, something went wrong:" . $e->getMessage();
    return false;
  }
  $file = sfConfig::get('sf_config_dir') . '/databases.yml';
  $dbConfiguration = sfYaml::load($file);
  $dbConfiguration['all']['doctrine']['param']['attributes'] = array(
    'default_table_type' => 'INNODB',
    'default_table_charset' => 'utf8',
    'default_table_collate' => 'utf8_general_ci'
  );
  try {
    file_put_contents($file, sfYaml::dump($dbConfiguration, 4));
  } catch (Exception $e) {
    echo "hey, something went wrong:" . $e->getMessage();
    return false;
  }

  return true;
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