<?php

/**
 * extends agActions for the administration module
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Clayton Kramer, CUNY SPS
 * @author Charles Wisniewski, CUNY SPS
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
// Load Symfony framework libs
require_once (dirname(__FILE__) . '/../../lib/vendor/symfony/lib/yaml/sfYaml.php');
require_once (dirname(__FILE__) . '/../../config/ProjectConfiguration.class.php');
require_once (dirname(__FILE__) . '/../../apps/frontend/lib/install/func.inc.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'all', false);

// Load installer's Slim framwork and libs
require_once 'Slim/Slim.php';
require_once 'lib/config.php';
require_once 'lib/functions.php';
require_once 'lib/requirements.php';


// Initiate a new Slim application instance
$app = new Slim();

/**
 * Define installation wizard routes
 * 
 */
//GET route
$app->get('/', function () use ($app) {

        // Step 0
        $step = 0;

        // Initiate an installation session
        if (empty($_SESSION['install'])) {
            $_SESSION['install']['maxStep'] = $step;
        }

        //Get the Request root and resource URI
        $data['rootUri'] = $app->request()->getRootUri();
        $data['resourceUri'] = $app->request()->getResourceUri();

        // Get steps
        $data['stepList'] = getStepList($step, $app->request()->getRootUri());
        $data['nextStep'] = getStepRoute($step + 1);
        $data['failCount'] = $step;

        // Display the page
        $app->render('intro.php', $data);
    });


$app->get('/intro', function () use ($app) {
        // Redirect back to the installer root
        $app->redirect($app->request()->getRootUri());
    });


$app->get('/progress', function () use ($app) {

        $response['progress'] = apc_fetch('sahana_install_progress') ? apc_fetch('sahana_install_progress') : 0;
        $response['step'] = apc_fetch('sahana_install_step') ? apc_fetch('sahana_install_step') : "";
        echo json_encode($response);
    });

$app->get('/cancel', function () use ($app) {

        // Clear the session
        $_SESSION['install'] = null;
        session_regenerate_id(true);


        // Redirect back to the installer root
        $app->redirect($app->request()->getRootUri());
    });

$app->get('/license/', function () use ($app) {

        // Step 
        $step = 1;

        // Check step
        if (checkStep($step)) {
            if ($_SESSION['install']['maxStep'] < $step) {
                $_SESSION['install']['maxStep']++;
            }
        } else {
            $app->redirect($app->request()->getRootUri());
        }

        if (isset($_SESSION['install']['licenseAgreement']) && $_SESSION['install']['licenseAgreement']) {
            $data['licenseAgreement'] = "checked";
        } else {
            $data['licenseAgreement'] = "";
        }

        // Read the license file
        $licenseFile = sfConfig::get('sf_root_dir') . '/LICENSE';

        if (file_exists($licenseFile)) {
            $data['license'] = file_get_contents($licenseFile);
        } else {
            $data['license'] = 'Missing licence file. See GPL licence.';
        }

        //Get the Request root and resource URI
        $data['rootUri'] = $app->request()->getRootUri();
        $data['resourceUri'] = $app->request()->getResourceUri();

        // Get steps
        $data['stepList'] = getStepList($step, $app->request()->getRootUri());
        $data['prevStep'] = getStepRoute($step - 1);
        if (isset($_SESSION['install']['licenseAgreement']) && $_SESSION['install']['licenseAgreement'] == 'agree') {
            $data['nextStep'] = getStepRoute($step + 1);
        }
        $data['failCount'] = 0;

        // Display the page
        $app->render("license.php", $data);
    })->name('license');

