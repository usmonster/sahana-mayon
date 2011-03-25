<?php

/**
* Extends BaseagEmailContact to return email_contact field for person records.
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


class agLanguageCompetency extends BaseagLanguageCompetency
{

/**
* Returns as string value as default instead of an ID.
*
* @return the string value of the LanguageCompetency()
*/
  public function __toString()
  {
    return $this->getLanguageCompetency();
  }
}
