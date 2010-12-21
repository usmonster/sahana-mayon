<?php
require_once (dirname(__FILE__) . '/../../../../lib/vendor/symfony/lib/yaml/sfYaml.php');
require_once (dirname(__FILE__) . '/../../../config/ProjectConfiguration.class.php');
require_once (dirname(__FILE__) . '/../../../../requirements.inc.php');
require_once (dirname(__FILE__) . '/../../../../func.inc.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'all', false);

class agConfig
{
    private static $instance;
    public static $AG_CONFIG;
    // A private constructor; prevents direct creation of object
  private function __construct()
  {
       echo 'I am constructed';
  }
  public static function getCurrent()
  {
    if (file_exists(dirname(__FILE__) . '/../config/databases.yml') == TRUE) {
      $dbArray = sfYaml::load(dirname(__FILE__) . '/../config/databases.yml');
    } else {
      $install_flag = false;
    }
    if (file_exists(dirname(__FILE__) . '/../config/config.yml') == TRUE) {
      $cfgArray = sfYaml::load(dirname(__FILE__) . '/../config/config.yml');
    } else {
      $install_flag = true;
      $existing_auth_method = "bypass";
    }
    try {
      $db_params = parseDSN($dbArray['all']['doctrine']['param']['dsn']);
      $this->setConfig('db_config', $dbArray);
      $this->setConfig('DB_SERVER', $db_params['host']);
      $this->setConfig('DB_DATABASE', $db_params['dbname']);
      $this->setConfig('DB_USER', $dbArray['all']['doctrine']['param']['username']);
      $this->setConfig('DB_PASSWORD', $dbArray['all']['doctrine']['param']['password']);
      $this->setConfig('ADMIN_NAME', $cfgArray['admin']['admin_name']);
      $this->setConfig('ADMIN_EMAIL', $cfgArray['admin']['admin_email']);
      $this->setConfig('AUTH_METHOD', $cfgArray['admin']['auth_method']['value']);
    } catch (Exception $e) {
      return 'file was unreadable';
    }
    return array($dbArray, $cfgArray);
  }

  public static function getConfig($name, $default = null)
  {
    return isset(self::$AG_CONFIG[$name]) ? self::$AG_CONFIG[$name] : $default;
  }

  public static function setConfig($name, $value)
  {
    return (self::$AG_CONFIG[$name] = $value);
  }

}