$app->post('/license/', function () use ($app) {

        // Step 
        $step = 1;

        // Check step
        if (checkStep($step)) {
            if ($_SESSION['install']['maxStep'] < $step) {
                $_SESSION['install']['maxStep']++;
            }
        } else {
            $app->redirect($app->request()->getRootUri());
        }


        // Read the license file
        $licenseFile = sfConfig::get('sf_root_dir') . '/LICENSE';

        if (file_exists($licenseFile)) {
            $data['license'] = file_get_contents($licenseFile);
        } else {
            $data['license'] = 'Missing licence file. See GPL licence.';
        }

        // Check license agreement
        if (isset($_POST['agree']) && $_POST['agree'] == true) {
            $_SESSION['install']['licenseAgreement'] = true;
            $data['licenseAgreement'] = "checked";
        } else {
            $data['licenseAgreement'] = "";
            $_SESSION['install']['licenseAgreement'] = null;
        }

        //Get the Request root and resource URI
        $data['rootUri'] = $app->request()->getRootUri();
        $data['resourceUri'] = $app->request()->getResourceUri();

        // Get steps
        $data['stepList'] = getStepList($step, $app->request()->getRootUri());
        $data['prevStep'] = getStepRoute($step - 1);

        if (isset($_POST['agree']) && $_POST['agree'] == true) {
            $data['nextStep'] = getStepRoute($step + 1);
        }
        $data['failCount'] = 0;

        // Display the page
        $app->render("license.php", $data);
    })->name('license_post');

$app->get('/syscheck/', function () use ($app) {

        // Step
        $step = 2;

        // Check step
        if (checkStep($step)) {
            if ($_SESSION['install']['maxStep'] < $step) {
                $_SESSION['install']['maxStep']++;
            }
        } else {
            $app->redirect($app->request()->getRootUri());
        }

        //Get the Request root and resource URI
        $data['rootUri'] = $app->request()->getRootUri();
        $data['resourceUri'] = $app->request()->getResourceUri();

        // Get steps
        $data['stepList'] = getStepList($step, $app->request()->getRootUri());
        $data['prevStep'] = getStepRoute($step - 1);
        $data['nextStep'] = getStepRoute($step + 1);

        // Check the requirements
        $data['phpReqs'] = check_php_requirements();
        $data['dirPermissions'] = check_directory_permissions();

        // Test for blocking failures
        $failCount = 0;
        foreach ($data['phpReqs'] as $req) {

            if ($req['result'] == 0) {
                $failCount++;
            }
        }
        $data['failCount'] = $failCount;

        // Display the page
        $app->render('syscheck.php', $data);
    })->name('syscheck');

$app->get('/filecheck/', function () use ($app) {

        // Step
        $step = 3;

        // Check step
        if (checkStep($step)) {
            if ($_SESSION['install']['maxStep'] < $step) {
                $_SESSION['install']['maxStep']++;
            }
        } else {
            $app->redirect($app->request()->getRootUri());
        }

        //Get the Request root and resource URI
        $data['rootUri'] = $app->request()->getRootUri();
        $data['resourceUri'] = $app->request()->getResourceUri();

        // Get steps
        $data['stepList'] = getStepList($step, $app->request()->getRootUri());
        $data['prevStep'] = getStepRoute($step - 1);
        $data['nextStep'] = getStepRoute($step + 1);

        // Check the requirements
        $data['filePerms'] = check_directory_permissions();

        // Test for blocking failures
        $failCount = 0;
        foreach ($data['filePerms'] as $req) {

            if ($req['result'] == 0) {
                $failCount++;
            }
        }
        $data['failCount'] = $failCount;

        // Display the page
        $app->render('filecheck.php', $data);
    })->name('filecheck');



