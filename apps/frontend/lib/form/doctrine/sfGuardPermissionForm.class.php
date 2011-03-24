<?php
/** 
 * sfGuardUserAdminForm for admin generators
*
* PHP Version 5.3
*
* LICENSE: This source file is subject to LGPLv2.1 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Full Name, Organization
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/

/**
* @todo add description of class in header
*/



class sfGuardPermissionForm extends BasesfGuardPermissionForm
{
/**
* <description>
* @todo add description of function above and details below
* @see sfForm
*/
  public function configure()
  {
    parent::configure();
    unset(
      $this['username'],
      $this['algorithm'],
      $this['groups_list'],
      $this['permissions_list'],
      $this['is_active']
    );
  }
}
