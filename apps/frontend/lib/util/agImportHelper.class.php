<?php
/**
 * Abstract class to provide import helper methods
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUNY SPS
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
abstract class agImportHelper
{
  protected   $importTable,
              $conn;

  public function __construct()
  {
    // Sets a new connection.
    $this->setConnection();
  }

  protected function getConnection()
  {
    // Lazy load and return pdo connection.
    if (!isset($this->conn)) { $this->setConnection(); }
    return $this->conn;
  }

  protected function setConnection()
  {
    // Explicit setter.
    $this->conn = Doctrine_Manager::connection();
    // @TODO Don't use default connection.  Instead use a new connection.
  }

}