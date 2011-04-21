<?php
/**
 * Sahana Agasti install.php, this file should be used only upon installation of Sahana Agasti
 * The purpose of the file is to automate the distro-like creation of the Agasti Application
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
require_once(dirname(__FILE__) . '/install.inc.php');


global $AG_CONFIG;

if (isset($_REQUEST['cancel']) || isset($_REQUEST['finish'])) {
  //redirect('admin/new');
  ag_unsetcookie('AG_CONFIG');
}
//ag_flush_post_cookies();
$AG_CONFIG = get_cookie('AG_CONFIG', null);
if (isset($AG_CONFIG)) {
  $AG_CONFIG = unserialize(stripcslashes($AG_CONFIG));
} else {
  $AG_CONFIG = array();
}
if (!isset($AG_CONFIG['step']))
  $AG_CONFIG['step'] = 0;
if (!isset($AG_CONFIG['agree']))
  $AG_CONFIG['agree'] = false;

global $AG_INSTALL;

$AG_INSTALL = new agInstall($AG_CONFIG);

ag_set_post_cookie('AG_CONFIG', serialize($AG_CONFIG));

ag_flush_post_cookies();

unset($_POST);
?>
<!--?xml version="1.0" encoding="utf-8"?-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sahana Agasti 2.0 Installer</title>
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/agMain.css" />
  </head>
  <body>
    <div id="header">
      <h1>
        Sahana Agasti 2.0 Installer
      </h1>
    </div>
    <div id="wrapper">
      <div id="columns">
        <h3><?php echo $AG_INSTALL->steps[$AG_INSTALL->getStep()]['title']; ?></h3>
        <div id="columnLeft">
<?php echo $AG_INSTALL->getList(); ?>
        </div>
        <div id="columnRight">
          <form action="install.php" method="post" class="configure" style="margin-right: 80px; float: left;">
          <?php echo $AG_INSTALL->getState(); ?>

            <ul>
              <li style="text-align: right">
                <input type="hidden" name="_enter_check" value="1" />
                <input type="hidden" name="_sql_check" value="<?php echo $install_flag; ?>" />

                <input id="back[<?php echo $AG_INSTALL->getStep(); ?>]" name="back[<?php echo $AG_INSTALL->getStep(); ?>]" type="submit" value="<< previous"<?php //echo $AG_INSTALL->; ?> class="linkButton" />
                <input type="submit" value="cancel" class="linkButton" id="cancel" name="cancel" />
<?php
          if (!isset($AG_INSTALL->steps[$AG_INSTALL->getStep() + 1])) {
            //checking to see if there is anything left in the ag_install stage
            $dolab = 'finish';
            $doval = 'finish';
          } else {
            $dolab = "next[" . $AG_INSTALL->getStep() . "]";
            $doval = "next >>";
          } ?>
                <input id="next[<?php echo $dolab; ?>]" name="<?php echo $dolab ?>" type="submit" value="<?php echo $doval ?>" class="linkButton" <?php if ($AG_INSTALL->DISABLE_NEXT)
                  echo " disabled=true"; ?> />
              </li>
            </ul>
          </form>
        </div>
      </div>
    </div>
    <div id="footer">
      <img src="images/nyc_logo.png" alt="NYC Office of Emergency Management Logo" />
    </div>
  </body>
</html>
