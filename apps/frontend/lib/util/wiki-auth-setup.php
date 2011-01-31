<?php
require_once (dirname(__FILE__) . '/../../../lib/vendor/symfony/lib/yaml/sfYaml.php');

$cfgArray = sfYaml::load(dirname(__FILE__) . '/../../../config/config.yml');

$username = $cfgArray[sudo][super_user];
$pass_hash = md5($cfgArray[sudo][super_pass]);
$name = $cfgArray[admin][admin_name];
$email = $cfgArray[admin][admin_email];
$groups = 'admin,user';
$line = implode(':',array($username,$pass_hash,$name,$email,$groups));
$lines = file(dirname(__FILE__) . '/../../../web/wiki/conf/users.auth.php');
$lines[count($lines)-1] = $line;
//echo join('',$lines);
$fh = fopen(dirname(__FILE__) . '/../../../web/wiki/conf/users.auth.php', 'w');
fwrite($fh, join('',$lines));
fclose($fh);
