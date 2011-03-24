<?php

/** 
* overrides the BaseagReligion class
*
* PHP Version 5.3
*
* LICENSE: This source file is subject to LGPLv2.1 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Charles Wisniewski, CUNY SPS
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/

class agReligion extends BaseagReligion
{
/**
* @return the object's string representation
* the base __toString method is overriden
* so the ORM knows exactly what to return when asked form
*/
  public function __toString()
  {
    return $this->getReligion();
  }
}