$app->get('/dbconfig/', function () use ($app) {

        // Step
        $step = 4;

        // Check step
        if (checkStep($step)) {
            if ($_SESSION['install']['maxStep'] < $step) {
                $_SESSION['install']['maxStep']++;
            }
        } else {
            $app->redirect($app->request()->getRootUri());
        }

        //Get the Request root and resource URI
        $data['rootUri'] = $app->request()->getRootUri();
        $data['resourceUri'] = $app->request()->getResourceUri();

        // Get steps
        $data['stepList'] = getStepList($step, $app->request()->getRootUri());
        $data['prevStep'] = getStepRoute($step - 1);
        $data['nextStep'] = getStepRoute($step + 1);

        // Load current config
        $dbConfig = getCurrentDBConfig();
        if (!empty($dbConfig['dsn'])) {
            $dsnParams = parseDSN($dbConfig['dsn']);

            $dbName = $dsnParams['dbname'];
            $dbType = $dsnParams['dbtype'];
            $dbHost = $dsnParams['host'];
            $dbPort = isset($dsnParams['port']) ? $dsnParams['port'] : null;
        }
        if (isset($dbConfig['username'])) {
            $dbUser = $dbConfig['username'];
        } else {
            $dbUser = null;
        }
        if (isset($dbConfig['password'])) {
            $dbPassword = $dbConfig['password'];
        } else {
            $dbPassword = null;
        }

        // Test for blocking failures
        if ($_SESSION['install']['maxStep'] > $step) {
            $failCount = 0;
        } else {
            $failCount = 1;
        }

        // Prepare form data
        $data['failCount'] = $failCount;

        $data['dbHost'] = isset($dbHost) ? $dbHost : "localhost";
        $data['dbPort'] = isset($dbPort) ? $dbPort : "";
        $data['dbName'] = isset($dbName) ? $dbName : "sahana_rmp";
        $data['dbUser'] = isset($dbUser) ? $dbUser : "sahana_user";

        // Give the form the obfuscated version of the input
        $data['dbPassword'] = empty($dbPassword) ? "" : obfuscateString($dbPassword);

        // Look for enabled sample data in session
        if (isset($_SESSION['install']['sampleData']) && $_SESSION['install']['sampleData'] == true) {
            $data['sampleData'] = true;
        } else {
            $data['sampleData'] = false;
        }

        // Display the page
        $app->render('dbconfig.php', $data);
    })->name('dbconfig_get');

