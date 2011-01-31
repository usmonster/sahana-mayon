<?php

/**
 * This utility reads the Agasti configuration and copies the credentials so that they may be used
 * in the wiki.
 */
require_once (dirname(__FILE__) . '/../../../../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'all', false);
require_once (sfConfig::get('sf_lib_dir') . '/vendor/symfony/lib/yaml/sfYaml.php');

// loads the configuration
$cfgArray = sfYaml::load(sfConfig::get('sf_config_dir') . '/config.yml');

// reads relevant components of the Agasti configuration file
$username = $cfgArray[sudo][super_user];
$pass_hash = md5($cfgArray[sudo][super_pass]);
$name = $cfgArray[admin][admin_name];
$email = $cfgArray[admin][admin_email];
$groups = 'admin,user';

// builds the wiki auth file contents
$lines = array();
$lines[] = '# users.auth.php';
$lines[] = '# <?php exit()?>';
$lines[] = "# Don't modify the lines above";
$lines[] = implode(':', array($username, $pass_hash, $name, $email, $groups));
$lines[] = '';

// writes and closes the file
$auth_file = sfConfig::get('sf_web_dir') . '/wiki/conf/users.auth.php';
$fh = fopen($auth_file, 'w');
fwrite($fh, join("\n", $lines));
fclose($fh);
