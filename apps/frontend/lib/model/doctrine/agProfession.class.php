<?php

/** 
* Extends baseAgProfession
*
* agProfession extends the BaseagProfession class so that we can add additional
* functionality without modifying the Doctrine-generated model
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


class agProfession extends BaseagProfession
{
/**
* __toString returns the string value of the agProfession object's name value
* @return string version of getProfession()
*/
  public function __toString()
  {
    return $this->getProfession();
  }
}
