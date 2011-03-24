<?php

/** 
* Extends BaseagPersonMjAgNationality and returns ???
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
class agPersonMjAgNationality extends BaseagPersonMjAgNationality
{
/**
* <description>
* @todo add description of function above and details below
* @return ???
*/
 public function __toString()
 {
   return $this->getAgNationality();
 }
}
