<?php
/**
 * Agasti Administrator Configurationinstall.php, this file should be used only upon installation of Agasti
 * The purpose of the file is to automate the distrolike creation of the Agasti Application
 * The installer takes the input of users to customize the install of Agasti
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the worldwideweb at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
require_once (sfConfig::get('sf_app_module_dir') . '/admin/lib/config.inc.php');

?>
<div class="adminConfig">
  <h2>System Configuration</h2>
  <p>This page will allow you to configure your Agasti installation.</p>
  <p>Use the form to the top left to enter your system information.
  The information to the right provides you with a system configuration check
  </p>
<div id="contentLeft">
  <?php
  //get existing configuration
  $agConfig = agConfig::singleton();
  $agConfig->getCurrent();
 
  ?>
  
<form action="<?php echo url_for('admin/config') ?>" method="post" class="configure adminConfigLegend">
    <fieldset>
      <legend><img src="<?php echo url_for('images/database.png') ?>" class="textBottom" alt="database icon" />Database Configuration:</legend>
      <p>
        <?php ?>
      </p>
      <ul>
        <li>
          <label>host:</label><input disabled="disabled" type="text" name="db_host" id="db_host" class="inputGray" value="<?php echo $agConfig->getConfig('DB_SERVER', 'dont') ?>" />
        </li>
        <li>
          <label>database:</label><input disabled="disabled" type="text" name="db_name" id="db_name" class="inputGray" value="<?php echo $agConfig->getConfig('DB_DATABASE', 'meddle') ?>" />
        </li>
        <li>
          <label>username:</label><input disabled="disabled" type="text" name="db_user" id="db_user" class="inputGray" value="<?php echo $agConfig->getConfig('DB_USER', 'with')  ?>" />
        </li>
<!--        <li>
          <label>password:</label><input disabled="disabled" type="password" name="db_pass" id="db_pass" class="inputGray" value="<?php // echo $agConfig->getConfig('DB_PASSWORD', 'sysfiles')  ?>" />
        </li>
        <li>
          <input disabled="disabled" id="init_schema" type="checkbox" name="init_schema" />re-initialize database schema
        </li>
        <li><span class="warningColor">WARNING: this will drop your current database.</span></li>!-->
      </ul>
    </fieldset>
    <fieldset>
      <legend><?php echo image_tag('config.png', array('class' => 'textBottom', 'alt' => 'config gear icon')) ?>Select Authentication Method:</legend>
      <ul>
        <li>
          <input id="auth_method1" type="radio" name="auth_method" value="default"<?php if ($agConfig->getConfig('AUTH_METHOD')  == 'default')
          echo ' checked="checked"'; ?> /><label for="auth_method1">default security</label><br />
          <input id="auth_method2" type="radio" name="auth_method" value="bypass"<?php if ($agConfig->getConfig('AUTH_METHOD') == 'bypass')
                   echo ' checked="checked"'; ?> /><label for="auth_method1">bypass/superadmin</label><br />
        </li>
      </ul>
    </fieldset>
    <fieldset>
      <legend><?php echo image_tag('config.png', array('class' => 'textBottom', 'alt' => 'config gear icon')) ?>Administrator Configuration:</legend>
      <ul>
        <li>
          <label>name:</label><input type="text" name="admin_name" id="admin_name" class="inputGray" value="<?php echo $agConfig->getConfig('ADMIN_NAME') ?>" /><br />
        </li>
        <li>
          <label>email:</label><input type="text" name="admin_email" id="admin_email" class="inputGray" value="<?php echo $agConfig->getConfig('ADMIN_EMAIL') ?>" /><br />
        </li>
        <li
      </ul>
    </fieldset>
    <ul>
      <li class="textRight">
        <input type="hidden" name="_enter_check" value="1" />
        <input type="hidden" name="_sql_check" value="<?php echo $install_flag; ?>" />
        <input type="submit" name="saveconfig" value="save config" class="saveLinkButton" onclick="submit.disabled=true;" />
        <?php
//if the right information was passed
//process and install, with the schema file(s) needed.
        ?>
      </li>
    </ul>
  </form>
</div>
<div id="columnRight">
<form action="<?php echo url_for('admin/config') ?>" method="post" class="configure adminConfigForm">
  <fieldset>
      <legend>Configuration Test</legend>
<?php $table = '<table class="adminConfigRequirements"><tr><td>&nbsp;</td><td>Option</td><td>Current Value</td><td>Required</td><td>Recommended</td><td>&nbsp;</td></tr>';
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

      $current = '<tr><td>&nbsp;</td><td><strong>' . $req['name'] . '</strong></td>' . '<td>' . $req['current'] . '</td>';
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
      $final_result = '<span class="fail">Please correct all issues and press the "retry" button</span><br /><br />' . $retry;
    } else {
      $this->DISABLE_NEXT = false;
      $final_result = '<span class="green">Ok</span>';
    }

    echo $table . '</table><br />' . $final_result;
    ?>
  </fieldset>
</form>
</div>
</div>