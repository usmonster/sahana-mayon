<?php

/** 
* Extends BaseagCountry for person, returns country field 
*
* PHP Version 5.3
*
* LICENSE: This source file is subject to LGPLv2.1 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Charles Wisniewski, http://sps.cuny.edu
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/


class agCountry extends BaseagCountry
{

/**
* Returns as string value as default instead of an ID.
*
* @return the string value of the country field from the ag_country table. __toString is used to get that value instead of the ID.
* Used to fill lists autopopulated from tables by Symfony.
*/
  public function __toString()
  {
    return $this->getCountry();
  }
}