$app->post('/dbconfig/', function () use ($app) {

        // Step
        $step = 4;

        // Declare failCount
        $failCount = 0;
        $result = "";

        // Check step
        if (checkStep($step)) {
            if ($_SESSION['install']['maxStep'] < $step) {
                $_SESSION['install']['maxStep']++;
            }
        } else {
            $app->redirect($app->request()->getRootUri());
        }

        //Get the Request root and resource URI
        $data['rootUri'] = $app->request()->getRootUri();
        $data['resourceUri'] = $app->request()->getResourceUri();

        // Get steps
        $data['stepList'] = getStepList($step, $app->request()->getRootUri());
        $data['prevStep'] = getStepRoute($step - 1);
        $data['nextStep'] = getStepRoute($step + 1);

        // Validate host
        if (empty($_POST['db_host'])) {
            $failCount++;
            $result = showStatus(0) . " : You must provide the hostname of the MySQL server.";
            $dbHost = "";
        } else {
            $dbHost = $_POST['db_host'];
        }

        // Get the port
        $dbPort = isset($_POST['db_port']) ? $_POST['db_port'] : null;

        // Validate database name
        if (empty($_POST['db_name'])) {
            $failCount++;
            $result = showStatus(0) . " : MySQL database name not provided.";
            $dbName = "";
        } else {
            $dbName = $_POST['db_name'];
        }

        // Make sure there is a MySQL account username
        if (empty($_POST['db_user'])) {
            $failCount++;
            $result = showStatus(0) . " : A username is required to connect to the MySQL server service.";
            $dbUser = "";
        } else {
            $dbUser = $_POST['db_user'];
        }

        // Get the MySQL account password
        $dbPass = isset($_POST['db_pass']) ? $_POST['db_pass'] : null;

        // Skip these next steps if we don't have valid input
        if ($failCount == 0) {

            // Load current database config from file if it exits
            $dbConfig = array();
            $dbConfig = getCurrentDBConfig();
            if (isset($dbConfig['username'])) {
                $data['dbUser'] = $dbConfig['username'];
            }
            if (isset($dbConfig['password'])) {
                $hashedPass = obfuscateString($dbConfig['password']);
            } else {
                $hashedPass = null;
            }
            if (!empty($dbConfig['dsn'])) {
                $dsnParams = parseDSN($dbConfig['dsn']);

                $data['dbName'] = $dsnParams['dbname'];
                $data['dbType'] = $dsnParams['dbtype'];
                $data['dbHost'] = $dsnParams['host'];
                $data['dbPort'] = isset($dsnParams['port']) ? $dsnParams['port'] : null;
            }

            // Apply db attributes if they don't already exist
            if (empty($dbConfig['attributes'])) {
                $dbConfig['attributes'] = array(
                  'default_table_type' => 'INNODB',
                  'default_table_charset' => 'utf8',
                  'default_table_collate' => 'utf8_general_ci'
                );
            }

            // Update dbconfig array from posted values

            $data['db_user'] = $dbUser;

            // Handle obfuscated passwords
            if (isset($dbPass)) {
                if ($dbPass == $hashedPass) {
                    // Matches so use password from file instead
                    $dbPass = empty($dbConfig['password']) ? "" : $dbConfig['password'];
                }
            }

            // Construct DSN
            if (!empty($dbPort)) {
                $dbConfig['dsn'] = "mysql:host=$dbHost:$dbPort;dbname=$dbName";
            } else {
                $dbConfig['dsn'] = "mysql:host=$dbHost;dbname=$dbName";
            }
            $dbConfig['username'] = $dbUser;
            $dbConfig['password'] = $dbPass;

            // Write file

            if (!saveDbConfig($dbConfig)) {
                $result = showStatus(0) . " : Error writing database configuration file.";
                $failCount = 1;
            }

            if ($failCount == 0) {
                // Test connection
                $testResult = CheckConnection($dbConfig);
                if ($testResult != 'good') {
                    $failCount++;
                    $result = showStatus(0) . " : " . $testResult;
                } else {
                    $result = showStatus(2) . " : Successfully connected to the database.";
                }
            }
        }

        // Store sample data option in session
        $data['sampleData'] = isset($_POST['sample_data']) ? true : false;
        $_SESSION['install']['sampleData'] = $data['sampleData'];

        // Prepare form data
        $data['dbHost'] = $dbHost;
        $data['dbPort'] = $dbPort;
        $data['dbName'] = $dbName;
        $data['dbUser'] = $dbUser;

        // Give the form the obfuscated version of the input
        $data['dbPassword'] = empty($dbPass) ? $dbPass : obfuscateString($dbPass);

        $data['testResult'] = $result;
        $data['failCount'] = $failCount;

        // Display the page
        $app->render('dbconfig.php', $data);
    })->name('dbconfig_post');

$app->get('/superuser/', function () use ($app) {

        // Step
        $step = 5;

        // Check step
        if (checkStep($step)) {
            if ($_SESSION['install']['maxStep'] < $step) {
                $_SESSION['install']['maxStep']++;
            }
        } else {
            $app->redirect($app->request()->getRootUri());
        }

        //Get the Request root and resource URI
        $data['rootUri'] = $app->request()->getRootUri();
        $data['resourceUri'] = $app->request()->getResourceUri();

        // Get steps
        $data['stepList'] = getStepList($step, $app->request()->getRootUri());
        $data['prevStep'] = getStepRoute($step - 1);
        $data['nextStep'] = getStepRoute($step + 1);

        // Check the requirements
        $sfConfig = getCurrentSfConfig();

        $data['superUser'] = isset($sfConfig['sudo']['super_user']) ? $sfConfig['sudo']['super_user'] : "";

        // Obfuscate the su password
        if (isset($sfConfig['sudo']['super_pass'])) {
            $suPassword = obfuscateString($sfConfig['sudo']['super_pass']);
        } else {
            $suPassword = "";
        }
        $data['superPassword'] = $suPassword;


        $data['adminName'] = isset($sfConfig['admin']['admin_name']) ? $sfConfig['admin']['admin_name'] : "";
        $data['adminEmail'] = isset($sfConfig['admin']['admin_email']) ? $sfConfig['admin']['admin_email'] : "";

        // Test for blocking failures
        $failCount = 1;
        $data['failCount'] = $failCount;
        $data['errors'] = array();

        // Display the page
        $app->render('superuser.php', $data);
    })->name('superuser');

