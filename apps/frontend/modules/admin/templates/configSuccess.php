<?php
/**
 * Agasti install.php, this file should be used only upon installation of Agasti
 * The purpose of the file is to automate the distrolike creation of the Agasti Application
 * The installer takes the input of users to customize the install of Agasti
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the worldwideweb at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

/**
 *  parses a DSN string into its elements and returns them as an array
 *
 * @param $dsnString DSN string to parse
 * @return array(dbtype, host, port, dbname)
 */
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

    //print_r('.'.$dsnString);

    return $dsnString;
  }

  return null;
}

require_once(sfConfig::get('sf_config_dir') . '/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);

/**
 * readInstallConfig reads from the configuration files in the config/ directory and
 * returns all of the settings as a multidimensional array.
 *
 * @return array Current configuration as a multidimensional array
 */
function readInstallConfig()
{
  $configArray = array();

  if (file_exists(sfConfig::get('sf_config_dir') . '/databases.yml') == TRUE) {
    $dbArray = sfYaml::load(sfConfig::get('sf_config_dir') . '/databases.yml');
    $adsn = parseDsn($dbArray['all']['doctrine']['param']['dsn']);

    $configArray['dbUser'] = $dbArray['all']['doctrine']['param']['username'];
    $configArray['dbPass'] = $dbArray['all']['doctrine']['param']['password'];
    $configArray['dbHost'] = $adsn['host'];
    $configArray['dbName'] = $adsn['dbname'];

    if ($configArray['dbUser'] && $configArray['dbHost'] && $configArray['dbName']) {
      $configArray['dbConfigValid'] = true;
    }
  } else {
    $install_flag = true;
  }

  if (file_exists(sfConfig::get('sf_config_dir') . '/config.yml') == TRUE) {
    $cfgArray = sfYaml::load(sfConfig::get('sf_config_dir') . '/config/config.yml');

    $configArray['saUser'] = $cfgArray['sudo']['super_user'];
    $configArray['saPass'] = $cfgArray['sudo']['super_pass'];
    $configArray['authMethod'] = $cfgArray['auth_method']['value'];
    $configArray['admin_user'] = $cfgArray['admin']['admin_user'];
    $configArray['admin_pass'] = $cfgArray['admin']['admin_pass'];
    $configArray['admin_name'] = $cfgArray['admin']['admin_name'];
    $configArray['admin_email'] = $cfgArray['admin']['admin_email'];

    if ($configArray['saUser'] && $configArray['saHost'] && $configArray['authMethod']) {
      $configArray['authConfigValid'] = true;
    }

    $configArray['logLevel'] = $cfgArray['log_level']['value'];
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

if (array_key_exists('_enter_check', $_POST)) {

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

  function agSaveSetup($config, $config_sec)
  {
    sfClearCache('frontend', 'all');
    sfClearCache('frontend', 'dev');
    sfClearCache('frontend', 'prod');
    require_once (sfConfig::get('sf_lib_dir') . '/vendor/symfony/lib/yaml/sfYaml.php');
    $file = sfConfig::get('sf_config_dir') . '/config.yml';
    // update config.yml
    try {
      file_put_contents($file, sfYaml::dump($config, 4));
    } catch (Exception $e) {
      echo "hey, something went wrong:" . $e->getMessage();
    }
    $file = dirname(__FILE__) . '/../../../config/app.yml';
    try {
      file_put_contents($file, sfYaml::dump($config_sec, 4));
    } catch (Exception $e) {
      echo "hey, something went wrong:" . $e->getMessage();
    }
    $file = sfConfig::get('sf_config_dir') . '/databases.yml';
    $dbConfiguration = sfYaml::load(sfConfig::get('sf_config_dir') . '/../config/databases.yml');
    $dbConfiguration['all']['doctrine']['param']['attributes'] = array(
      'default_table_type' => 'INNODB',
      'default_table_charset' => 'utf8',
      'default_table_collate' => 'utf8_general_ci'
    );
    try {
      file_put_contents($file, sfYaml::dump($dbConfiguration, 4));
    } catch (Exception $e) {
      echo "hey, something went wrong:" . $e->getMessage();
    }
  }

  function sfClearCache($app, $env)
  {
    $cacheDir = sfConfig::get('sf_cache_dir') . '/' . $app . '/' . $env . '/';
    /**
     * in order to clear the cache, we have to actually specify what to actually
     * clear
     */
    //Clear cache
    $cache = new sfFileCache(array('cache_dir' => $cacheDir));
    $cache->clean();
  }

  $arguments = array(
    'task' => 'configure:database',
    //'dsn' => buildDsnString('mysql', $_POST['db_host'], $_POST['db_name'], $_POST['db_port']),
    'dsn' => buildDsnString('mysql', $_POST['db_host'], $_POST['db_name']), // ilya 2010-07-21 15:16:58
    //'dsn' => buildDsnString($_POST['db_type'], $_POST['db_host'], $_POST['db_name'], $_POST['db_port']),
    'username' => $_POST['db_user'],
    'password' => $_POST['db_pass'],
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
  $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);

  switch ($_POST['auth_method']) {
    case 'default':
      $authMethod = 'default';
      break;
    case 'bypass':
      $authMethod = 'bypass';
      break;
    default:
      $authMethod = 'default';
  }

//also save super admin user/password, etc.
  $config_array = array(
    'is_installed' => array('value' => 'true'),
    'sudo' => array(
      'super_user' => $_POST['sa_user'],
      'super_pass' => $_POST['sa_pass']),
    'admin' => array(
      'admin_name' => $_POST['admin_name'],
      'admin_email' => $_POST['admin_email'],
      'admin_user' => $_POST['admin_user'],
      'admin_pass' => $_POST['admin_pass']),
    'auth_method' => array('value' => $authMethod),
    'log_level' => array('value' => 'debug'),
      //'log_level' => array('value' => $_POST['log_level']),
      //'db_type' => $_POST['db_type'],
      //'db_host' => $_POST['db_host'],
      //'db_name' => $_POST['db_port'], //ilya 2010-07-21 15:17:13
  );
  if ($authMethod == "bypass") {
    $security_array = array('all' =>
      array('sf_guard_plugin' =>
        array('check_password_callable' =>
          array('agSudoAuth', 'authenticate')),
        'sf_guard_plugin_signin_form' => 'agSudoSigninForm')
    );
  } else {
    $security_array = array('all' => array());
  }

  agSaveSetup($config_array, $security_array);

  if ($_POST['init_schema']) {
    $databaseManager = new sfDatabaseManager($configuration);
    $buildSql = new Doctrine_Task_GenerateSql();
    $dropDb = new Doctrine_Task_DropDb();
    $dropDb->setArguments(array('force'));
    $createDb = new Doctrine_Task_CreateDb();
    $buildSql->setArguments(array(
      'models_path' => sfConfig::get('sf_lib_dir') . '/model/doctrine',
      'sql_path' => sfConfig::get('sf_data_dir') . '/sql',
    ));
    try {
      if ($dropDb->validate()) {
        $dropDb->execute();
      } else {
        throw new Doctrine_Exception($dropDb->ask());
      }
    } catch (Exception $e) {
      throw new Doctrine_Exception($e->getMessage());
    }
    try {
      if ($createDb->validate()) {
        $createDb->execute();
      } else {
        throw new Doctrine_Exception($createDb->ask());
      }
    } catch (Exception $e) {
      echo "hey, something went wrong:" . $e->getMessage();
    }
    try {
      if ($buildSql->validate()) {
        $buildSql->execute();
      }
    } catch (Exception $e) {
      echo "hey, something went wrong:" . $e->getMessage();
    }
    Doctrine_Core::loadModels(sfConfig::get('sf_lib_dir') . '/model/doctrine', Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
    try {
      Doctrine_Core::createTablesFromArray(Doctrine_Core::getLoadedModels());
    } catch (Exception $e) {
      echo "hey, something went wrong:" . $e->getMessage();
    }

    $insertSql = new Doctrine_Task_LoadData();
    $insertSql->setArguments(array(
      'data_fixtures_path' => sfConfig::get('sf_data_dir') . '/fixtures',
      'models_path' => sfConfig::get('sf_lib_dir') . '/model/doctrine',
      'append' => false,
    ));
    try {
      if ($insertSql->validate()) {
        $insertSql->execute();
      } else {
        throw new Doctrine_Exception($insertSql->ask());
      }
    } catch (Exception $e) {
      echo "hey, something went wrong:" . $e->getMessage();
    }
  }
  /**
   * at this point, all of the data has been inserted(fixtures, tables created, etc)
   * we need to now just create our regular admin user
   * default info from sfGuard.yml fixture:
   *  sfGuardUser:
   *    sgu_admin:
   *      username:       admin
   *      password:       admin
   *      is_super_admin: true
   *
   * this section is currently commented out until conflicts are resolved with
   * the admin/superadmin user(s)
   *    $user = new sfGuardUser();
   * $user->setUsername($_POST['admin_user']);
   * $user->setPassword($_POST['admin_pass']);
   * $user->setIsSuperAdmin(TRUE);
   */
  header("Location: index.php");
}

require_once (sfConfig::get('sf_lib_dir') . '/vendor/symfony/lib/yaml/sfYaml.php');
if (isset($_POST['act']))
  $act = $_POST['act'];

if (file_exists(sfConfig::get('sf_config_dir') . '/databases.yml') == TRUE) {
  $dbArray = sfYaml::load(sfConfig::get('sf_config_dir') . '/databases.yml');
  //print_r("...{".$dbArray['all']['doctrine']['param']['dsn']."}...");
  $adsn = parseDsn($dbArray['all']['doctrine']['param']['dsn']);
  //print_r($adsn);
  $existing_db_user = $dbArray['all']['doctrine']['param']['username'];
  $existing_db_pass = $dbArray['all']['doctrine']['param']['password'];
  $existing_db_host = $adsn['host'];
  //$existing_db_port = $adsn['port']; //ilya 2010-07-21 15:11:28
  $existing_db_name = $adsn['dbname'];
  $install_flag = false;
} else {
  $install_flag = true;
}
if (file_exists(sfConfig::get('sf_config_dir') . '/config.yml') == TRUE) {
  $cfgArray = sfYaml::load(sfConfig::get('sf_config_dir') . '/config.yml');
  $existing_sa_user = $cfgArray['sudo']['super_user'];
  $existing_sa_pass = $cfgArray['sudo']['super_pass'];
  $existing_admin_user = $cfgArray['admin']['admin_user'];
  $existing_admin_pass = $cfgArray['admin']['admin_pass'];
  $existing_admin_name = $cfgArray['admin']['admin_name'];
  $existing_admin_email = $cfgArray['admin']['admin_email'];
  $existing_auth_method = $cfgArray['auth_method']['value'];
  $act = 'installed';
} else {
  $install_flag = true;
}
switch ($act) {
  case 'config':
    $stat = "Configuring settings for Agasti complete.";
    break;

  case 'installed':
    $stat = "Agasti is already installed, this will override your current settings.";
    break;

  default:
    $stat = "Welcome to the Agasti Installer<br />";
}
?>
<h1>
  <?php echo $stat; ?>
</h1>
<div id="columns">
  <form action="<?php echo url_for('admin/config') ?>" method="post" class="configure" style="margin-right: 40px; float: left;">
    <h3>Configuration Options</h3>
    <fieldset>
      <legend><img src="<?php echo url_for('images/database.png') ?>" style="vertical-align: text-bottom" alt="database icon" />Database Configuration:</legend>
      <p>
        <?php ?>
      </p>
      <ul>
        <li>
          <label>host:</label><input type="text" name="db_host" id="db_host" class="inputGray" value="<?php echo $existing_db_host; ?>" />
        </li>
        <li>
          <label>database:</label><input type="text" name="db_name" id="db_name" class="inputGray" value="<?php echo $existing_db_name; ?>" />
        </li>
        <li>
          <label>username:</label><input type="text" name="db_user" id="db_user" class="inputGray" value="<?php echo $existing_db_user; ?>" />
        </li>
        <li>
          <label>password:</label><input type="password" name="db_pass" id="db_pass" class="inputGray" value="<?php echo $existing_db_pass; ?>" />
        </li>
      </ul>
    </fieldset>
    <fieldset>
      <legend><img src="<?php echo url_for('images/config.png') ?>" style="vertical-align: text-bottom"alt="config gear icon" />Select Authentication Method:</legend>

      <ul>
        <li>
          <input id="auth_method1" type="radio" name="auth_method" value="default"<?php if ($existing_auth_method == 'default')
          echo ' checked="checked"'; ?> /><label for="auth_method1">default security</label><br />
          <input id="auth_method2" type="radio" name="auth_method" value="bypass"<?php if ($existing_auth_method == 'bypass')
                   echo ' checked="checked"'; ?> /><label for="auth_method1">bypass/superadmin</label><br />
        </li>
      </ul>
    </fieldset>
    <fieldset>
      <legend><img src="<?php echo url_for('images/config.png') ?>" style="vertical-align: text-bottom" alt="config gear icon" />Administrator Configuration:</legend>
      <ul>
        <li>
          <label>name:</label><input type="text" name="admin_name" id="admin_name" class="inputGray" value="<?php echo $existing_admin_name; ?>" /><br />
        </li>
        <li>
          <label>email:</label><input type="text" name="admin_email" id="admin_email" class="inputGray" value="<?php echo $existing_admin_email; ?>" /><br />
        </li>
        <li>
          <label>username:</label><input type="text" name="admin_user" id="admin_user" class="inputGray" value="<?php echo $existing_admin_user; ?>" /><br />
        </li>
        <li>
          <label>password:</label><input type="password" name="admin_pass" id="admin_pass" class="inputGray" value="<?php echo $existing_admin_pass; ?>" /><br />
        </li>

      </ul>
    </fieldset>
    <fieldset>
      <legend><img src="<?php echo url_for('images/config.png') ?>" style="vertical-align: text-bottom" alt="config gear icon" />Super Admin Configuration:</legend>
      <ul>
        <li>
          <label>username:</label><input type="text" name="sa_user" id="sa_user" class="inputGray" value="<?php echo $existing_sa_user; ?>" /><br />
        </li>
        <li>
          <label>password:</label><input type="password" name="sa_pass" id="sa_pass" class="inputGray" value="<?php echo $existing_sa_pass; ?>" /><br />
        </li>
      </ul>
    </fieldset>
    <fieldset>
      <legend><img src="<?php echo url_for('images/config.png') ?>" style="vertical-align: text-bottom" alt="config gear icon" />Select Logging Scheme:</legend>
      <ul>
        <li>
          <input type="radio" name="log_level" value="default"<?php //if ($cfgArray['auth_method'] == 'sfDoctrineGuard') echo " checked";     ?> />default<br />
          <input type="radio" name="log_level" value="bypass"<?php //if ($cfgArray['auth_method'] == 'bypass') echo " checked";     ?> />special logging<br />
        </li>
      </ul>
    </fieldset>

    <fieldset>
      <legend><img src="<?php echo url_for('images/config.png') ?>" style="vertical-align: text-bottom" alt="config gear icon" />Select Modules to Install:</legend>
      <ul>
        <li><br />
          <select multiple="multiple" size="4" name="moduleselect"><!-- sfGuard -->
            <option value="staff">staff module 2.0</option>
            <option value="facility">facility module 2.0</option>
          </select>
        </li>
      </ul>
    </fieldset>
    <fieldset>
      <legend><img src="<?php echo url_for('images/config.png') ?>" style="vertical-align: text-bottom" alt="config gear icon" />Schema Initialization:</legend>
      <ul>
        <li>
          <input id="init_schema" type="checkbox" name="init_schema" />re-initialize database schema
        </li>
        <li><span style="color:#ff0000;">WARNING: this will drop your current database.</span></li>
      </ul>
    </fieldset>
    <ul>
      <li style="text-align: right">
        <input type="hidden" name="_enter_check" value="1" />
        <input type="hidden" name="_sql_check" value="<?php echo $install_flag; ?>" />
        <input type="submit" value="install" class="linkButton" onclick="submit.disabled=true;" />
        <?php
//if the right information was passed
//process and install, with the schema file(s) needed.
        ?>
      </li>
    </ul>
  </form>
  <h2 style="color: #848484">Instructions</h2>
  <p style="color: #848484">This page will allow you to configure your Agasti installation.</p>
  <p style="color: #848484">Use the form to the left to enter your database information, choose
    your super administrator username and password, and select the modules you would like installed
    along with the Agasti core.</p>
</div>
