<?php

/**
 * Agasti 2.0 Installer
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
/**
 * Sahana Agasti 2.0 install.inc.php
 * this file houses installation specific functions, primarily called from install.php
 */
require_once (dirname(__FILE__) . '/../lib/vendor/symfony/lib/yaml/sfYaml.php');
require_once (dirname(__FILE__) . '/../config/ProjectConfiguration.class.php');
require_once(dirname(__FILE__) . '/requirements.inc.php');
require_once (dirname(__FILE__) . '/../apps/frontend/lib/install/func.inc.php');
//OR sfProjectConfiguration::getActive()->loadHelpers(array('Install)); ^
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'all', false);

//perhaps we should also autoload-regsiter...

class agInstall
{
  /* protected *//*
    var $AG_CONFIG;
    var $DISABLE_NEXT;
    var $steps = array();
   */

  /* public */

  function __construct(&$AG_CONFIG)
  {
    $this->DISABLE_NEXT = FALSE;
    $this->RETRY_SUCCESS = FALSE;
    $this->ERROR_MESSAGE = '';
    $this->INSTALL_RESULT = '';

    $this->AG_CONFIG = &$AG_CONFIG;

    $this->steps = array(
      0 => array('title' => '1. Introduction', 'fun' => 'stage0'),
      1 => array('title' => '2. License Agreement', 'fun' => 'stage1'),
      2 => array('title' => '3. Prerequisite Check', 'fun' => 'stage2'),
      3 => array('title' => '4. Configure Database', 'fun' => 'stage3'),
      4 => array('title' => '5. Configuration Summary', 'fun' => 'stage4'),
      5 => array('title' => '6. Installation Summary', 'fun' => 'stage5'),
//    6 => array('title' => '7. Enter Secure Mode', 'fun' => 'stage6' ),
    );
    $this->EventHandler();
    GLOBAL $trans;
    $trans = array(
//     requirements.inc.php
      'S_PHP_VERSION' => 'PHP version',
      'S_MINIMAL_VERSION_OF_PHP_IS' => 'Minimal version of PHP is',
      'S_PHP_MEMORY_LIMIT' => 'PHP memory limit',
      'S_IS_A_MINIMAL_PHP_MEMORY_LIMITATION_SMALL' => 'is a minimal PHP memory limitation',
      'S_PHP_POST_MAX_SIZE' => 'PHP post max size',
      'S_IS_A_MINIMUM_SIZE_OF_PHP_POST_SMALL' => 'is minimum size of PHP post',
      'S_PHP_MAX_EXECUTION_TIME' => 'PHP max execution time',
      'S_PHP_MAX_INPUT_TIME' => 'PHP max input time',
      'S_IS_A_MINIMAL_LIMITATION_EXECTUTION_TIME_SMALL' => 'is a minimal limitation on execution time of PHP scripts',
      'S_IS_A_MINIMAL_LIMITATION_INPUT_PARSE_TIME_SMALL' => 'is a minimal limitation on input parse time for PHP scripts',
      'S_PHP_TIMEZONE' => 'PHP timezone',
      'S_NO_SMALL' => 'no',
      'S_YES_SMALL' => 'yes',
      'S_TIMEZONE_FOR_PHP_IS_NOT_SET' => 'Timezone for PHP is not set',
      'S_PLEASE_SET' => 'Please set',
      'S_OPTION_IN_SMALL' => 'option in',
      'S_PHP_DATABASES_SUPPORT' => 'PHP databases support',
      'S_REQUIRES_ANY_DATABASE_SUPPORT' => 'Requires any database support [MySQL or PostgreSQL or Oracle or SQLite3]',
      'S_REQUIRES_BCMATH_MODULE' => 'Requires bcmath module',
      'S_CONFIGURE_PHP_WITH_SMALL' => 'configure PHP with',
      'S_REQUIRES_MB_STRING_MODULE' => 'Requires mb string module',
      'S_PHP_SOCKETS' => 'PHP Sockets',
      'S_REQUIRED_SOCKETS_MODULE' => 'Required Sockets module',
      'S_THE_GD_EXTENSION_IS_NOT_LOADED' => 'The GD extension is not loaded.',
      'S_GD_PNG_SUPPORT' => 'GD PNG Support',
      'S_REQUIRES_IMAGES_GENERATION_SUPPORT' => 'Requires images generation support',
      'S_LIBXML_MODULE' => 'libxml module',
      'S_PHPXML_MODULE_IS_NOT_INSTALLED' => 'php-xml module is not installed',
      'S_CTYPE_MODULE' => 'ctype module',
      'S_REQUIRES_CTYPE_MODULE' => 'Requires ctype module',
      'S_PHP_UPLOAD_MAX_FILESIZE' => 'PHP upload max filesize',
      'S_IS_MINIMAL_FOR_PHP_ULOAD_FILESIZE_SMALL' => 'is minimum for PHP upload filesize',
      'S_SESSION_MODULE' => 'PHP Session',
      'S_REQUIRED_SESSION_MODULE' => 'Required Session module',
    );
    foreach ($trans as $const => $label) {
      if (!defined($const))
        define($const, $label);
    }
    unset($GLOBALS['trans']);
  }

