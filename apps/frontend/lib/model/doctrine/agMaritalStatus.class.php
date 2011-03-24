<?php

/** 
* Extends BaseagMaritalStatus to return marial status for person records.
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

class agMaritalStatus extends BaseagMaritalStatus
{

/**
* Returns as string value as default instead of an ID.
*
* @return the string value of the marital_status field from the ag_marital_status table. __toString is used to get that value instead of the ID.
* Used to fill lists autopopulated from tables by Symfony.
*/
  public function __toString()
  {
    return $this->getMaritalStatus();
  }
}