$app->post('/superuser/', function () use ($app) {

        // Step
        $step = 5;

        $failCount = 0;
        $errors = array();

        // Check step
        if (checkStep($step)) {
            if ($_SESSION['install']['maxStep'] < $step) {
                $_SESSION['install']['maxStep']++;
            }
        } else {
            $app->redirect($app->request()->getRootUri());
        }

        //Get the Request root and resource URI
        $data['rootUri'] = $app->request()->getRootUri();
        $data['resourceUri'] = $app->request()->getResourceUri();

        // Get steps
        $data['stepList'] = getStepList($step, $app->request()->getRootUri());
        $data['prevStep'] = getStepRoute($step - 1);
        $data['nextStep'] = getStepRoute($step + 1);

        // Validate the input
        if (empty($_POST['admin_user'])) {
            $failCount++;
            $errors[] = showStatus(0) . " : " . "You must supply a login value.";
            $adminUser = null;
        } else {
            $adminUser = $_POST['admin_user'];
        }
        if (empty($_POST['admin_pass'])) {
            $failCount++;
            $errors[] = showStatus(0) . " : " . "You must supply a password for the super user.";
        }


        // Get the current SF config if it exits
        $sfConfig = getCurrentSfConfig();

        // Handle password
        $hashedPass = isset($sfConfig['sudo']['super_pass']) ? obfuscateString($sfConfig['sudo']['super_pass']) : null;
        if (isset($_POST['admin_pass'])) {
            if ($_POST['admin_pass'] == $hashedPass) {
                // Matches so use password from file
                $adminPass = empty($sfConfig['sudo']['super_pass']) ? "" : $sfConfig['sudo']['super_pass'];
            } else {
                // Use the value from the post
                $adminPass = $_POST['admin_pass'];
            }
        } else {
            $adminPass = "";
        }

        // Save the configuration to file
        $sfConfig = array(
          'is_installed' => array('value' => 'true'),
          'sudo' => array(
            'super_user' => $_POST['admin_user'],
            'super_pass' => $adminPass
          ),
          'admin' => array(
            'admin_name' => $_POST['admin_name'],
            'admin_email' => $_POST['admin_email'],
            'auth_method' => array('value' => 'default'),
            'log_level' => array('value' => 'default'),
          )
        );

        // Save the file if we have no errors
        if ($failCount == 0) {
            $result = saveSfConfig($sfConfig);
        }

        // Reload the SF configuration from file
        $sfConfig = getCurrentSfConfig();

        $data['superUser'] = isset($sfConfig['sudo']['super_user']) ? $sfConfig['sudo']['super_user'] : "";
        $data['superPassword'] = (isset($sfConfig['sudo']['super_pass']) && isset($_POST['admin_pass'])) ? obfuscateString($adminPass) : "";
        $data['adminName'] = isset($sfConfig['admin']['admin_name']) ? $sfConfig['admin']['admin_name'] : "";
        $data['adminEmail'] = isset($sfConfig['admin']['admin_email']) ? $sfConfig['admin']['admin_email'] : "";

        // Test for blocking failures

        $data['failCount'] = $failCount;
        $data['errors'] = $errors;

        // Display the page
        $app->render('superuser.php', $data);
    })->name('superuser_post');