  function getConfig($name, $default = null)
  {
//if entry method to this function is admin/config instead of install, set the global
    return isset($this->AG_CONFIG[$name]) ? $this->AG_CONFIG[$name] : $default;
  }

  function setConfig($name, $value)
  {
    return ($this->AG_CONFIG[$name] = $value);
  }

  function getStep()
  {
    return $this->getConfig('step', 0);
  }

  function DoNext()
  {
    if (isset($this->steps[$this->getStep() + 1])) {
      $this->AG_CONFIG['step']++;
      return true;
    }
    return false;
  }

  function DoBack()
  {
    if (isset($this->steps[$this->getStep() - 1])) {
      $this->AG_CONFIG['step']--;
      return true;
    }
    return false;
  }

  function getList()
  {
    /**
     * this is a simple html constructor, takes our array and
     * generates a series of list items
     */
    $list = "<ul>";
    foreach ($this->steps as $id => $data) {
      if ($id < $this->getStep())
        $style = 'completed';
      else if ($id == $this->getStep())
        $style = 'current';
      else
        $style = null;

      $list = $list . '<li class="' . $style . '">' . $data['title'] . '</li>';
    }
    $list = $list . "</ul>";
    return $list;
  }

  function getState()
  {
    $fun = $this->steps[$this->getStep()]['fun'];
    return $this->$fun();
  }

  function stage0()
  {
    return '<div class=info><h2>Welcome to the Sahana Agasti 2.0 Installation Wizard</h2><br />
      <p>Agasti is an emergency management application with tools to manage staff, resources, 
      client information and facilities through an easy to use web interface. The Installation
      Wizard will guide you through the installation of Agasti 2.0.</p> <br />
      Click the "Next" button to proceed to the Next screen. If you want to change something at 
      a previous step, click the "Previous" button.  You may cancel installation at any time by
      clicking the "Cancel" button.</p>
      <p><b> Click the "Next" button to continue.</p></b></div>';
  }

  function stage1()
  {
    $LICENSE_FILE = sfConfig::get('sf_root_dir') . '/LICENSE';

    $this->DISABLE_NEXT = !$this->getConfig('agree', false);

    $license = 'Missing licence file. See GPL licence.';
    if (file_exists($LICENSE_FILE)) {
      $license = file_get_contents($LICENSE_FILE);
    }

    $agree = '<input class="checkbox" type="checkbox" value="yes" name="agree" id="agree" onclick="submit();"';
    $this->getConfig('agree', false) == 'yes' ? $agree .= ' checked=checked>' : $agree .= '>';

    return '<div class=info>' . nl2br($license) . "</div><br />" . $agree . '<label for="agree">I agree</label>';
  }

