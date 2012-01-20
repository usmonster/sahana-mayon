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

function getStepName($id = 0)
{

    global $steps;

    return $steps[$id]['title'];
}

function getStepRoute($id = 0)
{

    global $steps;

    return $steps[$id]['route'];
}

/**
 * this is a simple html constructor, takes our array and
 * generates a series of list items
 */
function getStepList($stepNum = 0, $rootUri)
{
    global $steps;

    $maxStep = $_SESSION['install']['maxStep'];

    $list = "<ul>";
    foreach ($steps as $id => $value) {
        if ($id <= $maxStep && $id != $stepNum) {
            $liStyle = 'completed';
            $btnStyle = 'completedButton';
        } else if ($id == $stepNum) {
            $liStyle = 'current';
            $btnStyle = 'currentButton';
        } else {
            $liStyle = null;
            $btnStyle = 'incompleteButton';
        }

        $count = $id + 1;
        $list .= '<li class="' . $liStyle . '">';
        if ($maxStep >= $id) {
            $list .= '<a class="' . $btnStyle . '" href="' . $rootUri . $value['route'] . '">';
            $list .= $count . '. ' . $value['title'];
            $list .= '</a></li>';
        } else {
            $list .= '<span class="' . $btnStyle . '">';
            $list .= $count . '. ' . $value['title'];
            $list .= '</span></li>';
        }
    }
    $list = $list . "</ul>";
    return $list;
}

function showStatus($value)
{

    switch ($value) {
        case 1:
            $result = '<span class="orange">Passed</span>';
            break;
        case 2:
            $result = '<span class="green">Success</span>';
            break;
        default:
            $result = '<span class="fail">Failed</span>';
    }

    return $result;
}

function checkStep($step = 0)
{

    // Check the installation steps
    if (isset($_SESSION['install']['maxStep']) || $_SESSION['install']['maxStep'] >= $step) {
        $result = true;
    } else {
        $result = false;
    }

    return $result;
}

function check_php_requirements()
{
    $result = array();

    $result[] = check_php_version();
    $result[] = check_php_memory_limit();
    $result[] = check_php_post_max_size();
    $result[] = check_php_upload_max_filesize();
    $result[] = check_php_max_execution_time();
    $result[] = check_php_max_input_time();
    $result[] = check_php_timezone();
    $result[] = check_php_databases();
    $result[] = check_php_bc();
    $result[] = check_php_mbstring();
    $result[] = check_php_sockets();
    $result[] = check_php_session();
    $result[] = check_php_gd();
    $result[] = check_php_gd_png();

    return $result;
}

function check_directory_permissions()
{
    $result = array();

    $result[] = check_file_permissions(sfConfig::get('sf_config_dir'), '0775');
    $result[] = check_file_permissions(sfConfig::get('sf_app_config_dir'), '0775');
    $result[] = check_file_permissions(sfConfig::get('sf_data_dir'), '0775');
    $result[] = check_file_permissions(sfConfig::get('sf_data_dir') . '/search/', '0775');
    $result[] = check_file_permissions(sfConfig::get('sf_data_dir') . '/sql/', '0775');
    $result[] = check_file_permissions(sfConfig::get('sf_data_dir') . '/uploads/', '0775');
    $result[] = check_file_permissions(sfConfig::get('sf_data_dir') . '/downloads/', '0775');
    $result[] = check_file_permissions(sfConfig::get('sf_data_dir') . '/xspchart/', '0775');

    return $result;
}

function getCurrentDBConfig()
{
    // Declare the param variable array
    $dbArray['all']['doctrine']['param'] = null;

    // Parse the SF yml for database settings
    $filename = sfConfig::get('sf_config_dir') . '/databases.yml';
    if (file_exists($filename)) {
        $dbArray = sfYaml::load($filename);
    }

    return $dbArray['all']['doctrine']['param'];
}

function getCurrentSfConfig()
{
    $cfgArray = array();
    $filename = sfConfig::get('sf_config_dir') . '/config.yml';
    if (file_exists($filename)) {
        $cfgArray = sfYaml::load($filename);
    }

    return $cfgArray;
}

function dbParams(array $dbParams)
{
    $foo = $dbParams;
    $arguments = array(
      'task' => 'configure:database',
      'dsn' => $dbParams['dsn'],
      'username' => $dbParams['username'],
      'password' => $dbParams['password'],
    );
    $options = array(
      'help' => null,
      'quiet' => null,
      'trace' => null,
      'version' => null,
      'color' => null,
      'env' => 'all',
      'name' => 'doctrine',
      'class' => 'sfDoctrineDatabase',
      'app' => null
    );
    $dispatcher = new sfEventDispatcher();
    $formatter = new sfFormatter();
    $dbConfig = new agDoctrineConfigureDatabaseTask($dispatcher, $formatter);
    $dbConfig->execute($arguments, $options);
    try {
        $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'all', false);
    } catch (Exception $e) {
        $configuration = false;
    }
    return $configuration;
}

function CheckConnection(array $dbConfig)
{
    if (!empty($dbConfig)) {
        try {
            $databaseManager = new sfDatabaseManager(dbParams($dbConfig));
            $connection = Doctrine_Manager::connection()->connect();
            $result = 'good';
        } catch (Exception $e) {
            $result = "Connection error : " . $e->getMessage();
        }
    } else {
        $result = 'Database configuration is not valid: bad DSN';
    }
    return $result;
}

/** these two extended classes are for configuring doctrine */
class agDoctrineConfigureDatabaseTask extends sfDoctrineConfigureDatabaseTask
{