$app->get('/confirm/', function () use ($app) {

        // Step 
        $step = 6;

        // Check step
        if (checkStep($step)) {
            if ($_SESSION['install']['maxStep'] < $step) {
                $_SESSION['install']['maxStep']++;
            }
        } else {
            $app->redirect($app->request()->getRootUri());
        }

        //Get the Request root and resource URI
        $data['rootUri'] = $app->request()->getRootUri();
        $data['resourceUri'] = $app->request()->getResourceUri();

        // Get steps
        $data['stepList'] = getStepList($step, $app->request()->getRootUri());
        $data['prevStep'] = getStepRoute($step - 1);
        $data['nextStep'] = getStepRoute($step + 1);

        // Get current config
        $sfConfig = getCurrentSfConfig();
        $data['superUser'] = isset($sfConfig['sudo']['super_user']) ? $sfConfig['sudo']['super_user'] : "";
        $data['superPassword'] = isset($sfConfig['sudo']['super_pass']) ? "****" : "";
        $data['adminName'] = isset($sfConfig['admin']['admin_name']) ? $sfConfig['admin']['admin_name'] : "";
        $data['adminEmail'] = isset($sfConfig['admin']['admin_email']) ? $sfConfig['admin']['admin_email'] : "";

        // Load current database config
        $dbConfig = getCurrentDBConfig();
        if (!empty($dbConfig['dsn'])) {
            $dsnParams = parseDSN($dbConfig['dsn']);

            $data['dbName'] = $dsnParams['dbname'];
            $data['dbType'] = $dsnParams['dbtype'];
            $data['dbHost'] = $dsnParams['host'];
            $data['dbPort'] = isset($dsnParams['port']) ? $dsnParams['port'] : null;
        }
        $data['dbUser'] = $dbConfig['username'];
        $data['dbPassword'] = $dbConfig['password'];
        $data['sampleData'] = $_SESSION['install']['sampleData'] ? "yes" : "no";

        // Test for blocking failures
        $failCount = 0;

        $data['failCount'] = $failCount;

        $foo = new agSudoSigninForm();
        $data['csrfToken'] = $foo->getCSRFToken();


        // Display the page
        $app->render('confirm.php', $data);
    })->name('confirm');


$app->get('/summary/', function () use ($app) {

        // Step 
        $step = 7;

        // Check step
        if (checkStep($step)) {
            if ($_SESSION['install']['maxStep'] < $step) {
                $_SESSION['install']['maxStep']++;
            }
        } else {
            $app->redirect($app->request()->getRootUri());
        }

        //Get the Request root and resource URI
        $data['rootUri'] = $app->request()->getRootUri();
        $data['resourceUri'] = $app->request()->getResourceUri();

        // Get steps
        $data['stepList'] = getStepList($step, $app->request()->getRootUri());
        $data['prevStep'] = getStepRoute($step - 1);

        // Get current config
        $sfConfig = getCurrentSfConfig();
        $data['superUser'] = $sfConfig['sudo']['super_user'];
        $data['superPassword'] = $sfConfig['sudo']['super_pass'];

        // Load current database config
        $dbConfig = getCurrentDBConfig();

        // See if sample data is enabled
        $sampleData = $_SESSION['install']['sampleData'];

        // Execute the database installation
        $data['results'] = doInstall($dbConfig, $data['superUser'], $data['superPassword'], $sampleData);

        // Check install results for fails
        $failCount = 0;
        $installSuccess = true;
        foreach ($data['results'] as $result) {
            if ($result[1] != 2) {
                $failCount++;
                $installSuccess = false;
            }
        }
        $data['failCount'] = $failCount;
        $data['installSuccess'] = $installSuccess;

        // Clear the session
        $_SESSION['install'] = null;
        session_regenerate_id(true);

        // Display the page
        $app->render('summary.php', $data);
    })->name('summary');

/**
 *  Run the Slim application
 *
 */
$app->run();