  /**
   * step 2: pre-requisite check
   * @return block of html for UI of stage/step 2
   */
  function stage2()
  {

    $table = '<table class="requirements" style="align:center; width:100%;">
                <th><tr style="font-weight:bold;">
                  <td>Option</td><td>Current Value</td><td>Required</td><td>Recommended</td><td>&nbsp;</td></tr></th>';
    /**
     * @todo clean up the following code
     */
    $final_result = true;

    $reqs = check_php_requirements();
    foreach ($reqs as $req) {

      $result = null;
      if (!is_null($req['recommended']) && ($req['result'] == 1)) {
        $result = '<span class="orange">Ok</span>';
      } else if ((!is_null($req['recommended']) && ($req['result'] == 2))
          || (is_null($req['recommended']) && ($req['result'] == 1))) {
        $result = '<span class="green">Ok</span>';
      } else if ($req['result'] == 0) {
        $result = '<span class="fail">Fail</span>';
// this will be useful$result->setHint($req['error']);
      }

      $current = '<tr><td><strong>' . $req['name'] . '</strong></td>' . '<td>' . $req['current'] . '</td>';
      $required = $req['required'] ? $req['required'] : '&nbsp;';
      $recommend = $req['recommended'] ? $req['recommended'] : '&nbsp;';
      $res = $req['result'] ? '&nbsp;' : 'fail';
      $row = '<td>' . $current . '</td><td>' . $required . '</td><td>' . $recommend . '</td><td>' . $result . '</td>';

      $table = $table . $row . '</tr>';

      $final_result &= (bool) $req['result'];
    }

    if (!$final_result) {
      $this->DISABLE_NEXT = true;

      $retry = '<input type="submit" class="inputGray" id="retry" name="retry" value="retry" />';
      $final_result = '<span class="fail">There are errors in your configuration.  
        Please correct all issues and press the "retry" button.  For additional assistance
        reference the README file.</span><br /><br />' . $retry;
    } else {
      $this->DISABLE_NEXT = false;
      $final_result = '<span class="green">Your system is properly configured.  Please continue.</span>';
    }

    return $table . '</table><br />' . $final_result;
  }

  function stage3()
  {
    global $AG_CONFIG; //why? why not $this->AG_CONFIG? -UA
    $this->getCurrent();

    if (isset($_REQUEST['retry']) && $this->RETRY_SUCCESS == false) {
      $retry_label = 'retry';
      $instruct = '<span class="fail">Error</span><br />
      <br />Error Message:' . $this->ERROR_MESSAGE . '<br /><span class="fail">Please correct the
        error and press retry.</span>';
    } else if ($this->RETRY_SUCCESS == false) {
      $retry_label = 'test connection';
      $instruct = 'Press "Test connection" button when done.';
    } else {
      $instruct = 'Database Settings are <span class="green">Ok</span> please click Next';
    }
    $retry = '';
    if ($this->RETRY_SUCCESS == false) {
      $retry = '<input type="submit" class="linkButton" id="retry" name="retry" value="' . $retry_label . '" />';
    }
    $table = '<fieldset>
              <legend><img src="images/database.png" alt="database icon" />Database Configuration:</legend>

              <ul>
                <li>
                  <label>host:</label>
                  <input type="text" name="db_host" id="db_host" class="inputGray"
                         value="' . $this->getConfig('DB_SERVER', 'localhost') . '"/>
                </li>
                <li>
                  <label>database:</label>
                  <input type="text" name="db_name" id="db_name" class="inputGray"
                         value="' . $this->getConfig('DB_DATABASE', 'agasti') . '" />
                </li>
                <li>
                  <label>username:</label>
                  <input type="text" name="db_user" id="db_user" class="inputGray"
                         value="' . $this->getConfig('DB_USER', 'root') . '" />
                </li>
                <li>
                  <label>password:</label>
                  <input type="password" name="db_pass" id="db_pass" class="inputGray"
                         value="' . $this->getConfig('DB_PASSWORD', 'root') . '" />
                </li>
                <input id="init_schema" type="hidden" name="init_schema" checked="checked" />
                <li><span class="fail">this will drop your current database.</span></li>
              </ul>
            </fieldset>
            <fieldset>
              <legend><img src="images/config.png" alt="config gear icon" />Administrator Information:</legend>
              <ul>
                <li>
                  <label>Name:</label>
                  <input type="text" name="admin_name" id="admin_name" class="inputGray"
                         value="' . $this->getConfig('ADMIN_NAME', 'administrator') . '" /><br />
                </li>
                <li>
                  <label>Email:</label>
                  <input type="text" name="admin_email" id="admin_email" class="inputGray"
                         value="' . $this->getConfig('ADMIN_EMAIL', 'b@m.an') . '" /><br />
                </li>
              </ul>
              <legend><img src="images/config.png" alt="config gear icon" />Superuser Information:</legend>
              <ul>
                <li>
                  <label>Superuser:</label>
                  <input type="text" name="admin_user" id="admin_user" class="inputGray"
                         value="' . $this->getConfig('ADMIN_USER', 'superuser') . '" /><br />
                </li>
                <li>
                  <label>Password:</label>
                  <input type="text" name="admin_pass" id="admin_pass" class="inputGray"
                         value="' . $this->getConfig('ADMIN_PASS', 'sEcur3P45s!') . '" /><br />
                </li>
              </ul>              
            </fieldset>';
    $results = 'The database is created manually.  First, the Agasti Installer will test your
      configuration settings before continuing.  Enter your database settings and click "Test Connection". <br /><br/>'
        . $instruct . $table . $retry;

//$this->DISABLE_NEXT ? new CSpan(S_OK,'ok') :  new CSpan(S_FAIL, 'fail'),
//new  CButton('retry', 'Test connection')
    return $results;
  }

