<?php

/**
 * Returns deployment algorithm
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
class agDeploymentAlgorithm extends BaseagDeploymentAlgorithm
{

  /**
   *
   * @return a string representation of the deployment algorithm.
   */
  public function __toString()
  {
    return $this->getDeploymentAlgorithm();
  }

}
