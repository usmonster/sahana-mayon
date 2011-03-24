<?php

/** 
* Agasti Sudo User Class
*
* PHP Version 5.3
*
* LICENSE: This source file is subject to LGPLv2.1 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/licenses/lgpl-2.1.html
*
* @author Charles Wisniewski, http://sps.cuny.edu
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/

class sfGuardValidatorCustomUser Extends sfGuardValidatorUser
{
protected function doClean($values)
  {
 
    $username = isset($values[$this->getOption('username_field')]) ? $values[$this->getOption('username_field')] : '';
    $password = isset($values[$this->getOption('password_field')]) ? $values[$this->getOption('password_field')] : '';
 
    //This is the authentication code that you want to replace it with
    
    if($user = agSudoUser::checkSudoPassword($username, $password)) {
        return array_merge($values, array('user' => $user));
    }
 
    if ($this->getOption('throw_global_error'))
    {
      throw new sfValidatorError($this, 'invalid');
    }
 
    throw new sfValidatorErrorSchema($this,
      array($this->getOption('username_field') =>
            new sfValidatorError($this, 'invalid')));
  }
}