  function stage4()
  {
    $current = $this->getCurrent();

    return 'Configuration files written. Below is your installation configuration summary:<br />
      <div class="info">
        <strong>Database Host</strong>: ' . $this->getConfig('DB_SERVER') .
    '<br /><strong>Database Name</strong>: ' . $this->getConfig('DB_DATABASE') .
    '<br /><strong>Database User</strong>: ' . $this->getConfig('DB_USER') .
    '<br /><strong>Database Password</strong>: ' . preg_replace('/./', '*', $this->getConfig('DB_PASSWORD', 'unknown')) .
    '<br /><strong>Administrator Name </strong>: ' . $this->getConfig('ADMIN_NAME') .
    '<br /><strong>Admin E-mail</strong>: ' . $this->getConfig('ADMIN_EMAIL') .
    '<br /><strong>Super User</strong>: ' . $this->getConfig('ADMIN_USER') .
    '<br /><strong>Super User Password:</strong>: *******' .
    '</div>
      <br /> Please verify your settings.  By clicking Next you will install Sahana Agasti.';
  }

  function stage5()
  {
    if ($this->INSTALL_RESULT == 'Success!') {
      return '<span class="okay">Congratulations!  Installation was successful:</span> <br /><div class="info">
        <strong>Database Host</strong>: ' . $this->getConfig('DB_SERVER') .
      '<br /><strong>Database Name</strong>: ' . $this->getConfig('DB_DATABASE') .
      '<br /><strong>Database User</strong>: ' . $this->getConfig('DB_USER') .
      '<br /><strong>Database Password</strong>: ' . preg_replace('/./', '*', $this->getConfig('DB_PASSWORD', 'unknown')) .
      '<br /><strong>Administrator</strong>: ' . $this->getConfig('ADMIN_NAME') .
      '<br /><strong>Admin E-mail</strong>: ' . $this->getConfig('ADMIN_EMAIL') .
      '</div><br /> NOTE: to continue with Agasti setup you must first create the "Super User"
        account by editing the config file.  In .../config please edit the config.yml file with the
        Super User username and password.  After you have done so, click finish and you will be 
        redirected to log in with the Super User username and password and then create your
        first user.';
    } else {
      return '<span class="fail">There was an error with your installation:</span><br /><div class="info">' . $this->INSTALL_RESULT . '</div>';
    }
  }

  function stage6()
  {
    return "there is no step 6 in the installer, well, no screen for it at least: this should login the user and redirect to admin/createuser, i.e. you shouldn't even SEE this";
  }

