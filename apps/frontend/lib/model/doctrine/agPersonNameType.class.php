<?php

/** 
* extends the Doctrine-generated BaseagPersonNameType model
*
* Extends the Doctrine-generated BaseagPersonNameType model
* so that we can add our own functionality without modifying the Doctrine-
* generated model
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

class agPersonNameType extends BaseagPersonNameType
{
/**
* __toString returns the string value of the NameType object
* @return String version of getPersonNameType()
*/
  public function __toString()
  {
    return $this->getPersonNameType();
  }
}
