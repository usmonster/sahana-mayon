<?php

/**
 * A set of functions used by the administration configuration section
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */

require_once (dirname(__FILE__) . '/../../../../../lib/vendor/symfony/lib/yaml/sfYaml.php');
require_once (sfConfig::get('sf_config_dir') . '/ProjectConfiguration.class.php');
require_once (sfConfig::get('sf_web_dir') . '/requirements.inc.php');
require_once (sfConfig::get('sf_app_lib_dir') . '/install/func.inc.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'all', false);

class agConfig
{
    private static $instance;
    public static $AG_CONFIG;
    // A private constructor; prevents direct creation of object
  private function __construct()
  {
       //echo 'I am constructed';
  }
  public static function singleton() {
    if(!isset(self::$instance)) {
      $c = __CLASS__;
      self::$instance = new $c();
    }
    return self::$instance;
  }
  public static function getCurrent()
  {
    if (file_exists(sfConfig::get('sf_config_dir') . '/databases.yml') == TRUE) {
      $dbArray = sfYaml::load(sfConfig::get('sf_config_dir') . '/databases.yml');
    } else {
      $install_flag = false;
    }
    if (file_exists(sfConfig::get('sf_config_dir') .  '/config.yml') == TRUE) {
      $cfgArray = sfYaml::load(sfConfig::get('sf_config_dir') .  '/config.yml');
    } else {
      $install_flag = true;
      $existing_auth_method = "bypass";
    }
    try {
      $db_params = parseDSN($dbArray['all']['doctrine']['param']['dsn']);
      self::setConfig('db_config', $dbArray);
      self::setConfig('DB_SERVER', $db_params['host']);
      self::setConfig('DB_DATABASE', $db_params['dbname']);
      self::setConfig('DB_USER', $dbArray['all']['doctrine']['param']['username']);
      self::setConfig('DB_PASSWORD', $dbArray['all']['doctrine']['param']['password']);
      self::setConfig('ADMIN_NAME', $cfgArray['admin']['admin_name']);
      self::setConfig('ADMIN_EMAIL', $cfgArray['admin']['admin_email']);
      self::setConfig('AUTH_METHOD', $cfgArray['admin']['auth_method']['value']);
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