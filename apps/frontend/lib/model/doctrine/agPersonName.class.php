<?php

/**
 * Agasti Sudo User Class
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

/**
 * @todo add description of class in header
 */
class agPersonName extends BaseagPersonName
{

  public $luceneSearchFields = array
    (
    'person_name' => 'unstored'
  );

  /**
   * <description>
   * @todo add description of function above and details below
   * @return 
   */
  public function __toString()
  {
    return $this->getPersonName();
  }

}
