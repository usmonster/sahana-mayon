<?php

/** 
* Extends BaseagEthnicity to return ethnicity field for person records.
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


class agEthnicity extends BaseagEthnicity
{

/**
* Returns as string value as default instead of an ID.
*
* @return the string value of the ethnicity field from the ag_ethnicity table. __toString is used to get that value instead of the ID.
* Used to fill lists autopopulated from tables by Symfony.
*/
  public function __toString()
  {
    return $this->getEthnicity();
  }
}
