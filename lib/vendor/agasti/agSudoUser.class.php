<?php
/** 
* Agasti Sudo User Class
*
* PHP Version 5
*
* LICENSE: This source file is subject to LGPLv3.0 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @package Agasti Core
* @author Charles Wisniewski, http://sps.cuny.edu
* @copyright Sahana Software Foundation, sahanafoundation.org
*/

class agSudoUser extends sfGuardSecurityUser {
  //TODO: log all activity of SUDO user to database

    public static function checkSudoPassword($username, $password)
    {
      echo "test";
      require_once dirname(__FILE__).'/../../../lib/vendor/symfony/lib/yaml/sfYaml.php';
      $cfgArray = sfYaml::load( dirname(__FILE__).'/../../../config/config.yml');

      $validUser = false;
      // Check user existance    

      if ($cfgArray['all']['param']['super_user'] == $username && $cfgArray['all']['param']['super_pass'] == $password){
        throw new sfException(sprintf('big success.'));
	$validUser == true;
        $_SESSION['validUser'] = true; //replace this with actual symphony valid user session variable
        return true;
      }
      else
      {
        $_SESSION['validUser'] = false;
        throw new sfException(sprintf('The username/password combination did not match'));
        return false;
      }
    }
}

