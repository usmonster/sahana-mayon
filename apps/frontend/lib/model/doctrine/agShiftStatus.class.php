<?php

/**
 * Returns shift status
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */

class agShiftStatus extends BaseagShiftStatus
{
  /**
   *
   * @return a string representation of the shift status.
   */
  public function __toString()
  {
    return $this->getShiftStatus();
  }
}
