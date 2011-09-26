<?php

/**
 * Provides a quick way to enable Agasti accounts as web services
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Clayton Kramer, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
require_once (dirname(__FILE__) . '/../../../../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'all', false);
require_once (sfConfig::get('sf_lib_dir') . '/vendor/symfony/lib/yaml/sfYaml.php');

// Create a database connection
$databaseManager = new sfDatabaseManager($configuration);
$databaseManager->loadConfiguration();


// Setup options
$shortopts = "u:t:h";
$options = getopt($shortopts);

foreach ($options as $key => $value) {
    switch ($key) {
        case "u":
            $webSrvUser = $value;
            break;
        case "t":
            $webSrvToken = $value;
            break;
        case "h":
            print_help_message();
            exit(1);
    }
}

if (!isset($webSrvUser)) {
    echo ("Error: No user name provided.\n");
    print_help_message();
    exit(1);
}


if (!isset($webSrvToken)) {
    $webSrvToken = rand_sha1(40);
}


$user = Doctrine_Core::getTable('sfGuardUser')->findOneBy('username', $webSrvUser, Doctrine_Core::HYDRATE_ARRAY);

if (isset($user['id'])) {

    if (!$profile = Doctrine_Core::getTable('sfGuardUserProfile')->findOneBy('user_id', $user['id'])) {

        // Record doesn't exist
        $newRec = new sfGuardUserProfile($profile, true);
        $newRec['user_id'] = $user['id'];
        $newRec['token'] = $webSrvToken;
        $newRec['is_webservice_client'] = 1;
        $newRec['is_active'] = 1;

        try {
            $newRec->save();
        } catch (Exception $e) {
            
        }
    } else {

        // Update token
        $profile['token'] = $webSrvToken;
        $profile->save();
    }
    echo ("User $webSrvUser updated.\n");
    echo ("Token = $webSrvToken\n");
} else {
    echo ("Error: User does not exist.\n");
}

//print_r($newRec);
// Random sha1 string
function rand_sha1($length)
{
    $max = ceil($length / 40);
    $random = '';
    for ($i = 0; $i < $max; $i++) {
        $random .= sha1(microtime(true) . mt_rand(10000, 90000));
    }
    return substr($random, 0, $length);
}

function print_help_message()
{
    $msg = "Usage: webservice-token-setup.php [options] -u <username> 
       webservice-token-setup.php [options] -u <username> -t <token>

  -h            This help
  -t <string>   Web service token ID string. This is optional. The script will
                   automatically generate a 40 char SHA1 string otherwise.         
  -u <string>   Username of the Agasti account that should be web service enabled.
  
";
    print($msg);
}