  function getCurrent()
  {
    $filename = sfConfig::get('sf_config_dir') . '/databases.yml';
    if (file_exists($filename)) {
      $dbArray = sfYaml::load($filename);
    } else {
      $install_flag = false;
    }
    $filename = sfConfig::get('sf_config_dir') . '/config.yml';
    if (file_exists($filename)) {
      $cfgArray = sfYaml::load($filename);
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

      $this->setConfig('ADMIN_PASS', $cfgArray['sudo']['super_pass']);
      $this->setConfig('ADMIN_USER', $cfgArray['sudo']['super_user']);
      $this->setConfig('ADMIN_NAME', $cfgArray['admin']['admin_name']);
      $this->setConfig('ADMIN_EMAIL', $cfgArray['admin']['admin_email']);
      $this->setConfig('AUTH_METHOD', $cfgArray['admin']['auth_method']['value']);
    } catch (Exception $e) {
      return 'file was unreadable';
    }
    return array($dbArray, $cfgArray);
  }

  function dbParams(array $db_params)
  {
    $arguments = array(
      'task' => 'configure:database',
      //'dsn' => buildDsnString('mysql', $_POST['db_host'], $_POST['db_name'], $_POST['db_port']),
      'dsn' => $db_params['dsn'], // ilya 2010-07-21 15:16:58
//'dsn' => buildDsnString($_POST['db_type'], $_POST['db_host'], $_POST['db_name'], $_POST['db_port']),
      'username' => $db_params['username'],
      'password' => $db_params['password'],
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

  function CheckConnection(array $db_config)
  {
    if ($db_config['dsn'] != '') {
      try {
        $databaseManager = new sfDatabaseManager($this->dbParams($db_config));
        $connection = Doctrine_Manager::connection()->connect();
        $result = 'good';
      } catch (Exception $e) {
        $result = $e->getMessage();
      }
    } else {
      $result = 'Database configuration is not valid: bad DSN';
    }
    return $result;
  }

  function doInstall(array $db_params)
  {
    $databaseManager = new sfDatabaseManager($this->dbParams($db_params));
    $buildSql = new Doctrine_Task_GenerateSql();
    $dropDb = new Doctrine_Task_DropDb();

    $dropDb->setArguments(array('force' => true));
    $buildSql->setArguments(array(
      'models_path' => sfConfig::get('sf_lib_dir') . '/model/doctrine',
      'sql_path' => sfConfig::get('sf_data_dir') . '/sql',
    ));
    $createDb = new Doctrine_Task_CreateDb();
    try {
      if ($dropDb->validate()) {
        $dropDb->execute();
      } else {
        throw new Doctrine_Exception($dropDb->ask());
      }
    } catch (Exception $e) {
      $installed[] = 'Could not drop DB! : ' . "\n" . $e->getMessage();
    }
    try {
      if ($createDb->validate()) {
        $createDb->execute();
      } else {
        throw new Doctrine_Exception($createDb->ask());
      }
      $installed[] = 'Successfully created database';
    } catch (Exception $e) {
      $installed[] = 'Could not create DB! : ' . "\n" . $e->getMessage();
    }
    try {
      if ($buildSql->validate()) {
        $buildSql->execute();
      }
      $installed[] = 'Successfully built SQL';
    } catch (Exception $e) {
      $installed[] = 'Could not build SQL! : ' . "\n" . $e->getMessage();
    }

    try {
      $allmodels = Doctrine_Core::loadModels(
              sfConfig::get('sf_lib_dir') . '/model/doctrine',
              Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
      $installed[] = 'Sucessefully loaded all data models';
    } catch (Exception $e) {
      $installed[] = 'Could not load models! : ' . "\n" . $e->getMessage();
    }
    try {
      Doctrine_Core::createTablesFromArray(Doctrine_Core::getLoadedModels());
      $installed[] = 'Successfully created database tables from models';
    } catch (Exception $e) {

      $installed[] = 'Could not create tables! : ' . "\n" . $e->getMessage();
    }

    try {
      Doctrine_Core::loadData(sfConfig::get('sf_data_dir') . '/fixtures', false);
      //$installed[] = 'Successfully loaded core data fixtures';
      $installed = 'Success!';
    } catch (Exception $e) {
      $installed[] = 'Could not insert SQL! : ' . "\n" . $e->getMessage();
    }
//
//    $packages = agPluginManager::getPackagesByStatus(1); //get all enabled packages
//    foreach($packages as $package)
//    {
//      try {
////        if($package == 'agStaffPackage'){
////          Doctrine_Core::loadModels(sfConfig::get('sf_lib_dir') . '/model/doctrine', Doctrine_Core::MODEL_LOADING_AGGRESSIVE);
////        }
////        else{
//          Doctrine_Core::loadModels(sfConfig::get('sf_app_dir') . '/lib/packages/' . $package . '/lib/model/doctrine', Doctrine_Core::MODEL_LOADING_AGGRESSIVE);
////        }
//        Doctrine_Core::loadData(sfConfig::get('sf_app_dir') . '/lib/packages/' . $package  . '/data/fixtures', true);
//        $installed[] = 'Successfully loaded packaged data fixtures';
//      } catch (Exception $e) {
//        $installed[] = 'Could not insert SQL! : ' . "\n" . $e->getMessage();
//      }
//    }
//    this entry is achieved by proxy of the agHost.yml fixture/example
//
//    try {
//      $ag_host = new agHost();
//      $ag_host->setHostname($this->getConfig('DB_SERVER'));
//      $ag_host->save();
//      //$installed[] = 'Successfully generated host record based on database server host';
//    } catch (Exception $e) {
//      $installed[] = 'Could not insert ag_host record ' . $e->getMessage();
//    }
    try {
      $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'all', false);
      $databaseManager = new sfDatabaseManager($configuration);
//        $connection = Doctrine_Manager::connection()->connect();
      sfContext::createInstance($configuration)->dispatch();

      $luceneIndex = new agLuceneIndex(array('agStaff', 'agFacility', 'agScenario', 'agScenarioFacilityGroup'));
      $luceneIndex->indexAll();
      $installed = 'Success!';
    } catch (Exception $e) {
      $installed[] = 'Could not index Data! : ' . "\n" . $e->getMessage();
    }
    if (is_array($installed)) {
      return implode('<br>', $installed);
    } else {
      return $installed;
    }
  }

  function EventHandler()
  {
    if (isset($_REQUEST['back'][$this->getStep()]))
      $this->DoBack();
//STEP ONE
    if ($this->getStep() == 1) {
      if (!isset($_REQUEST['Next'][0]) && !isset($_REQUEST['back'][2])) {
        $this->setConfig('agree', isset($_REQUEST['agree']));
        //$this->doNext();
      }

      if (isset($_REQUEST['Next'][$this->getStep()]) && $this->getConfig('agree', false)) {
        $this->DoNext();
      }
    }

//STEP TWO
    if ($this->getStep() == 2
        && isset($_REQUEST['Next'][$this->getStep()])
        && !isset($_REQUEST['problem'])) {
      $filename = sfConfig::get('sf_config_dir') . '/databases.yml';
      if (file_exists($filename)) {
        $dbArray = sfYaml::load($filename);
      } else {
        $dbArray = array();
      }
      $this->dbParams($dbArray);
      $this->DoNext();
    }
//STEP THREE
    if ($this->getStep() == 3) {
//on our first pass, these values won't exist (or if someone has returned with no POST
      $current = $this->getCurrent();
      if ($_POST['db_host']) {
        $db_params = array(
          'dsn' => buildDsnString('mysql', $_POST['db_host'], $_POST['db_name']), // ilya 2010-07-21 15:16:58
          //'dsn' => buildDsnString($_POST['db_type'], $_POST['db_host'], $_POST['db_name'], $_POST['db_port']),
          'username' => $_POST['db_user'],
          'password' => $_POST['db_pass']);
        $this->setConfig('DB_SERVER', $_POST['db_host']);
        $this->setConfig('DB_DATABASE', $_POST['db_name']);
        $this->setConfig('DB_USER', $_POST['db_user']);
        $this->setConfig('DB_PASSWORD', $_POST['db_pass']);
        $this->setConfig('ADMIN_NAME', $_POST['admin_name']);
        $this->setConfig('ADMIN_EMAIL', $_POST['admin_email']);
      } else {
        $db_params = $current[0];
        $config_array = $current[1];
      }
      $config_array = array(
        'is_installed' => array('value' => 'true'),
        'sudo' => array(
          'super_user' => $current[1]['sudo']['super_user'],
          'super_pass' => $current[1]['sudo']['super_pass']),
        'admin' => array(
          'admin_name' => $this->getConfig('ADMIN_NAME'),
          'admin_email' => $this->getConfig('ADMIN_EMAIL'),
          'auth_method' => array('value' => 'bypass'),
          'log_level' => array('value' => 'default'),
        //'db_type' => $_POST['db_type'],
        //'db_host' => $_POST['db_host'],
        //'db_name' => $_POST['db_port'], //ilya 2010-07-21 15:17:13
          ));

      if (!($this->agSaveSetup($config_array))) {
        $this->DISABLE_NEXT = true;
        unset($_REQUEST['Next']);
//if we cannot save our configuration
      } else {
        $dbcheck = $this->CheckConnection($db_params);
        if ($dbcheck == 'good') {
          $this->RETRY_SUCCESS = true;
        } else {
//if we cannot establish a db connection
          $this->RETRY_SUCCESS = false;
          $this->DISABLE_NEXT = true;
          $this->ERROR_MESSAGE = $dbcheck;
//set the installer's global error message to the return of our connection attempt
          unset($_REQUEST['Next']);
        }
      }
      if (isset($_REQUEST['Next'][$this->getStep()])) {
//the validation comment below can be handled by:
//$this->getCurrent();
        $this->setConfig('db_config', $db_params);
        $this->setConfig('DB_SERVER', $_POST['db_host']);
        $this->setConfig('DB_DATABASE', $_POST['db_name']);
        $this->setConfig('DB_USER', $_POST['db_user']);
        $this->setConfig('DB_PASSWORD', $_POST['db_pass']);
        $this->setConfig('ADMIN_NAME', $_POST['admin_name']);
        $this->setConfig('ADMIN_EMAIL', $_POST['admin_email']);
        $this->DoNext();
//we should validate here in case someone changes correct information
      }
    }
//STEP FOUR
    if ($this->getStep() == 4) {
//present user with configuration settings, show 'install button'
      if (isset($_REQUEST['Next'][$this->getStep()])) {
        $this->INSTALL_RESULT = $this->doInstall($this->getConfig('db_config'));
        $this->DoNext();
      }
    }
//STEP FINISH
    if (isset($_REQUEST['Finish'])) {       //isset($_REQUEST['Next'][$this->getStep()]
//$this->doNext();
      $sudo = $this->getCurrent();
      $sudoer = $sudo[1]['sudo']['super_user']; //get username and password from config.yml, should be cleaner.
      $supw = $sudo[1]['sudo']['super_pass'];
//authenticate with this
      try {
        $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'all', false);
        $databaseManager = new sfDatabaseManager($configuration);
//        $connection = Doctrine_Manager::connection()->connect();
        sfContext::createInstance($configuration)->dispatch();
        agSudoAuth::authenticate($sudoer, $supw);
        redirect('admin/new');
      } catch (Exception $e) {
        $this->ERROR_MESSAGE = $e->getMessage();
      }
      return;
    }

    if (isset($_REQUEST['Next'][$this->getStep()])) {
      $this->DoNext();
    }
  }

  function agSaveSetup($config)
  {
    /** remember to set the superadmin config.yml up!!!  */
    sfClearCache('frontend', 'all');
    sfClearCache('frontend', 'dev');
    sfClearCache('frontend', 'prod');
    require_once sfConfig::get('sf_lib_dir') . '/vendor/symfony/lib/yaml/sfYaml.php';

    $file = sfConfig::get('sf_config_dir') . '/config.yml';
    touch($file);
// if config.yml does not exist
    try {
      file_put_contents($file, sfYaml::dump($config, 4));
    } catch (Exception $e) {
      echo "hey, something went wrong:" . $e->getMessage();
    }

    $file = sfConfig::get('sf_app_config_dir') . '/app.yml';
    touch($file);
//if app.yml does not exist
    $appYmlResult = writeAppYml();
    $file = sfConfig::get('sf_config_dir') . '/databases.yml';
//the below line will fail if the permissions are not correct, should be wrapped in a try/catch
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
    //once save setup is complete, create entry in ag_host (needed for global params
  }

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
