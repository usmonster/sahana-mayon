<?php

/** 
* extends the BaseagPhoneContact
*
* Extends the BaseagPhoneContact to allow us to add additional
* behavior without modifying the Doctrine-generated model
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


class agPhoneContact extends BaseagPhoneContact
{
/**
* __toString returns the string version of the stored Phone Contact value
 *
 * @return string value of getPhoneContact();
*/
  public function __toString()
  {
    return $this->getPhoneContact();
  }
}
