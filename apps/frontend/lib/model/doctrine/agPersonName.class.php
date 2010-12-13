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
* @author Charles Wisniewski, http://sps.cuny.edu
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/

/**
* @todo add description of class in header
*/

class agPersonName extends BaseagPersonName
{
/**
* <description>
* @todo add description of function above and details below
* @return ???
*/
  public function __toString()
  {
    return $this->getPersonName();
  }
}