    function execute($arguments = array(), $options = array())
    {
        parent::execute($arguments, $options);
    }

}

class agDoctrineBuildSqlTask extends sfDoctrineBuildSqlTask
{

    function execute($arguments = array(), $options = array())
    {
        try {
            parent::execute($arguments, $options);
        } catch (Exception $e) {
            throw new Doctrine_Exception($e->getMessage());
        }
    }

}

function saveDbConfig($dbConfig)
{
    $success = true;
    $configData['all']['doctrine']['param'] = $dbConfig;

    /** remember to set the superadmin config.yml up!!!  */
    sfClearCache('frontend', 'all');
    sfClearCache('frontend', 'dev');
    sfClearCache('frontend', 'prod');
    require_once sfConfig::get('sf_lib_dir') . '/vendor/symfony/lib/yaml/sfYaml.php';

    $file = sfConfig::get('sf_config_dir') . '/databases.yml';

    // See if the database.yml is already present
    if (file_exists($file)) {
        $dbConfiguration = sfYaml::load($file);
    } else {
        touch($file);
    }

    try {
        file_put_contents($file, sfYaml::dump($configData, 4));
    } catch (Exception $e) {
        echo "Error:" . $e->getMessage();
        $success = false;
    }

    return $success;
}

function saveSfConfig($sfConfig)
{
    $success = true;

    /** remember to set the superadmin config.yml up!!!  */
    sfClearCache('frontend', 'all');
    sfClearCache('frontend', 'dev');
    sfClearCache('frontend', 'prod');
    require_once sfConfig::get('sf_lib_dir') . '/vendor/symfony/lib/yaml/sfYaml.php';

    $appYmlResult = writeAppYml("default");
    $file = sfConfig::get('sf_config_dir') . '/config.yml';

    // See if the database.yml is already present
    if (file_exists($file)) {
        $sfConfiguration = sfYaml::load($file);
    } else {
        touch($file);
    }

    try {
        file_put_contents($file, sfYaml::dump($sfConfig, 4));
    } catch (Exception $e) {
        echo "Error:" . $e->getMessage();
        $success = false;
    }

    return $success;
}

function doInstall(array $dbParams, $adminName, $adminPass, $adminEmail, $loadSamples = false)
{
    $databaseManager = new sfDatabaseManager(dbParams($dbParams));
    $buildSql = new Doctrine_Task_GenerateSql();
    $dropDb = new Doctrine_Task_DropDb();

    $dropDb->setArguments(array('force' => true));
    $buildSql->setArguments(array(
      'models_path' => sfConfig::get('sf_lib_dir') . '/model/doctrine',
      'sql_path' => sfConfig::get('sf_data_dir') . '/sql',
    ));

    // Drop existing database
    $createDb = new Doctrine_Task_CreateDb();
    try {
        if ($dropDb->validate()) {
            $dropDb->execute();
        } else {
            throw new Doctrine_Exception($dropDb->ask());
        }
    } catch (Exception $e) {
        $installed[] = array('Could not drop DB! : ' . "\n" . $e->getMessage(), 0);
    }

    // Create a new database
    try {
        if ($createDb->validate()) {
            $createDb->execute();
        } else {
            throw new Doctrine_Exception($createDb->ask());
        }
        $installed[] = array('Create database', 2);
    } catch (Exception $e) {
        $installed[] = array('Could not create DB! : ' . "\n" . $e->getMessage(), 0);
    }

    // Build schema
    try {
        if ($buildSql->validate()) {
            $buildSql->execute();
        }
        $installed[] = array('Build SQL installation schema', 2);
    } catch (Exception $e) {
        $installed[] = array('Could not build SQL installation schema! : ' . "\n" . $e->getMessage(), 0);
    }

    // Load all the models
    try {
        $allmodels = Doctrine_Core::loadModels(
                sfConfig::get('sf_lib_dir') . '/model/doctrine', Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $installed[] = array('Load all data models', 2);
    } catch (Exception $e) {
        $installed[] = array('Could not load models! : ' . "\n" . $e->getMessage(), 0);
    }

    // Generate create table SQL from models
    try {
        Doctrine_Core::createTablesFromArray(Doctrine_Core::getLoadedModels());
        $installed[] = array('Create database tables from models', 2);
    } catch (Exception $e) {

        $installed[] = array('Could not create tables! : ' . "\n" . $e->getMessage(), 0);
    }

    // Load fixture data
    try {
        // Get the core fixtures
        $dataDirectories = array(sfConfig::get('sf_data_dir') . '/fixtures');
        $installed[] = array('Default fixture data applied', 2);

        // Load sample data fixtures if enabled
        if ($loadSamples) {
            $dataDirectories[] = sfConfig::get('sf_data_dir') . '/samples';
            $installed[] = array('Sample data added', 2);
        }
        Doctrine_Core::loadData($dataDirectories, false);
        $installed[] = array('Database install complete', 2);
    } catch (Exception $e) {
        $installed[] = array('Could not insert SQL! : ' . "\n" . $e->getMessage(), 0);
    }

    try {
        // Create super user as first application user
        $user = new sfGuardUser();
        $user->setUsername($adminName);
        $user->setPassword($adminPass);
        $user->setEmailAddress($adminEmail);
        $user->setIsActive(true);
        $user->save();

        $data['results'][] = array('Super user created in database as first user ', 2);
    } catch (Exception $e) {
        $installed[] = array('Could not create user account', 0);
    }

    // Release the database connection manager
    unset($databaseManager);

    if (is_array($installed)) {
        return $installed;
    }
}

function obfuscateString($str)
{
    return substr(sha1($str . session_id()), 1, 12);
}

?>
